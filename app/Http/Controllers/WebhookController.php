<?php

namespace App\Http\Controllers;

use App\Jobs\Chat2Desk;
use App\Jobs\Chat2DeskOperator;
use App\Jobs\Product;
use App\Jobs\PuzzleBot;
use App\Models\clients\ClientsLog;
use App\Models\Log;
use App\Models\Operator\OperatorLog;
use App\Models\TelegramIntegration;
use App\Notifications\TicketNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function puzzleBot(Request $request): void
    {
        $data = $request->all();
        $webhook_data = json_encode($request->all());

        try {
            if (isset($data['c2d_channel_id'])) {
                $clientsLog = ClientsLog::query()->create([
                    'client_id' => $data['tg_id'],
                    'webhook_event' => 'PuzzleBot',
                    'webhook_data' => $webhook_data
                ]);
            } elseif (isset($data['date'])) {
                $clientsLog = ClientsLog::query()->create([
                    'client_id' => $data['user']['id'],
                    'webhook_event' => 'PuzzleBot',
                    'webhook_data' => $webhook_data
                ]);
            } else {
                $clientsLog = ClientsLog::query()->create([
                    'client_id' => $data['tg_id'],
                    'webhook_event' => 'PuzzleBot',
                    'webhook_data' => $webhook_data
                ]);
            }

            // Отправка в очередь на обработку
            PuzzleBot::dispatch($clientsLog);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function chat2Desk(Request $request): void
    {
        $data = $request->all();
        $webhook_data = json_encode($request->all());

        try {
            if (isset($data['clickid'])) {
                $clientsLog = ClientsLog::query()->create([
                    'client_id' => $data['tg_id'],
                    'webhook_event' => 'Chat2Desk',
                    'webhook_data' => $webhook_data
                ]);
                Chat2Desk::dispatch($clientsLog)->onQueue('clickid');
            } else {
                switch ($data['hook_type']) {
                    case 'inbox':
                        $clientsLog = ClientsLog::query()->create([
                            'client_id' => preg_replace('/\D/', '', $data['client']['phone']),
                            'c2d_client_id' => $data['client']['id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data
                        ]);
                        break;
                    case 'add_tag_to_client':
                    case 'delete_tag_from_client':
                        $clientsLog = ClientsLog::query()->create([
                            'c2d_client_id' => $data['client_id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data
                        ]);
                        break;
                    case 'outbox_status':
                        ClientsLog::query()->create([
                            'c2d_client_id' => $data['data']['client_id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data,
                            'task_status' => 'done'
                        ]);
                        return;
                    case 'outbox':
                        ClientsLog::query()->create([
                            'client_id' => preg_replace('/\D/', '', $data['client']['phone']),
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data,
                            'task_status' => 'done'
                        ]);
                        return;
                    case 'new_client':
                    case 'client_added_to_blacklist':
                    case 'client_updated':
                        ClientsLog::query()->create([
                            'client_id' => preg_replace('/\D/', '', $data['phone']),
                            'c2d_client_id' => $data['id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data,
                            'task_status' => 'done'
                        ]);
                        return;
                    case 'new_ticket':
                    case 'ticket_updated':
                    case 'ticket_deleted':
                        ClientsLog::query()->create([
                            'client_id' => $data['ticket']['reporter_id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data,
                            'task_status' => 'done'
                        ]);
                        return;
                    default:
                        ClientsLog::query()->create([
                            'c2d_client_id' => $data['client_id'],
                            'webhook_event' => 'Chat2Desk',
                            'webhook_data' => $webhook_data,
                            'task_status' => 'done'
                        ]);
                        return;
                }
                Chat2Desk::dispatch($clientsLog);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }


    public function c2dOperators(Request $request): void
    {
        $webhook_data = $request->all();

        try {

            if ($webhook_data['type'] == 'to_client' || $webhook_data['type'] == 'from_client') {
                $datetime = Carbon::parse($webhook_data['event_time'])->format('Y-m-d H:i:s');

                if ($webhook_data['operator_id'] != null) {
                    $operatorLog = OperatorLog::query()->create([
                        'operator_id' => $webhook_data['operator_id'],
                        'client_id' => preg_replace('/\D/', '', $webhook_data['client']['phone']),
                        'channel_id' => $webhook_data['channel_id'],
                        'is_new_client' => $webhook_data['is_new_client'],
                        'event_type' => $webhook_data['hook_type'],
                        'event_time' => $datetime
                    ]);

                    Chat2DeskOperator::dispatch($operatorLog);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function product(Request $request): void
    {
        $data = $request->query();
        $webhook_data = json_encode($request->all());

        try {

            $clientLog = ClientsLog::query()->create([
                'client_id' => $data['sub5'],
                'webhook_event' => 'Product',
                'webhook_data' => $webhook_data
            ]);

            Product::dispatch($clientLog);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . ' | ' . $webhook_data);
        }
    }

    public function tgBot(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $str = $data['message']['text'] ?? '';

            Log::info($str);

            preg_match('/^\/start\s+(key-[a-zA-Z0-9]+)$/i', trim($str), $matches);

            $key = $matches[1] ?? null;

            Log::info("Hook key: $key");

            if ($key) {
                $integration = TelegramIntegration::query()->where('key', $key)->first();
                $integration->update([
                    'tg_id' => $data['message']['chat']['id'],
                    'activated_at' => Carbon::now(),
                ]);
                $user = $integration->user;
                $user->notify(new TicketNotification("connected"));
            } else {
                Log::error('Некорректный формат команды /start' . $str);
            }
            return response()->json([], 200);
        } catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([], 200);
        }
    }
}
