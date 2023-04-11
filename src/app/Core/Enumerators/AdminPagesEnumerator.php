<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class AdminPagesEnumerator
{
    public const SECTIONS = [
        'configuration',
        'information',
        'edition',
        'tools',
        'maintenance',
    ];

    public const CONFIGURATION = [
        'server',
        'modules',
        'planets',
        'registration',
        'statistics',
        'premium',
    ];

    public const INFORMATION = [
        'tasks',
        'errors',
        'fleets',
        'messages',
    ];

    public const EDITION = [
        'maker',
        'users',
        'alliances',
        'languages',
        'changelog',
        'permissions',
    ];

    public const TOOLS = [
        'backup',
        'encrypter',
        'announcement',
        'ban',
        'rebuildhighscores',
        'update',
        'migrate',
    ];

    public const MAINTENANCE = [
        'repair',
        'reset',
    ];
}
