<?php

declare(strict_types=1);

namespace Switon\Command\Tests\Unit;

use ReflectionMethod;
use Switon\Command\Attribute\Hidden;
use Switon\Command\Attribute\ShortOption;
use Switon\Command\Attribute\Tool;
use Switon\Command\CommandInspector;
use Switon\Command\Tests\TestCase;

class CommandInspectorTest extends TestCase
{
    public function testHiddenAndToolAttributesAreAvailable(): void
    {
        $hidden = new Hidden();
        $tool = new Tool('Describe the action');
        $shortOption = new ShortOption('x');

        $this->assertInstanceOf(Hidden::class, $hidden);
        $this->assertSame('Describe the action', $tool->doc);
        $this->assertSame('x', $shortOption->letter);
    }

    public function testInspectorReadsHiddenToolAndOptionsMetadata(): void
    {
        $inspector = new CommandInspector();

        $this->assertTrue($inspector->isHiddenCommand(CommandInspectorFixture::class));
        $this->assertSame('Fixture command summary', $inspector->getCommandDescription(CommandInspectorFixture::class));
        $this->assertSame('Action summary', $inspector->getMethodDescription(new ReflectionMethod(CommandInspectorFixture::class, 'demoAction')));
        $this->assertSame('Returns JSON: {"ok": true}', $inspector->getActionAiDoc(CommandInspectorFixture::class, 'tool'));
        $this->assertNull($inspector->getActionAiDoc(CommandInspectorFixture::class, 'demo'));

        $actions = $inspector->getActions(CommandInspectorFixture::class);
        $this->assertArrayNotHasKey('hidden', $actions);
        $this->assertArrayHasKey('demo', $actions);

        $options = $inspector->getOptions(new ReflectionMethod(CommandInspectorFixture::class, 'demoAction'));
        $this->assertSame('string', $options['name']['type']);
        $this->assertSame('Option name used for filtering', $options['name']['description']);

        $params = $inspector->getActionParameters(CommandInspectorFixture::class, 'demo');
        $this->assertSame($options, $params);
    }

    public function testGetActionsIncludesHiddenActionsWhenRequested(): void
    {
        $inspector = new CommandInspector();

        $actions = $inspector->getActions(CommandInspectorFixture::class, true);

        $this->assertArrayHasKey('hidden', $actions);
        $this->assertSame('hidden', $actions['hidden']);
    }
}

#[Hidden]
/**
 * Fixture command summary.
 */
final class CommandInspectorFixture
{
    /**
     * Action summary.
     *
     * @param string $name Option name used for filtering
     */
    #[Tool('Returns JSON: {"ok": true}')]
    public function toolAction(): void
    {
    }

    /**
     * Action summary.
     *
     * @param string $name Option name used for filtering
     */
    public function demoAction(
        string $name = 'demo'
    ): void {
    }

    #[Hidden]
    public function hiddenAction(): void
    {
    }
}
