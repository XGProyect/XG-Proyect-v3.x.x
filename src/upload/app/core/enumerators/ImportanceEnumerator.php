<?php
/**
 * Importance enumerator
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\core\enumerators;

/**
 * ImportanceEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class ImportanceEnumerator
{
    const unimportant = 0;
    const normal = 1;
    const important = 2;
}
