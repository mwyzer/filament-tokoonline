<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\RajaOngkirSetting;
use Filament\Notifications\Notification;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    protected $setting;

    public function __construct()
    {
        $this->setting = RajaOngkirSetting::getActive();

        if (!$this->setting || !$this->setting->is_valid) {
            Notification::make()
                ->title('RajaOngkir API Configuration Invalid')
                ->body('Please configure valid RajaOngkir settings before performing API operations.')
                ->danger()
                ->send();

            Log::error('RajaOngkir settings are invalid or missing.');
            return;
        }

        $this->apiKey = $this->setting->api_key;
        $this->baseUrl = $this->setting->baseUrl ?: 'https://api.rajaongkir.com/starter'; // Default fallback URL
    }

    /**
     * Fetch the list of provinces from the RajaOngkir API.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProvinces()
    {
        if (!$this->isServiceConfigured()) {
            return collect();
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/province');

            if ($response->successful()) {
                return collect($response->json('rajaongkir.results'))
                    ->pluck('province', 'province_id');
            }

            Log::error('Failed to fetch provinces from RajaOngkir API', [
                'status' => $response->status(),
                'response_body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while fetching provinces from RajaOngkir API', [
                'exception_message' => $e->getMessage(),
            ]);
        }

        return collect(); // Return empty collection on failure
    }

    /**
     * Fetch the list of cities based on the given province ID.
     *
     * @param string|int $provinceId
     * @return \Illuminate\Support\Collection
     */
    public function getCities($provinceId)
    {
        if (!$this->isServiceConfigured()) {
            return collect();
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/city', [
                'province' => $provinceId,
            ]);

            if ($response->successful()) {
                return collect($response->json('rajaongkir.results'))
                    ->pluck('city_name', 'city_id');
            }

            Log::error('Failed to fetch cities from RajaOngkir API', [
                'status' => $response->status(),
                'response_body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while fetching cities from RajaOngkir API', [
                'exception_message' => $e->getMessage(),
            ]);
        }

        return collect(); // Return empty collection on failure
    }

    /**
     * Check if the service is properly configured with valid settings.
     *
     * @return bool
     */
    protected function isServiceConfigured(): bool
    {
        if (!$this->setting || !$this->setting->is_valid) {
            Log::warning('RajaOngkir settings are not configured or invalid.');
            return false;
        }

        return true;
    }
}
