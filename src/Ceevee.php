<?php

declare(strict_types=1);

namespace Worksome\Ceevee;

use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\HttpFoundation\File\File;
use Worksome\Ceevee\ContentProviders\FileContentProvider;
use Worksome\Ceevee\ContentProviders\FilePathContentProvider;
use Worksome\Ceevee\Contracts\ContentProvider;
use Worksome\Ceevee\Contracts\Parser;
use Worksome\Ceevee\Managers\ParserManager;
use Worksome\Ceevee\Support\CVDetail;

final class Ceevee
{
    use ForwardsCalls;

    private ?Parser $parser = null;

    public function __construct(private ParserManager $parserManager)
    {
    }

    public function read(ContentProvider|File|string $file): CVDetail
    {
        $file = match (true) {
            $file instanceof File => new FileContentProvider($file),
            is_string($file) => new FilePathContentProvider($file),
            default => $file,
        };

        return $this->parser()->parse($file->getContent());
    }

    /**
     * Set a Parser to use when reading CV files. If set
     * to `null` or omitted entirely, we will use the
     * `ParserManager` to obtain a Parser instance.
     */
    public function usingParser(?Parser $parser): self
    {
        $this->parser = $parser;

        return $this;
    }

    private function parser(): Parser
    {
        // @phpstan-ignore-next-line
        return $this->parser ?? $this->parserManager->driver();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardCallTo($this->parser(), $name, $arguments);
    }
}
