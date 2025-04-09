<?php

namespace App\Enum\Accounting;

enum JournalStatus: int
{
    case UNPOSTED = 0;
    case POSTED = 1;

    public static function labels(): array
    {
        return [
            self::UNPOSTED->value => 'Unposted',
            self::POSTED->value => 'Posted',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            JournalStatus::UNPOSTED => 'Unposted',
            JournalStatus::POSTED => 'Posted',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPOSTED => 'warning',
            self::POSTED => 'success',
        };
    }
}
