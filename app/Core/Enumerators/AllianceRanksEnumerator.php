<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class AllianceRanksEnumerator
{
    public const DELETE = 1;
    public const KICK = 2;
    public const APPLICATIONS = 3;
    public const VIEW_MEMBER_LIST = 4;
    public const APPLICATION_MANAGEMENT = 5;
    public const ADMINISTRATION = 6;
    public const ONLINE_STATUS = 7;
    public const SEND_CIRCULAR = 8;
    public const RIGHT_HAND = 9;
}
