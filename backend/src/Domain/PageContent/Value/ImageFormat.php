<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Raster formats accepted for uploads. SVG is deliberately absent: it can carry scripts.
 */
enum ImageFormat: string
{
    case Jpeg = 'image/jpeg';
    case Png = 'image/png';
    case Webp = 'image/webp';

    /**
     * Detects the format from the file's real content, ignoring any client-supplied name or type.
     */
    public static function detect(string $bytes): ?self
    {
        $info = @getimagesizefromstring($bytes);
        if ($info === false) {
            return null;
        }

        return self::tryFrom($info['mime']);
    }

    public function extension(): string
    {
        return match ($this) {
            self::Jpeg => 'jpg',
            self::Png => 'png',
            self::Webp => 'webp',
        };
    }
}
