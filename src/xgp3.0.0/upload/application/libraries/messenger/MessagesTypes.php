<?php
/**
 * Messenger Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */

namespace application\libraries\messenger;

/**
 * MessagesTypes Class
 *
 * @category Enumerator
 * @package  Libraries
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
abstract class MessagesTypes
{
    const espio   = 0;
    const combat  = 1;
    const exp     = 2;
    const ally    = 3;
    const user    = 4;
    const general = 5;
}

/* end of MessagesTypes.php */
