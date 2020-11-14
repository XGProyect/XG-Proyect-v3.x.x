<?php declare (strict_types = 1);

/**
 * Preferences Enumerator
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
 * PreferencesEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class PreferencesEnumerator
{
    const order = [
        'emergence' => 0,
        'coordinates' => 1,
        'alphabet' => 2,
        'size' => 3,
        'used_fields' => 4,
    ];

    const sequence = [
        'up' => 0,
        'down' => 1,
    ];
}
