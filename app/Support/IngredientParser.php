<?php

namespace App\Support;

class IngredientParser
{
    /**
     * The unit pattern from before units had to be whole words.
     *
     * Kept only so the backfill command can recognise rows this pattern wrote,
     * and re-derive them. Never call it to build a shopping list.
     */
    public const LEGACY_PATTERN = '~^
        (
            \d+/\d+
            |
            \d+(?:\.\d+)?
            |
            pinch
        )
        \s*
        (
            tablespoons?|tbsp|
            teaspoons?|tsp|
            cups?|
            grams?|g|
            kilograms?|kg|
            milliliters?|ml|
            liters?|l|
            cloves?|
            pieces?|pcs|
            slices?|
            pinch(?:es)?
        )?
        \s*
        (?:of\s+)?
    ~ix';

    /**
     * Split a raw recipe ingredient line into its name and its quantity.
     *
     * The shopping list stores the quantity in its own column so it can be
     * rendered as a right-aligned column. Every path that writes shopping list
     * rows must use this parser, otherwise the quantity stays glued to the
     * ingredient text and is missing from the column.
     *
     * @return array{ingredient: string, quantity: string|null}
     */
    public static function parse(string $line): array
    {
        return self::split($line, self::pattern());
    }

    /**
     * Reproduce the parse made by the pre-fix unit pattern.
     *
     * Without a word boundary this matched only the leading letter of units it
     * did not know, so "1 lettuce leaf" became a quantity of "1 l" and a name
     * of "ettuce leaf". Stored rows are compared against this to prove they
     * came from the buggy pattern, rather than guessing from a damaged name.
     *
     * @return array{ingredient: string, quantity: string|null}
     */
    public static function parseLegacy(string $line): array
    {
        return self::split($line, self::LEGACY_PATTERN);
    }

    /**
     * @return array{ingredient: string, quantity: string|null}
     */
    private static function split(string $line, string $pattern): array
    {
        $line = trim($line);

        /*
        |----------------------------------------------------------------------
        | Quantity + unit
        |----------------------------------------------------------------------
        |
        | Supports:
        |
        | 3 cups cooked rice
        | 1 chicken breast
        | 2 cloves garlic
        | 1/2 teaspoon salt
        | 1/4 onion
        | 2 tablespoons soy sauce
        | 1 can tuna
        | 1 bunch bok choy
        | 1 stalk lemongrass
        | 2 kaffir lime leaves
        | 1 thumb ginger
        | 1 pinch salt
        | pinch salt
        |
        */

        if (preg_match($pattern, $line, $matches)) {

            $quantity = trim(
                $matches[1].
                (
                    ! empty($matches[2])
                        ? ' '.trim($matches[2])
                        : ''
                )
            );

            $name = trim(
                preg_replace($pattern, '', $line, 1)
            );

            return [
                'ingredient' => $name,
                'quantity' => $quantity,
            ];
        }

        return [
            'ingredient' => $line,
            'quantity' => null,
        ];
    }

    /**
     * The current unit pattern.
     *
     * Units must be whole words. Without the trailing lookahead, "l" matched the
     * start of "lettuce" and the rest of the word was left behind as the name.
     * Longer spellings are listed before their abbreviations for the same
     * reason, so "litre" is not cut down to "l".
     */
    private static function pattern(): string
    {
        return '~^
            (
                \d+/\d+
                |
                \d+(?:\.\d+)?
                |
                pinch
            )
            (?:\s*
                (
                    tablespoons?|tbsp|
                    teaspoons?|tsp|
                    cups?|
                    grams?|g|
                    kilograms?|kg|
                    millilit(?:er|re)s?|ml|
                    lit(?:er|re)s?|l|
                    cloves?|
                    pieces?|pcs|
                    slices?|
                    cans?|
                    tins?|
                    stalks?|
                    bunch(?:es)?|
                    packs?|
                    leaves|
                    thumbs?|
                    handfuls?|
                    dash(?:es)?|
                    drops?|
                    pinch(?:es)?
                )
                (?![a-z])
                \s*
            )?
            (?:of\s+)?
        ~ix';
    }
}
