<?php

declare(strict_types=1);

namespace Switon\Command\Tests\Unit;

use ReflectionProperty;
use Switon\Command\CommandDiscovery;
use Switon\Command\Tests\TestCase;
use Switon\ComposerExtra\ComposerExtraInterface;
use Switon\Core\ClassScannerInterface;

class CommandDiscoveryTest extends TestCase
{
    public function testDiscoverMergesPackageAndScannerResults(): void
    {
        $discovery = new CommandDiscovery();

        $composer = $this->createMock(ComposerExtraInterface::class);
        $composer->expects($this->once())
            ->method('getClasses')
            ->with('switon.commands')
            ->willReturn([
                'Vendor\\Pkg\\AlphaCommand',
                'Vendor\\Pkg\\DupCommand',
            ]);

        $scanner = $this->createMock(ClassScannerInterface::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->with([
                '@app/Command/*Command.php' => 'App\\Command\\*Command',
                '@app/Areas/*/Command/*Command.php' => 'App\\Areas\\*\\Command\\*Command',
            ])
            ->willReturn([
                'App\\Command\\DupCommand',
                'App\\Command\\BetaCommand',
            ]);

        $this->setProperty($discovery, 'composerExtra', $composer);
        $this->setProperty($discovery, 'classScanner', $scanner);

        $result = $discovery->discover();

        $this->assertSame([
            'alpha' => 'Vendor\\Pkg\\AlphaCommand',
            'beta' => 'App\\Command\\BetaCommand',
            'completion' => 'Switon\\Cli\\Command\\CompletionCommand',
            'dup' => 'App\\Command\\DupCommand',
            'help' => 'Switon\\Cli\\Command\\HelpCommand',
            'list' => 'Switon\\Cli\\Command\\ListCommand',
        ], $result);
    }

    public function testDiscoverCachesResults(): void
    {
        $discovery = new CommandDiscovery();

        $composer = $this->createMock(ComposerExtraInterface::class);
        $composer->expects($this->once())
            ->method('getClasses')
            ->with('switon.commands')
            ->willReturn([]);

        $scanner = $this->createMock(ClassScannerInterface::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([]);

        $this->setProperty($discovery, 'composerExtra', $composer);
        $this->setProperty($discovery, 'classScanner', $scanner);

        $first = $discovery->discover();
        $second = $discovery->discover();

        $this->assertSame($first, $second);
    }

    private function setProperty(object $object, string $property, mixed $value): void
    {
        $ref = new ReflectionProperty($object, $property);
        $ref->setValue($object, $value);
    }
}
