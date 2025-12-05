<?php

namespace App\Jobs;

use App\Models\ApiToken;
use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use App\Providers\GuzzleClientProvider;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Log;

class Chat2DeskFreshNames implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $apiUrls = [
        'operators' => 'https://api.chat2desk.com/v1/operators',
        'channels' => 'https://api.chat2desk.com/v1/channels',
    ];

    public function __construct()
    {
    }

    public function getData($url, $token, $offset = null): array
    {
        //Getting Guzzle client singleton
        $client = GuzzleClientProvider::getInstance();
        $opts = [
            'headers' => [
                'Authorization' => $token->token,
            ],
            'verify' => false
        ];

        $url .= '?limit=200&offset='.$offset;

        try {
            $response = $client->request('GET', $url, array_merge($opts, [
                'timeout' => 10,
                'connect_timeout' => 5,
            ]));
        } catch (GuzzleException $exception) {
            Log::error('Cant get info from ' . $url . ' : ' .  "\r\n" .
                "TokenID: {$token->id} , token: ". $token->token . "\r\n" .
                $exception->getMessage());
            return ['data' => [], 'meta' => null];
        }

        $answer = $response->getBody();

        try {
            $jsonResponse = json_decode($answer, 1);
            return [
                'data' => $jsonResponse['data'] ?? [],
                'meta' => $jsonResponse['meta'] ?? null
            ];
        } catch (\Exception $exception) {
            Log::error('Cant decode json from ' . $url . ' : ' . "\r\n" .
                "TokenID: {$token->id} , token: ". $token->token . "\r\n" .
                "JSON: " . $answer . "\r\n" .
                $exception->getMessage());
            return ['data' => [], 'meta' => null];
        }
    }

    public function handle(): void
    {
        //Getting all Chat2Desk tokens
        $tokens = ApiToken::where('service', '=', 'Chat2Desk')->get();

        //For each token getting operators and channels info
        foreach ($tokens as $token) {
            //Getting fresh operators names
            $offsetOperator = 0;
            $total = null;
            $maxIterationsOperator = 10;
            $iterationsOperator = 0;

            do {
                $response = $this->getData($this->apiUrls['operators'], $token, $offsetOperator);
                $data = $response['data'];

                // Получаем total только один раз в начале
                if ($total === null && isset($response['meta']['total'])) {
                    $total = $response['meta']['total'];
                }

                //For each operator setting gotten name
                foreach ($data as $operatorData) {
                    $operator = Operator::where('operator_id', $operatorData['id'])->first();
                    if ($operator) {
                        $operator->name = $operatorData['first_name'] . ' ' . $operatorData['last_name'];
                        $operator->save();
                    }
                }

                $offsetOperator += 200;

                $iterationsOperator++;
                if ($iterationsOperator > $maxIterationsOperator) {
                    Log::info("Too many iterations for operators. Token ID: {$token->id}");
                    break;
                }

            } while ($total === null || $offsetOperator < $total);

            $offsetChannel = 0;
            $total = null;
            $maxIterationsChannel = 10;
            $iterationsChannel = 0;

            do {
                //Getting fresh channel names
                $response = $this->getData($this->apiUrls['channels'], $token, $offsetChannel);
                $data = $response['data'];

                // Получаем total только один раз в начале
                if ($total === null && isset($response['meta']['total'])) {
                    $total = $response['meta']['total'];
                }

                //Updating channel names
                foreach ($data as $channelData) {
                    $channel = Channel::where('channel_id', $channelData['id'])->first();
                    if ($channel) {
                        $channel->name = $channelData['name'];
                        $channel->save();
                    }
                }

                $offsetChannel += 200;

                $iterationsChannel++;
                if ($iterationsChannel > $maxIterationsChannel) {
                    Log::info("Too many iterations for channels. Token ID: {$token->id}");
                    break;
                }

            } while ($total === null || $offsetChannel < $total);
        }
    }
}
