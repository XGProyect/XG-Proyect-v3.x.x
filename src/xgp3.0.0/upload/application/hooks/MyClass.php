<?php

class MyClass extends XGPCore
{
    public function MyMethod($params = array())
    {
        $query = parent::$db->queryFetch('SELECT `user_name` FROM ' . USERS . ' WHERE user_id = 1');

        echo $query['user_name'] . ' likes: ' . $params[0] . ', ' . $params[1] . ' and ' . $params[2];

        echo '<br/>Yeah!';
    }
}
/* end of MyClass.php */
