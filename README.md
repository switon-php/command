# Switon Command Package

Command metadata, discovery, and inspection for Switon Framework.

## Installation

```bash
composer require switon/command
```

**Requirements:** PHP 8.3+

## Quick Start

```php
use Switon\Command\Attribute\Hidden;
use Switon\Command\Attribute\Tool;

class ReportCommand
{
    #[Hidden]
    #[Tool('Returns JSON: report status by id.')]
    public function statusAction(string $id): array
    {
        return ['id' => $id, 'status' => 'ready'];
    }
}
```

Docs: https://docs.switon.dev/latest/command

## License

MIT.
