<?php

declare(strict_types=1);

namespace Switon\Command;

use ReflectionMethod;

/**
 * Contract for reading metadata from command classes.
 *
 * @see \Switon\Command\CommandInspector
 */
interface CommandInspectorInterface
{
    public function isHiddenCommand(string $commandClassName): bool;

    public function getCommandDescription(string $class): string;

    /**
     * @return array<string, string>
     */
    public function getActions(string $commandClassName, bool $includeHidden = false): array;

    public function getMethodDescription(ReflectionMethod $rMethod): string;

    public function hasOptions(ReflectionMethod $rMethod): bool;

    /**
     * @return array<string, array{description: string, default: mixed, type: string}>
     */
    public function getOptions(ReflectionMethod $rMethod): array;

    public function getActionAiDoc(string $commandClassName, string $actionName): ?string;

    /**
     * @return array<string, array{description: string, default: mixed, type: string}>
     */
    public function getActionParameters(string $commandClassName, string $actionName): array;
}
