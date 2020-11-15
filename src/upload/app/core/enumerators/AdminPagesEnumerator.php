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
    const SECTIONS = [
        'configuration',
        'information',
        'edition',
        'tools',
        'maintenance',
    ];

    const CONFIGURATION = [
        'server',
        'modules',
        'planets',
        'registration',
        'statistics',
        'premium',
    ];

    const INFORMATION = [
        'tasks',
        'errors',
        'fleets',
        'messages',
    ];

    const EDITION = [
        'maker',
        'users',
        'alliances',
        'languages',
        'changelog',
        'permissions',
    ];

    const TOOLS = [
        'backup',
        'encrypter',
        'announcement',
        'ban',
        'rebuildhighscores',
        'update',
        'migrate',
    ];

    const MAINTENANCE = [
        'repair',
        'reset',
    ];
}
