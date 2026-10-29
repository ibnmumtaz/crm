<?php

namespace App\Enums;

enum TargetType: string
{
    case Revenue = 'revenue';
    case DealCount = 'deal_count';
    case CallCount = 'call_count';
}
