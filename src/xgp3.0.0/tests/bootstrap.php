<?php
define('PATH_BASE', '../upload/application');

chdir(PATH_BASE);

spl_autoload_register(function($name) {

    // Autoload anything in the mywebapplication namespace
    if (0 === strpos("application\\", $name)) {
        $name = strreplace("\\", '/', $name);
        require $name;
    }

}, true, true);