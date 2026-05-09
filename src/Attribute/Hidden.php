<?php

declare(strict_types=1);

namespace Switon\Command\Attribute;

use Attribute;

/**
 * Marker for classes or methods hidden from default listings.
 *
 * Actual behavior depends on the consuming component.
 *
 * @see \Switon\Command\CommandInspector::isHiddenCommand()
 * @see \Switon\Command\CommandInspector::isMethodHidden()
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Hidden
{
    public function __construct()
    {
    }
}
