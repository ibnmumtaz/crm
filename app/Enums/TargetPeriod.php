<?php

namespace App\Enums;

use Carbon\Carbon;

enum TargetPeriod: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';

    public function startOf(\DateTimeInterface $date): Carbon
    {
        $c = Carbon::parse($date);

        return match ($this) {
            self::Monthly => $c->copy()->startOfMonth(),
            self::Quarterly => $c->copy()->startOfQuarter(),
        };
    }

    public function endOf(\DateTimeInterface $date): Carbon
    {
        $c = Carbon::parse($date);

        return match ($this) {
            self::Monthly => $c->copy()->endOfMonth(),
            self::Quarterly => $c->copy()->endOfQuarter(),
        };
    }
}
