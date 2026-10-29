<?php

namespace App\Enums;

enum LeadStatus: string
{
    case Active = 'active';
    case Won = 'won';
    case Lost = 'lost';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Won => 'Won',
            self::Lost => 'Lost',
            self::Archived => 'Archived',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Won, self::Lost], true);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'blue',
            self::Won => 'green',
            self::Lost => 'red',
            self::Archived => 'gray',
        };
    }
}
