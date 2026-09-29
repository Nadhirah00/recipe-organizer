<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait DownloadsRecipeImages
{
    /**
     * Download a real photo into the public disk and return its relative path.
     *
     * Falls back to a placeholder source and finally to null, so a missing
     * network never stops the rest of the seed. The reason for a failure is
     * reported so an unreachable host is obvious rather than silent.
     */
    private function storeRecipeImage(string $sourceUrl, string $slug, string $recipeName): ?string
    {
        $candidates = [
            $sourceUrl,
            'https://picsum.photos/seed/'.$slug.'/1200/800',
        ];

        $lastFailure = 'no attempt made';

        foreach ($candidates as $candidate) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders(['User-Agent' => 'recipe-organizer-seeder'])
                    ->get($candidate);

                if (! $response->successful()) {
                    $lastFailure = "{$candidate} returned HTTP {$response->status()}";

                    continue;
                }

                $contents = $response->body();

                if (@getimagesizefromstring($contents) === false) {
                    $lastFailure = "{$candidate} did not return a readable image";

                    continue;
                }

                $path = 'recipes/seed-'.$slug.'.jpg';

                Storage::disk('public')->put($path, $contents);

                return $path;
            } catch (Throwable $exception) {
                $lastFailure = $exception->getMessage();

                continue;
            }
        }

        $this->command?->warn("No image for {$recipeName}: {$lastFailure}");

        return null;
    }
}
