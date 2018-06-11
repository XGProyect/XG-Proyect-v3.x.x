<?php
/**
 * User entity
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

/**
 * UserEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class UserEntity
{

    /**
     *
     * @var array
     */
    private $_user = [];

    /**
     * Init with the current user data
     * 
     * @param array $curret_user Current User
     */
    public function __construct($curret_user)
    {
        $this->setUser($curret_user);
    }

    /**
     * Set the current user
     * 
     * @param array $curret_user Current User
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function setUser($user)
    {
        try {

            if (!is_array($user)) {
                
                return  null;
            }

            $this->_user = $user;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
}

/* end of UserEntity.php */
