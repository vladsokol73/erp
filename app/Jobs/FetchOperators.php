<?php

namespace App\Jobs;

use App\Models\ApiToken;
use App\Models\Log as AppLog;
use App\Models\User\User;
use App\Providers\GuzzleClientProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Psr\Http\Message\ResponseInterface;

class FetchOperators implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AppLog::info('FetchOperators: start');
        $client = GuzzleClientProvider::getInstance();

        $allFetchedEmails = [];
        $totalCreated = 0;
        $totalUpdated = 0;

        $tokens = ApiToken::query()->chat2desk()->get();
        AppLog::info('FetchOperators: tokens found = ' . $tokens->count());

        foreach ($tokens as $tokenModel) {
            try {
                $authHeader = ['Authorization' => $tokenModel->token];

                $roles = ['operator', 'supervisor'];
                $fetchedUsers = [];
                foreach ($roles as $role) {
                    $response = $client->request('GET', 'https://api.chat2desk.com/v1/operators/', [
                        'headers' => $authHeader,
                        'json' => [
                            'role' => $role,
                            'limit' => 200,
                        ],
                        'http_errors' => false,
                        'timeout' => 20,
                    ]);

                    $status = $response->getStatusCode();
                    if ($status >= 400) {
                        AppLog::error('FetchOperators: API error status=' . $status . ' role=' . $role . ' token_id=' . $tokenModel->id);
                        continue;
                    }

                    $data = $this->decodeResponse($response);
                    $list = Arr::get($data, 'data', []);
                    AppLog::info('FetchOperators: fetched ' . count($list) . ' users for role=' . $role . ' token_id=' . $tokenModel->id);
                    foreach ($list as $userData) {
                        $fetchedUsers[] = $userData;
                    }
                }

                $createdCount = 0;
                $updatedCount = 0;
                foreach ($fetchedUsers as $remote) {
                    $email = trim((string)($remote['email'] ?? ''));
                    if ($email === '') {
                        AppLog::info('FetchOperators: skipped user without email, token_id=' . $tokenModel->id);
                        continue;
                    }

                    $allFetchedEmails[] = $email;

                    $firstName = (string)($remote['first_name'] ?? '');
                    $lastName = (string)($remote['last_name'] ?? '');
                    $fullName = trim($firstName . ' ' . $lastName);
                    $operatorId = (int)($remote['id'] ?? 0);

                    // upsert by email
                    $payload = [
                        'name' => $fullName !== '' ? $fullName : $email,
                        'password' => Hash::make($email),
                        'available_channels' => [],
                        'available_countries' => [],
                        'available_operators' => [],
                        'available_tags' => [],
                        'operator_id' => $operatorId,
                    ];

                    $existing = User::withTrashed()->where('email', $email)->first();
                    if ($existing) {
                        $existing->restore();
                        $existing->fill($payload);
                        $existing->save();
                        // ensure pivot has token
                        $existing->apiTokens()->syncWithoutDetaching([$tokenModel->id]);
                        $existing->roles()->attach(4);
                        $updatedCount++;
                    } else {
                        $created = User::create(array_merge(['email' => $email], $payload));
                        $created->apiTokens()->attach([$tokenModel->id]);
                        $created->addFlag('must_change_password');
                        $created->roles()->attach(4);
                        $createdCount++;
                    }
                }

                $totalCreated += $createdCount;
                $totalUpdated += $updatedCount;
                AppLog::info('FetchOperators: token_id=' . $tokenModel->id . ' created=' . $createdCount . ' updated=' . $updatedCount);
            } catch (\Throwable $e) {
                AppLog::error('FetchOperators: exception token_id=' . $tokenModel->id . ' msg=' . $e->getMessage());
            }
        }

        $this->cleanupMissingOperators($allFetchedEmails);
        AppLog::info('FetchOperators: done total_created=' . $totalCreated . ' total_updated=' . $totalUpdated);
    }

    private function decodeResponse(ResponseInterface $response): array
    {
        $status = $response->getStatusCode();
        if ($status >= 400) {
            return [];
        }
        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        return is_array($data) ? $data : [];
    }

    private function cleanupMissingOperators(array $emails): void
    {
        $emails = array_values(array_unique(array_filter($emails)));
        if (empty($emails)) {
            return;
        }
        User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('title', ['operator', 'supervisor']);
            })
            ->whereNotIn('email', $emails)
            ->delete();
    }
}
