<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['text', 'type'];
    protected $table = 'logs';

    public static function error($text){
        Log::create([
            'type' => 'error',
            'text' => $text
        ]);
    }

    public static function info($text){
        Log::create([
            'type' => 'info',
            'text' => $text
        ]);
    }
}
