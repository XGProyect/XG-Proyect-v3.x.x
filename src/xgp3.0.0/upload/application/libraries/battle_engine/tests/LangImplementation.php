<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct($name)
    {
        require(OPBEPATH."tests/runnable/langs/$name.php");
        $this->lang = $lang;    
    }
    
    public function getShipName($id)
    {
        return isset($this->lang['tech_rc'][$id])? $this->lang['tech_rc'][$id] : $id.' <font color=red>* no lang found</font>';
    }
}
?>