<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\RajaOngkirSetting;
use Filament\Notifications\Notification;

class RajaOngkirService
{
    protected $apiKey;
    protected $setting;
    protected $baseUrl;

    public function __construct()
    {
        $this->setting = RajaOngkirSetting::getActive();

        if (!$this->setting || !$this->setting->is_valid) {
            Notification::make()
                ->title('RajaOngkir API is not valid')
                ->body('Please configure valid RajaOngkir settings before creating a store.')
                ->danger()
                ->send();
            return;
        }

        $this->apiKey = $this->setting->api_key;
        $this->baseUrl = $this->setting->baseUrl; // Access baseUrl attribute
    }

    /**
     * Fetch the list of provinces from the RajaOngkir API.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProvinces()
    {
        if (!$this->setting || !$this->setting->is_valid) {
            return collect();
        }

        // dd($this->baseUrl);

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/province');

            // dd($response);

            if ($response->successful()) {
                return collect($response->json('rajaongkir.results'))
                    ->pluck('province', 'province_id');
            } else {
                Log::error('Failed to fetch provinces', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching provinces from RajaOngkir API', [
                'exception' => $e->getMessage(),
            ]);
        }

        return collect(); // Return empty collection on failure
    }

    public function getCities($provinceId)
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->get($this->baseUrl . '/city', [
            'province' => $provinceId,
        ]);
        
        if ($response->successful()) {
            return collect($response->json('rajaongkir.results'))
                ->pluck('city_name', 'city_id');
        } else {
            Log::error('Failed to fetch cities', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
