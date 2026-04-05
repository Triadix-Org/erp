<?php

namespace App\Enum\HumanResource;

enum DayOffStatus: int
{
    case SUMMITED = 0;
    case WAITING_APPROVAL = 1;
    case APPROVED = 2;
    case REJECTED = 3;

    public static function labels(): array
    {
        return [
            self::SUMMITED->value => 'Diajukan',
            self::WAITING_APPROVAL->value => 'Menunggu Persetujuan',
            self::APPROVED->value => 'Disetujui',
            self::REJECTED->value => 'Ditolak',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::SUMMITED => 'Diajukan',
            self::WAITING_APPROVAL => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }
}
