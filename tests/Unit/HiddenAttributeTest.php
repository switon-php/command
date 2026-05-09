<?php

declare(strict_types=1);

namespace Switon\Command\Tests\Unit;

use Switon\Command\Attribute\Hidden;
use Switon\Command\Tests\TestCase;

final class HiddenAttributeTest extends TestCase
{
    public function testHiddenAttributeCanBeInstantiated(): void
    {
        $attribute = new Hidden();
        $this->assertInstanceOf(Hidden::class, $attribute);
    }

    public function testHiddenAttributeIsTargetClassAndMethod(): void
    {
        $reflection = new \ReflectionClass(Hidden::class);
        $attributes = $reflection->getAttributes();

        $attributeAttribute = null;
        foreach ($attributes as $attr) {
            if ($attr->getName() === \Attribute::class) {
                $attributeAttribute = $attr;
                break;
            }
        }

        $this->assertNotNull($attributeAttribute, 'Hidden should have Attribute attribute');

        $instance = $attributeAttribute->newInstance();
        $expectedFlags = \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD;
        $this->assertSame($expectedFlags, $instance->flags);
    }

    public function testHiddenAttributeOnClass(): void
    {
        $testClass = new #[Hidden] class {
            public function test(): void
            {
            }
        };

        $reflection = new \ReflectionClass($testClass);
        $attributes = $reflection->getAttributes(Hidden::class);

        $this->assertCount(1, $attributes, 'Class should have Hidden attribute');

        $hidden = $attributes[0]->newInstance();
        $this->assertInstanceOf(Hidden::class, $hidden);
    }

    public function testHiddenAttributeOnMethod(): void
    {
        $testClass = new class {
            #[Hidden]
            public function internalMethod(): void
            {
            }

            public function publicMethod(): void
            {
            }
        };

        $reflection = new \ReflectionClass($testClass);

        $internalMethod = $reflection->getMethod('internalMethod');
        $attributes = $internalMethod->getAttributes(Hidden::class);
        $this->assertCount(1, $attributes, 'internalMethod should have Hidden attribute');

        $hidden = $attributes[0]->newInstance();
        $this->assertInstanceOf(Hidden::class, $hidden);

        $publicMethod = $reflection->getMethod('publicMethod');
        $attributes = $publicMethod->getAttributes(Hidden::class);
        $this->assertCount(0, $attributes, 'publicMethod should not have Hidden attribute');
    }
}
