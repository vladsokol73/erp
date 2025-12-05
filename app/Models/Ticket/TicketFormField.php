<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketFormField extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'topic_id',
        'name',
        'label',
        'type',
        'validation_rules',
        'options',
        'is_required',
        'sort_order',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($field) {
            $field->fieldValues()->delete();
        });
    }

    protected $casts = [
        'validation_rules' => 'json',
        'options' => 'json',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the topic that owns the field.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(TicketTopic::class, 'topic_id');
    }

    /**
     * Get available validation rules
     */
    public static function getValidationRules(): array
    {
        return [
            'email' => ['label' => 'Email', 'value_type' => null],
            'url' => ['label' => 'URL', 'value_type' => null],
            'numeric' => ['label' => 'Numeric', 'value_type' => null],
            'integer' => ['label' => 'Integer', 'value_type' => null],
            'min' => ['label' => 'Minimum Value', 'value_type' => 'number'],
            'max' => ['label' => 'Maximum Value', 'value_type' => 'number'],
            'between' => ['label' => 'Between', 'value_type' => 'range'],
            'size' => ['label' => 'Size', 'value_type' => 'number'],
            'date' => ['label' => 'Date', 'value_type' => null],
            'before' => ['label' => 'Before Date', 'value_type' => 'date'],
            'after' => ['label' => 'After Date', 'value_type' => 'date'],
            'in' => ['label' => 'In Values', 'value_type' => 'array'],
            'not_in' => ['label' => 'Not In Values', 'value_type' => 'array'],
            'mimes' => ['label' => 'File Types', 'value_type' => 'array'],
            'image' => ['label' => 'Image', 'value_type' => null],
            'dimensions' => ['label' => 'Image Dimensions', 'value_type' => 'dimensions'],
            'unique' => ['label' => 'Unique', 'value_type' => null],
            'exists' => ['label' => 'Exists', 'value_type' => null],
            'multiple' => ['label' => 'Multiple Values', 'value_type' => null],
        ];
    }

    /**
     * Get the field values for this field.
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(TicketFieldValue::class, 'field_id');
    }

    /**
     * Get available field types with their labels.
     */
    public static function getFieldTypes(): array
    {
        return [
            'text' => 'Text',
            'number' => 'Number',
            'select' => 'Select',
            'multiselect' => 'Multiselect',
            'country' => 'Country',
            'textarea' => 'Text Area',
            'date' => 'Date',
            'file' => 'File Upload',
            'checkbox' => 'Checkbox'
        ];
    }
}
