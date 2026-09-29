<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'cooking_time',
        'difficulty',
        'servings',
        'ingredients',
        'instructions',
        'image',
        'tutorial_url',
        'is_public',
    ];

    protected $casts = ['is_public' => 'boolean'];

    /**
     * The ingredients split into one trimmed entry per line, ready to render as
     * a list rather than a single block of pre-formatted text.
     *
     * Blank lines and stray bullet markers typed by the user are discarded. The
     * raw column is deliberately left untouched so the edit form textarea still
     * round-trips exactly what was saved.
     *
     * @return list<string>
     */
    public function ingredientList(): array
    {
        return $this->toLines($this->ingredients);
    }

    /**
     * The instructions split into one trimmed entry per line, with any leading
     * "1." or "2)" numbering removed so the view can number the steps itself.
     *
     * @return list<string>
     */
    public function instructionSteps(): array
    {
        return array_values(
            array_filter(
                array_map(
                    function (string $line): string {
                        return preg_replace('/^\d+[.)]\s*/', '', $line) ?? $line;
                    },
                    $this->toLines($this->instructions)
                ),
                fn (string $line): bool => $line !== ''
            )
        );
    }

    /**
     * Split a free-text column into trimmed, non-empty lines.
     *
     * @return list<string>
     */
    private function toLines(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        $lines = preg_split('/\R+/', $value) ?: [];

        return array_values(
            array_filter(
                array_map(
                    function (string $line): string {
                        return trim(
                            preg_replace('/^[-*\x{2022}]\s+/u', '', $line) ?? $line
                        );
                    },
                    $lines
                ),
                fn (string $line): bool => $line !== ''
            )
        );
    }

    /**
     * The privacy-enhanced embed URL for a YouTube tutorial, or null when the
     * stored link is empty or points somewhere other than YouTube.
     *
     * youtube-nocookie.com is used so the embed does not set tracking cookies
     * and avoids a consent banner. Callers fall back to a plain link when this
     * returns null, so an unrecognised URL is never a broken page.
     */
    public function youtubeEmbedUrl(): ?string
    {
        $videoId = $this->youtubeVideoId();

        return $videoId === null
            ? null
            : 'https://www.youtube-nocookie.com/embed/'.$videoId;
    }

    /**
     * Extract the 11-character video id from the tutorial URL.
     *
     * Accepts the watch, youtu.be, embed and shorts forms of a YouTube link.
     */
    public function youtubeVideoId(): ?string
    {
        if (blank($this->tutorial_url)) {
            return null;
        }

        $host = strtolower((string) parse_url($this->tutorial_url, PHP_URL_HOST));

        $host = preg_replace('/^www\./', '', $host);

        $path = (string) parse_url($this->tutorial_url, PHP_URL_PATH);

        $id = match (true) {
            $host === 'youtu.be' => trim($path, '/'),
            in_array($host, ['youtube.com', 'youtube-nocookie.com'], true) => $this->youtubeIdFromQuery()
                ?? $this->youtubeIdFromPath($path),
            default => null,
        };

        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) === 1
            ? $id
            : null;
    }

    /**
     * Read the v parameter from the tutorial URL's query string.
     */
    private function youtubeIdFromQuery(): ?string
    {
        $query = (string) parse_url($this->tutorial_url, PHP_URL_QUERY);

        parse_str($query, $parameters);

        $value = $parameters['v'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Read the id from a path such as /embed/<id> or /shorts/<id>.
     */
    private function youtubeIdFromPath(string $path): ?string
    {
        if (preg_match('#/(?:embed|shorts|v)/([A-Za-z0-9_-]{11})#', $path, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
