<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketFormField;
use Illuminate\Support\Facades\Crypt;

class TicketFieldValue extends Model
{
    protected $fillable = [
        'ticket_id',
        'field_id',
        'value',
    ];

    // Мутатор для шифрования перед сохранением
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = Crypt::encryptString($value);
    }

    // Аксессор для расшифровки при извлечении
    public function getValueAttribute($value): bool|string
    {
        return Crypt::decryptString($value);
    }

    /**
     * Get the ticket that owns the field value.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Get the form field that owns the field value.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(TicketFormField::class, 'field_id');
    }
}
