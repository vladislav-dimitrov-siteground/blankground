<?php declare(strict_types=1);

namespace App;

class Application
{
    public function isAwesome(): bool
    {
        return true;
    }

    public function sayHello(): string
    {
        return 'Hello World!';
    }
}
