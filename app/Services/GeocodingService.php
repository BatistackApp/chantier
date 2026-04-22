<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
    }

    public function getGeocodePlace(string $address): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Google Maps API Key manquante dans la configuration.');

            return null;
        }

        try {
            $response_place = Http::withHeaders([
                'X-Goog-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->post('https://places.googleapis.com/v1/places:autocomplete', [
                    'input' => $address,
                ]);

            $dataPlace = $response_place->json();

            $reponse_geo = Http::get('https://geocode.googleapis.com/v4/geocode/places/'.$dataPlace['suggestions'][0]['placePrediction']['placeId'].'?key='.$this->apiKey);

            if ($reponse_geo->failed()) {
                throw new Exception("Erreur de connexion à l'API Google Maps.");
            }

            $dataGeo = $reponse_geo->json();

            return [
                'latitude' => $dataGeo['location']['latitude'],
                'longitude' => $dataGeo['location']['longitude'],
            ];
        } catch (Exception $exception) {
            Log::error('Échec du calcul de distance Google : '.$exception->getMessage());

            return null;
        }
    }
}
