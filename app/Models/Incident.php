<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sms_id',
        'address_id',
        'description',
        'type',
    ];

    /**
     * The relationships that should always be loaded.
     * This ensures the map always gets the Barangay name and coordinates.
     */
    protected $with = ['barangay'];

    /**
     * Relationship: The SMS message that triggered this incident.
     */
    public function sms(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'sms_id');
    }

    /**
     * Relationship: The Barangay where the incident occurred.
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'address_id');
    }

    /**
     * Accessor: Useful for the Vue Map to get coordinates easily.
     * Use this if your Barangay model has 'latitude' and 'longitude'.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => $this->barangay->latitude ?? 0,
            'lng' => $this->barangay->longitude ?? 0,
        ];
    }
}