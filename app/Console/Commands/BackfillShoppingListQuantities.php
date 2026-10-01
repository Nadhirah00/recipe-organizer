<?php

namespace App\Console\Commands;

use App\Models\ShoppingList;
use App\Support\IngredientParser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:backfill-shopping-list-quantities')]
#[Description('Split quantities out of stored shopping list ingredients so every user gets the same right-aligned quantity column')]
class BackfillShoppingListQuantities extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $parsed = 0;
        $repaired = 0;
        $skipped = 0;

        ShoppingList::query()
            ->with('recipe')
            ->chunkById(200, function ($items) use (&$parsed, &$repaired, &$skipped) {
                foreach ($items as $item) {

                    $resolved = $this->resolve($item);

                    if ($resolved === null
                        || ($resolved['ingredient'] === $item->ingredient
                            && $resolved['quantity'] === $item->quantity)) {
                        $skipped++;

                        continue;
                    }

                    $item->update([
                        'ingredient' => $resolved['ingredient'],
                        'quantity' => $resolved['quantity'],
                    ]);

                    $resolved['outcome'] === 'repaired' ? $repaired++ : $parsed++;
                }
            });

        $this->components->info(
            "Parsed {$parsed} rows, repaired {$repaired} rows written by the old unit pattern, {$skipped} left unchanged."
        );

        return self::SUCCESS;
    }

    /**
     * Work out the correct name and quantity for a stored row.
     *
     * Rows written before units had to be whole words cannot be repaired from
     * their own text, because the old pattern had already moved part of the
     * name into the quantity. So each candidate source line is re-parsed with
     * the old pattern and compared to what was stored: an exact match proves
     * the row came from that line, and the current pattern gives the fix.
     *
     * Rows with no source line fall back to re-parsing their stored text, which
     * covers rows that never had a quantity to begin with.
     *
     * @return array{outcome: 'parsed'|'repaired', ingredient: string, quantity: string|null}|null
     */
    private function resolve(ShoppingList $item): ?array
    {
        $storedIngredient = trim($item->ingredient);
        $storedQuantity = $item->quantity;

        $hasQuantity = $storedQuantity !== null && $storedQuantity !== '';

        foreach ($item->recipe?->ingredientList() ?? [] as $line) {

            $current = IngredientParser::parse($line);

            /*
            |--------------------------------------------------------------
            | A row with no quantity was written from the raw line
            | before the parser ran at all, so the stored name is
            | the whole line rather than a parsed name.
            */

            if (! $hasQuantity) {

                if (strcasecmp(ucwords($line), $storedIngredient) !== 0) {
                    continue;
                }

                return $current['quantity'] === null
                    ? null
                    : $this->result($current, 'parsed');
            }

            /*
            |--------------------------------------------------------------
            | Re-parsing with the old pattern and matching the stored
            | row exactly proves which line produced it, so the
            | current pattern can supply the correct split.
            */

            /*
            |--------------------------------------------------------------
            | The old pattern eating a leading letter is proved by an
            | exact match against what it produced. A row that the
            | old pattern read correctly is still refreshed, so
            | improvements to the unit list reach rows written
            | before them.
            */

            $legacy = IngredientParser::parseLegacy($line);

            if ($this->matches($legacy, $storedIngredient, $storedQuantity)) {
                return $this->result($current, 'repaired');
            }
        }

        $parsed = IngredientParser::parse($item->ingredient);

        if ($parsed['quantity'] === null) {
            return null;
        }

        return $this->result($parsed, 'parsed');
    }

    /**
     * Whether a stored row is exactly what the old pattern produced.
     *
     * @param  array{ingredient: string, quantity: string|null}  $legacy
     */
    private function matches(array $legacy, string $storedIngredient, ?string $storedQuantity): bool
    {
        return strcasecmp(ucwords(trim($legacy['ingredient'])), $storedIngredient) === 0
            && strcasecmp((string) $legacy['quantity'], (string) $storedQuantity) === 0;
    }

    /**
     * Normalise a parse result into a storable row.
     *
     * The casing matches what the write paths produce, so a backfilled row is
     * indistinguishable from a freshly generated one.
     *
     * @param  array{ingredient: string, quantity: string|null}  $parsed
     * @return array{outcome: 'parsed'|'repaired', ingredient: string, quantity: string|null}
     */
    private function result(array $parsed, string $outcome): array
    {
        return [
            'outcome' => $outcome,
            'ingredient' => ucwords(trim($parsed['ingredient'])),
            'quantity' => $parsed['quantity'],
        ];
    }
}
