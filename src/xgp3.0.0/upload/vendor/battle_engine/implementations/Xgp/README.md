Replace all content of function includes/classes/class.FlyingFleetHandler.php::missionCaseAttack() with 

   ```php
      $opbePath = XGP_ROOT.'includes'.DIRECTORY_SEPARATOR.'battle_engine'.DIRECTORY_SEPARATOR; // XGP 2.10.x
      //$opbePath = $GLOBALS['xgp_root'].'includes'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'opbe'.DIRECTORY_SEPARATOR; // XGP 2.9.x
      require_once($opbePath.'implementations'.DIRECTORY_SEPARATOR.'Xgp'.DIRECTORY_SEPARATOR.'missionCaseAttack.php'); 
   ```

where $opbePath is the path where you uploaded the battle engine pack. 
