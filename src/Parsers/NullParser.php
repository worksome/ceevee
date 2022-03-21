<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers;

use PHPUnit\Framework\Assert;
use Worksome\Ceevee\Contracts\Parser;
use Worksome\Ceevee\Support\CVDetail;
use Worksome\Ceevee\Testing\CVDetailFactory;

final class NullParser implements Parser
{
    private array $fakeSequence = [];

    private int $sequenceIndex = 0;

    private int $timesRead = 0;

    /**
     * @param array<CVDetail> $details
     */
    public function fakeSequence(array $details): self
    {
        $this->fakeSequence = $details;

        return $this;
    }

    public function parse(string $content): CVDetail
    {
        $this->timesRead++;

        if (count($this->fakeSequence) === 0) {
            return CVDetailFactory::new()->create();
        }

        if (! array_key_exists($this->sequenceIndex, $this->fakeSequence)) {
            $this->sequenceIndex = 0;
        }

        $sequenceItem = $this->fakeSequence[$this->sequenceIndex];
        $this->sequenceIndex++;

        return $sequenceItem;
    }

    public function assertRead(int $timesRead = 1): void
    {
        Assert::assertSame($timesRead, $this->timesRead);
    }
}
