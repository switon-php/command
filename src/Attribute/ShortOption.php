<?php

declare(strict_types=1);

namespace Switon\Command\Attribute;

use Attribute;

/**
 * Declares explicit short-option alias for one command action parameter.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class ShortOption
{
    public function __construct(
        public readonly string $letter,
    )
    {
    }
}
