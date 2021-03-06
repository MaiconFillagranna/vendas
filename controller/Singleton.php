<?php

abstract class Singleton {
    
    protected function __construct() {}
    
    protected function __clone() {}

    public static function getInstance() {
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass();
        }

        return $instances[$calledClass];
    }
    
}

