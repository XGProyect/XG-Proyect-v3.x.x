<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }
/**
 * MODES
 * before_loads
 * before_page
 * new_page
 */
// INSERT HOOKS AFTER THIS LINE
$hook['before_page'] = array(
                                'class'    => 'MyClass',
                                'function' => 'MyMethod',
                                'filename' => 'MyClass.php',
                                'filepath' => 'hooks',
                                'params'   => array('beer', 'wine', 'snacks')
                                );


// INSERT HOOKS BEFORE THIS LINE
/* end of hooks.php */