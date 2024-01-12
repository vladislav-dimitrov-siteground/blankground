<?php
namespace Tests\Unit;

use App\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testApplication(): void
    {
        $app = new Application();

        $this->assertTrue($app->isAwesome());

        $this->assertSame('Hello World!', $app->sayHello());
    }
}
