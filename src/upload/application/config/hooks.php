<?php
/**
 * Hooks
 *
 * PHP Version 7.1+
 *
 * @category Config
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

/**
 * MODES
 * before_loads
 * before_page
 * new_page
 */
// INSERT HOOKS AFTER THIS LINE
$hook['before_page'] = [
    'class' => 'MyClass',
    'function' => 'myMethod',
    'filename' => 'MyClass.php',
    'filepath' => 'hooks',
    'params' => ['beer', 'wine', 'snacks'],
];

/**
 * New mods/hooks have to be added like this:
 * $hook['before_loads'][]
 * $hook['before_page'][]
 * $hook['new_page'][]
 */

// INSERT HOOKS BEFORE THIS LINE
/* end of hooks.php */
