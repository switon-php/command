<?php

declare(strict_types=1);

namespace Switon\Command\Tests\Unit;

use ReflectionMethod;
use Switon\Command\Attribute\ShortOption;
use Switon\Command\Tests\TestCase;

class ShortOptionAttributeTest extends TestCase
{
    public function testShortOptionAttributeCanBeReadFromParameter(): void
    {
        $method = new ReflectionMethod(ShortOptionFixture::class, 'run');
        $parameter = $method->getParameters()[0];
        $attributes = $parameter->getAttributes(ShortOption::class);

        $this->assertCount(1, $attributes);
        $this->assertSame('x', $attributes[0]->newInstance()->letter);
    }
}

final class ShortOptionFixture
{
    public function run(
        #[ShortOption('x')]
        string $value
    ): void {
    }
}
