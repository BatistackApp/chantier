<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\GeocodingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateLatLngProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Project $project) {}

    public function handle(GeocodingService $geocodingService): void
    {
        $geocode = $geocodingService->getGeocodePlace($this->project->address);

        if($geocode !== null) {
            $this->project->updateQuietly([
                'geo_lat' => $geocode['latitude'],
                'geo_long' => $geocode['longitude'],
            ]);
        }
    }
}
