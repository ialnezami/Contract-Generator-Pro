<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'signer_id',
        'signature_type',
        'signed_at',
        'ip_address',
        'user_agent',
        'signature_data',
        'verification_hash',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'signature_data' => 'array',
    ];

    const SIGNATURE_TYPE_DIGITAL = 'digital';
    const SIGNATURE_TYPE_ELECTRONIC = 'electronic';
    const SIGNATURE_TYPE_WET = 'wet';

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('signature_type', $type);
    }

    public function scopeBySigner($query, $signerId)
    {
        return $query->where('signer_id', $signerId);
    }

    public function getSignatureTypeDisplayAttribute(): string
    {
        $types = [
            self::SIGNATURE_TYPE_DIGITAL => 'Digital Signature',
            self::SIGNATURE_TYPE_ELECTRONIC => 'Electronic Signature',
            self::SIGNATURE_TYPE_WET => 'Wet Signature',
        ];

        return $types[$this->signature_type] ?? ucfirst($this->signature_type);
    }

    public function getTimeElapsedAttribute(): string
    {
        return $this->signed_at->diffForHumans();
    }

    public function getIsVerifiedAttribute(): bool
    {
        return !empty($this->verification_hash);
    }
}
