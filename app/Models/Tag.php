<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'style'];

    public function creatives(): BelongsToMany
    {
        return $this->belongsToMany(Creative::class);
    }

    public function getTailwindColorAttribute(): string
    {
        $tailwindColorMap = [
            'red'         => 'red-500',
            'crimson'     => 'red-600',
            'rose'        => 'rose-500',
            'pink'        => 'pink-500',
            'magenta'     => 'fuchsia-600',
            'fuchsia'     => 'fuchsia-500',
            'purple'      => 'purple-500',
            'violet'      => 'violet-500',     // изменено с purple-600
            'indigo'      => 'indigo-500',
            'blue'        => 'blue-500',
            'azure'       => 'sky-300',
            'sky'         => 'sky-500',
            'cyan'        => 'cyan-500',
            'teal'        => 'teal-500',
            'mint'        => 'emerald-200',
            'emerald'     => 'emerald-500',
            'green'       => 'green-500',
            'lime'        => 'lime-500',
            'chartreuse'  => 'lime-300',
            'yellow'      => 'yellow-500',
            'amber'       => 'amber-500',
            'orange'      => 'orange-500',
            'tangerine'   => 'orange-400',
            'salmon'      => 'rose-300',       // изменено для лучшего соответствия
            'lightpink'   => 'pink-200',
            'lavender'    => 'violet-200',     // изменено с purple-200
            'lightblue'   => 'blue-200',
            'lightcyan'   => 'cyan-200',
            'lightgreen'  => 'green-200',
            'lightyellow' => 'yellow-200',
            'peach'       => 'orange-200',
            'coral'       => 'rose-400',
        ];

        // Безопасный фолбэк на нейтральный цвет, если цвет не найден
        return $tailwindColorMap[$this->style] ?? 'gray-500';
    }
}
