<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use JsonException;

/**
 * Value of a `stops` field: the Stops of a Route as a JSON-encoded array (Item fields stay strings, ADR 0005).
 * Each Stop is `{x, y, label, side}`: position in percent of the Route map picture, a plain-text label of at most
 * two lines and the side of the point its label is shown on.
 */
final readonly class RouteStops
{
    public const int MIN_STOPS = 2;
    public const int MAX_STOPS = 20;
    public const int LABEL_LENGTH_LIMIT = 60;
    public const array SIDES = ['top', 'right', 'bottom', 'left'];

    /**
     * The canonical JSON (only known keys, trimmed labels, coordinates rounded to 0.1),
     * or `null` when the value breaks any rule.
     */
    public static function normalize(string $value): ?string
    {
        try {
            $stops = json_decode($value, true, 4, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
        if (!is_array($stops) || !array_is_list($stops)) {
            return null;
        }
        $count = count($stops);
        if ($count < self::MIN_STOPS || $count > self::MAX_STOPS) {
            return null;
        }

        $result = [];
        foreach ($stops as $stop) {
            $clean = is_array($stop) ? self::normalizeStop($stop) : null;
            if ($clean === null) {
                return null;
            }
            $result[] = $clean;
        }

        return json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

    /**
     * @param array<array-key, mixed> $stop
     * @return array{x: float, y: float, label: string, side: string}|null
     */
    private static function normalizeStop(array $stop): ?array
    {
        $x = self::percent($stop['x'] ?? null);
        $y = self::percent($stop['y'] ?? null);
        $label = $stop['label'] ?? null;
        $side = $stop['side'] ?? null;
        if ($x === null || $y === null || !is_string($label) || !in_array($side, self::SIDES, true)) {
            return null;
        }

        $label = trim(str_replace("\r\n", "\n", $label));
        if ($label === '' || mb_strlen($label) > self::LABEL_LENGTH_LIMIT || substr_count($label, "\n") > 1
            || preg_match('/[\x00-\x09\x0B-\x1F\x7F]/u', $label) !== 0
        ) {
            return null;
        }

        return ['x' => $x, 'y' => $y, 'label' => $label, 'side' => $side];
    }

    private static function percent(mixed $value): ?float
    {
        if (!is_int($value) && !is_float($value)) {
            return null;
        }
        $value = round((float) $value, 1);

        return $value >= 0 && $value <= 100 ? $value : null;
    }
}
