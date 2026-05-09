<?php

declare(strict_types=1);

namespace Switon\Command;

use Switon\ComposerExtra\ComposerExtraInterface;
use Switon\Core\Attribute\Autowired;
use Switon\Core\ClassScannerInterface;
use Switon\Core\Naming;
use Throwable;
use function explode;
use function ksort;
use function str_ends_with;
use function str_starts_with;
use function substr;

/**
 * Builds and caches the command registry.
 *
 * Collects command definitions from package metadata and application command files.
 *
 * Guidance: Framework package commands must be listed in composer.json `extra.switon.commands`; app commands under `@app/Command` and `@app/Areas/{area}/Command` are scanned automatically.
 *
 * Road-signs:
 * - `*Command::*Action`
 * - packages: {@see ComposerExtraInterface::getClasses()} `switon.commands`
 * - app: {@see ClassScannerInterface} + `$files`
 * - `Naming::kebab()` `*Command` → command-name
 *
 * @see \Switon\Command\CommandDiscoveryInterface
 * @see \Switon\ComposerExtra\ComposerExtraInterface
 * @see \Switon\Core\ClassScannerInterface
 */
class CommandDiscovery implements CommandDiscoveryInterface
{
    #[Autowired] protected ComposerExtraInterface $composerExtra;
    #[Autowired] protected ClassScannerInterface $classScanner;
    #[Autowired] protected bool $app_only = false;

    /**
     * Glob entries for application command classes (path → FQCN template via ClassScanner).
     *
     * @var array<string, string>
     */
    #[Autowired] protected array $files = [
        '@app/Command/*Command.php' => 'App\\Command\\*Command',
        '@app/Areas/*/Command/*Command.php' => 'App\\Areas\\*\\Command\\*Command',
    ];

    /** @var list<string> Class-name prefixes to exclude (typically component namespaces). */
    #[Autowired] protected array $excludes = [];

    /**
     * Built-in emergency command classes that should remain discoverable even if composer-extra cache is missing/corrupt.
     *
     * @var list<class-string>
     */
    #[Autowired] protected array $builtins = [
        'Switon\\Cli\\Command\\ListCommand',
        'Switon\\Cli\\Command\\HelpCommand',
        'Switon\\Cli\\Command\\CompletionCommand',
    ];

    /** @var array<string, class-string> Cached command name to class map. */
    protected array $commands = [];

    /** {@inheritDoc} */
    public function discover(): array
    {
        if ($this->commands === []) {
            $commands = [];

            if (!$this->app_only) {
                $packageCommandClasses = $this->builtins;
                try {
                    foreach ($this->composerExtra->getClasses('switon.commands') as $commandClass) {
                        $packageCommandClasses[] = $commandClass;
                    }
                } catch (Throwable) {
                }

                foreach ($packageCommandClasses as $commandClass) {
                    if ($this->isExcludedByPrefix($commandClass)) {
                        continue;
                    }
                    $name = $this->extractCommandName($commandClass);
                    if ($name !== null) {
                        $commands[$name] = $commandClass;
                    }
                }
            }

            foreach ($this->classScanner->scan($this->files) as $className) {
                if ($this->isExcludedByPrefix($className)) {
                    continue;
                }
                $name = $this->extractCommandName($className);
                if ($name !== null) {
                    $commands[$name] = $className;
                }
            }

            ksort($commands);
            $this->commands = $commands;
        }

        return $this->commands;
    }

    /**
     * Converts FQCN ending with `Command` to kebab command name.
     */
    protected function extractCommandName(string $className): ?string
    {
        $parts = explode('\\', $className);
        $lastPart = $parts[count($parts) - 1];

        if (!str_ends_with($lastPart, 'Command')) {
            return null;
        }

        $name = substr($lastPart, 0, -7);
        return Naming::kebab($name);
    }

    /**
     * Returns true when class should be excluded from command discovery.
     */
    protected function isExcludedByPrefix(string $className): bool
    {
        foreach ($this->excludes as $prefix) {
            if ($prefix !== '' && str_starts_with($className, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
