<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\HtmlSanitizerInterface;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final readonly class SymfonyHtmlSanitizer implements HtmlSanitizerInterface
{
    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $this->sanitizer = new HtmlSanitizer(
            new HtmlSanitizerConfig()
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowRelativeMedias()
                ->allowMediaSchemes(['http', 'https'])
                ->forceHttpsUrls(false)
                ->allowAttribute('class', ['p', 'span', 'div', 'a', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'b', 'strong', 'em', 'i', 'br'])
                ->allowAttribute('target', 'a')
                ->allowAttribute('rel', 'a'),
        );
    }

    public function sanitize(string $html): string
    {
        return $this->sanitizer->sanitize($html);
    }
}
