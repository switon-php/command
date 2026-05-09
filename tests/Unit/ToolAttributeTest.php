<?php

declare(strict_types=1);

namespace Switon\Command\Tests\Unit;

use ReflectionMethod;
use Switon\Command\Attribute\Tool;
use Switon\Command\Tests\TestCase;

final class ToolAttributeTest extends TestCase
{
    public function testAttributeStoresDocOnMethod(): void
    {
        $ref = new ReflectionMethod(ToolSample::class, 'action');
        $attrs = $ref->getAttributes(Tool::class);
        $this->assertCount(1, $attrs);

        $tool = $attrs[0]->newInstance();
        $this->assertInstanceOf(Tool::class, $tool);
        $this->assertSame('Returns JSON: {"ok":true}', $tool->doc);
    }
}

final class ToolSample
{
    #[Tool('Returns JSON: {"ok":true}')]
    public function action(): void
    {
    }
}
