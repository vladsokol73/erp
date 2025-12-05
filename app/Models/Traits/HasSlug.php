<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    protected function generateSlug(): string
    {
        // Если модель - это TicketTopic, генерируем слаг с учетом категории
        if (get_class($this) === 'App\Models\tickets\TicketTopic') {
            $category = \App\Models\Ticket\TicketCategory::find($this->category_id);
            if ($category) {
                return Str::slug($category->name . '-' . $this->name);
            }
        }

        // Для всех остальных моделей генерируем слаг только из имени
        return Str::slug($this->name);
    }
}
