<?php

namespace App\Enum\Employee;

enum ApplyStatus: int
{
    case APPLIED = 0;
    case SCREENING = 1;
    case EXAM = 2;
    case INTERVIEW = 3;
    case FINALINTERVIEW = 4;
    case OFFERING = 5;
    case DONE = 6;
    case REJECTED = 7;

    public static function labels(): array
    {
        return [
            self::APPLIED->value => 'Applied',
            self::SCREENING->value => 'Screening',
            self::EXAM->value => 'Test',
            self::INTERVIEW->value => 'HR Interview',
            self::FINALINTERVIEW->value => 'User Interview',
            self::OFFERING->value => 'Offering',
            self::DONE->value => 'Done',
            self::REJECTED->value => 'Reject',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            ApplyStatus::APPLIED => 'Applied',
            ApplyStatus::SCREENING => 'Screening',
            ApplyStatus::EXAM => 'Test',
            ApplyStatus::INTERVIEW => 'HR Interview',
            ApplyStatus::FINALINTERVIEW => 'User Interview',
            ApplyStatus::OFFERING => 'Offering',
            ApplyStatus::DONE => 'Done',
            ApplyStatus::REJECTED => 'Reject',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::APPLIED => 'gray',
            self::SCREENING => 'info',
            self::EXAM => 'info',
            self::INTERVIEW => 'warning',
            self::FINALINTERVIEW => 'warning',
            self::OFFERING => 'success',
            self::DONE => 'success',
            self::REJECTED => 'danger',
        };
    }
}
