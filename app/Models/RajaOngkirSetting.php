<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirSetting extends Model
{
    protected $fillable = [
        'api_key',
        'api_type',
        'couriers',
        'is_valid',
        'error_message',
    ];

    protected $casts = [
        'couriers' => 'array', // Cast couriers as an array
        'is_valid' => 'boolean',
    ];

    /**
     * Get the latest active RajaOngkirSetting.
     *
     * @return static|null
     */
    public static function getActive()
    {
        return static::where('is_valid', true)->latest()->first();
    }

    /**
     * Check if the API type is Pro.
     *
     * @return bool
     */
    public function isPro(): bool
    {
        return $this->api_type === 'pro';
    }

    /**
     * Get the base URL for the RajaOngkir API.
     *
     * @return string
     */
    public function getBaseUrlAttribute(): string
    {
        return $this->api_type === 'pro'
            ? 'https://pro.rajaongkir.com/api'
            : 'https://api.rajaongkir.com/starter';
    }

    /**
     * Validate the API key by making a request to the RajaOngkir API.
     *
     * @return bool
     */
    public function validateApiKey(): bool
    {
        try {
            $baseUrl = $this->api_type === 'pro'
                ? 'https://pro.rajaongkir.com/api'
                : 'https://api.rajaongkir.com/starter';

            $response = Http::withHeaders([
                'key' => $this->api_key,
            ])->get("{$baseUrl}/province");

            // Validate response
            $isValid = $response->successful() && 
                data_get($response->json(), 'rajaongkir.status.code') === 200;
            $this->update([
                'is_valid' => $isValid,
                'error_message' => $isValid ? null : $response->json('rajaongkir.status.description'),
            ]);

            return $isValid;
        } catch (\Exception $e) {
            // Log the exception and return false
            Log::error('Error validating API key', ['exception' => $e]);
            return false;
        }
    }
}
