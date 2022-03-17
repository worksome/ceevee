<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Illuminate\Support\Collection;
use Worksome\Ceevee\Support\Link;

final class LinksParser
{
    public function __construct(private array $details)
    {
    }

    /**
     * @return array<int, Link>
     */
    public function __invoke(): array
    {
        return $this->directLinks()->merge($this->linksFromContactMethods())->filter()->all();
    }

    private function directLinks(): Collection
    {
        $linkDetails = data_get($this->details, 'UserArea.sov:ResumeUserArea.sov:ReservedData.sov:Urls.sov:Url', []);

        return collect($linkDetails)->map(fn (string $url) => $this->buildLinkFromUrl($url));
    }

    private function buildLinkFromUrl(string $url): Link|null
    {
        $parts = parse_url($url);

        return $parts === false
            ? null
            : new Link($parts['host'] ?? $parts['path'], $url);
    }

    private function linksFromContactMethods(): Collection
    {
        $contactMethodDetails = data_get($this->details, 'StructuredXMLResume.ContactInfo.ContactMethod');

        return collect($contactMethodDetails)
            ->filter(fn(array $details) => array_key_exists('InternetWebAddress', $details))
            ->map(fn(array $details) => new Link(
                $details['Use'] ?? $details['InternetWebAddress'],
                $details['InternetWebAddress']
            ));
    }
}
