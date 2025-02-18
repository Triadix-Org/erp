<?php

namespace App\Enum;

enum Month: string
{
    case JAN = 'January';
    case FEB = 'February';
    case MAR = 'March';
    case APR = 'April';
    case MAY = 'May';
    case JUN = 'June';
    case JUL = 'July';
    case AUG = 'August';
    case SEP = 'September';
    case OCT = 'October';
    case NOV = 'November';
    case DEC = 'December';

    public static function labels(): array
    {
        return [
            self::JAN->value => 'January',
            self::FEB->value => 'February',
            self::MAR->value => 'March',
            self::APR->value => 'April',
            self::MAY->value => 'May',
            self::JUN->value => 'June',
            self::JUL->value => 'July',
            self::AUG->value => 'August',
            self::SEP->value => 'September',
            self::OCT->value => 'October',
            self::NOV->value => 'November',
            self::DEC->value => 'December',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            Month::JAN => 'January',
            Month::FEB => 'February',
            Month::MAR => 'March',
            Month::APR => 'April',
            Month::MAY => 'May',
            Month::JUN => 'June',
            Month::JUL => 'July',
            Month::AUG => 'August',
            Month::SEP => 'September',
            Month::OCT => 'October',
            Month::NOV => 'November',
            Month::DEC => 'December',
        };
    }
}
