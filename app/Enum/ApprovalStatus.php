<?php

namespace App\Enum;

enum ApprovalStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::APPROVED->value => 'Disetujui',
            self::REJECTED->value => 'Ditolak',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            ApprovalStatus::PENDING => 'Pending',
            ApprovalStatus::APPROVED => 'Disetujui',
            ApprovalStatus::REJECTED => 'Ditolak',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            ApprovalStatus::PENDING => 'heroicon-o-clock',
            ApprovalStatus::APPROVED => 'heroicon-o-check-circle',
            ApprovalStatus::REJECTED => 'heroicon-o-x-circle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            ApprovalStatus::PENDING => 'warning',
            ApprovalStatus::APPROVED => 'success',
            ApprovalStatus::REJECTED => 'danger',
        };
    }
}
