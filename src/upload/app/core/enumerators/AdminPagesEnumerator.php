<?php
/**
 * Admin pages enumerator
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace App\core\enumerators;

/**
 * AdminPagesEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
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
