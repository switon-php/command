<?php

declare(strict_types=1);

namespace Switon\Command\Attribute;

use Attribute;

/**
 * Marks a discoverable command action as an AI tool.
 *
 * Road-signs:
 * - command action discovery
 * - AI-facing synopsis
 * - `#[Hidden]` for exclusion
 *
 * @see \Switon\Command\CommandInspector::getActionAiDoc()
 * @see \Switon\Command\Attribute\Hidden
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Tool
{
    public function __construct(public string $doc)
    {
    }
}
