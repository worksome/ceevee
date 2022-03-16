<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Tests\Factories\Http\Sovren;

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class SovrenHttpFactory
{
    const VERSION_9 = 9;

    private bool $makeActualRequest = false;

    private int $version = self::VERSION_9;

    private string $cv = 'hannah_mills';

    private Factory $client;

    private Dispatcher $event;

    public function __construct()
    {
        $this->event = new Dispatcher();
        $this->client = new Factory($this->event);
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * If you want to bypass faking the HTTP request and record the
     * result into the relevant file, you should temporarily add
     * this method call to the factory for a single request.
     */
    public function recordRealResponse(): self
    {
        $this->makeActualRequest = true;

        $this->event->listen(ResponseReceived::class, function (ResponseReceived $event) {
            file_put_contents($this->getResponseStubFilePath(), json_encode($event->response->json(), JSON_PRETTY_PRINT));
        });

        return $this;
    }

    public function usingVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function for(string $name): self
    {
        $this->cv = Str::snake($name);

        return $this;
    }

    public function create(): Factory
    {
        if ($this->makeActualRequest) {
            return $this->client;
        }

        if (! file_exists($this->getResponseStubFilePath())) {
            throw new InvalidArgumentException("[{$this->getResponseStubFilePath()}] does not exist as a stubbed response. You may need to create it.");
        }

        return $this->client->fake([
            'https://*.resumeparsing.com/*/parser/resume' => json_decode(file_get_contents($this->getResponseStubFilePath()), true)
        ]);
    }

    private function getResponseStubFilePath(): string
    {
        return __DIR__ . "/../../../Stubs/Http/Sovren/v{$this->version}/{$this->cv}.json";
    }
}
