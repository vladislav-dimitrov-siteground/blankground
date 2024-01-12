<?php declare(strict_types=1);

namespace App\Webhooks;

final class WebhookHandlerFactory
{
    protected const HANDLER_NAMESPACE = "App\Webhooks\\";

    protected const WEBHOOK_HANDLER_SUFFIX = 'WebhookHandler';

    public static function getHandler(array $payload): AbstractWebhookHandler
    {
        $className = self::HANDLER_NAMESPACE.self::buildClassNameFromType($payload['object_kind']);
        return new $className;
    }

    protected static function buildClassNameFromType(string $type): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $type)).self::WEBHOOK_HANDLER_SUFFIX);
    }
}
