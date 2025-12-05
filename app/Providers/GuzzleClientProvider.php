<?php

namespace App\Providers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * GuzzleHttp Client Wrapper
 * @author Himanshu Verma <himan.verma@live.com>
 * @version 1.0.0
 */
class GuzzleClientProvider
{
    protected static $client = null;

    public static function getInstance(Request $userRequest = null, bool $skipCertCheck = false, bool $log_api = false) : Client
    {
        if (is_null(static::$client)) {
            $stack = HandlerStack::create();
            if ($log_api) {
                $folderPath = storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR;
                Log::info('$folderPath = ' . $folderPath);
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }
                $folderPath .=  Carbon::now()->format('Y-m') . DIRECTORY_SEPARATOR;
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                $logfile = $folderPath . Carbon::now()->format('Y.m.d') . '.log';

                $log = [];
                $stack->push(Middleware::mapRequest(function (RequestInterface $request) use (&$log, $userRequest) {
                    $log = [
                        "URL" => $request->getUri()->__toString(),
                        "Request" => [
                            "Method" => $request->getMethod(),
                            "Headers" => $request->getHeaders(),
                            "Data" => $request->getBody()->getContents()
                        ],
                        "User" => $userRequest ? ['id' => $userRequest->user()['id'], 'IP_Address' => $userRequest->ip()] : NULL
                    ];

                    return $request;
                }));
                $stack->push(Middleware::mapResponse(function (ResponseInterface $response) use ($logfile, &$log) {
                    $log["Response"] = [
                        "HttpStatusCode" => $response->getStatusCode(),
                        "Data" => $response->getBody()->getContents()
                    ];
                    try {
                        file_put_contents($logfile, "\n\n\nLOGTIME:" . date('c') . "\n" . print_r($log, true), FILE_APPEND);
                    } catch (\Exception $exception) {

                    }
                    $response->getBody()->rewind();
                    return $response;
                }));
            }
            if ($skipCertCheck) {
                static::$client = new Client([
                    'handler' => $stack,
                    'verify' => false,
                ]);
            } else {
                static::$client = new Client([
                    'handler' => $stack
                ]);
            }
        }
        return static::$client;
    }
    public function __wakeup() {}
    private function __construct() {}
    private function __clone() {}
}

