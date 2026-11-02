<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface HtmlSanitizerInterface
{
    /**
     * Removes everything but an allowlist of safe tags and attributes.
     */
    public function sanitize(string $html): string;
}
