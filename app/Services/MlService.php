<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlService
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ml.url', 'http://localhost:5000'), '/');
        $this->timeout = (int) config('services.ml.timeout', 60);
    }

    public function estimateWeight(
        UploadedFile $photo,
        ?string $animalId = null,
        ?float $distanceMeters = null,
        ?string $photoAngle = null,
    ): array {
        try {
            $params = array_filter([
                'animal_id'       => $animalId,
                'distance_meters' => $distanceMeters,
                'photo_angle'     => $photoAngle,
            ], fn($v) => $v !== null);

            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($photo->getRealPath()), $photo->getClientOriginalName())
                ->post("{$this->baseUrl}/api/v1/estimate", $params);

            if ($response->failed()) {
                Log::error('[MlService] Error', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \RuntimeException('El servicio de estimación no está disponible.');
            }

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[MlService] Connection error', ['error' => $e->getMessage()]);
            throw new \RuntimeException('No se pudo conectar al servicio de estimación.');
        }
    }

    public function sendFeedback(string $animalId, float $estimated, float $real, ?string $notes = null): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/v1/feedback", [
                    'animal_id'           => $animalId,
                    'estimated_weight_kg' => $estimated,
                    'real_weight_kg'      => $real,
                    'notes'               => $notes,
                ]);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::warning('[MlService] Feedback failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}