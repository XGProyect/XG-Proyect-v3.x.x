<?php

use application\core\Database;
use application\core\XGPCore;

//HOOK EXAMPLE
class MyClass extends XGPCore
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_db = new Database();
    }
    
    function MyMethod($params = array())
    {
        $query = $this->_db->queryFetch('SELECT `user_name` FROM ' . USERS . ' WHERE user_id = 1');

        echo $query['user_name'] . ' likes: ' . $params[0] . ', ' . $params[1] . ' and ' . $params[2];

        echo '<br/>Yeah!';
    }

}

/* end of MyClass.php */