<?php

declare(strict_types=1);

namespace Switon\Command;

/**
 * Contract for discovering available commands.
 *
 * Guidance: `discover()` returns a cached map only; it does not register commands with any runtime handler.
 *
 * @see \Switon\Command\CommandDiscovery
 */
interface CommandDiscoveryInterface
{
    /**
     * Returns the discovered command name to class map (cached).
     *
     * @return array<string, class-string>
     */
    public function discover(): array;
}
