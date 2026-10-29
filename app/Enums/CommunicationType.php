<?php

namespace App\Enums;

enum CommunicationType: string
{
    case Call = 'call';
    case Email = 'email';
    case Meeting = 'meeting';
    case Note = 'note';
    case Proposal = 'proposal';
    case Other = 'other';
}
