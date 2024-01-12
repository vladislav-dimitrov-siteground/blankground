<?php declare(strict_types=1);

namespace App\Webhooks;

require __DIR__ . '/../boostrap.php';

use Gitlab\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

abstract class AbstractWebhookHandler
{
    public const GITLAB_KEY = 'glpat-Vi6Kqo9vf7M7MLaziQ6C';

    public const PROJECTS = [
        52034094, // BlankGround
        52129768, // bg-common
    ];

    public Client $client;

    protected Logger $logger;

    protected array $projectData;

    abstract protected function handleSpecific(array $payload);

    public function __construct()
    {
        $this->initClient();
        $this->initLogger();
    }

    protected function initClient(): void
    {
        if (!isset($this->client)) {
            // Token authentication
            $this->client = new Client();
            $this->client->authenticate(self::GITLAB_KEY, Client::AUTH_HTTP_TOKEN);
        }
    }

    protected function initLogger(): void
    {
        if (!isset($this->logger)) {
            $this->logger = new Logger(static::class);
            $this->logger->pushHandler(new StreamHandler('log/webhook.log', Level::Info));
        }
    }

    public function handle(array $payload): void
    {
        $this->projectData = $payload['project'];

        $this->handleSpecific($payload);
    }

    public function getOtherProjectIDs(): array
    {
        return array_diff(self::PROJECTS, [$this->projectData['id']]);
    }
}
