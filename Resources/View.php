<?php

namespace Resources;

class View{

    private static $listFile = [];

    private static function load(){

        $folders = array_filter(
            scandir(__DIR__), 
            function($value){ 
                return !in_array($value, array(".", "..", "View.php")); 
            }
        );

        foreach($folders as $folder){
            self::$listFile[$folder] = array_filter(
                scandir(__DIR__.DIRECTORY_SEPARATOR.$folder), 
                function($value){ 
                    return !in_array($value, array(".", "..")); 
                }
            );
        }
    }
    
    public function __construct($dir = "folder@file", Array $params = array()){

        self::load();

        $file = explode("@", $dir);
        $file = __DIR__ . DIRECTORY_SEPARATOR. $file[0]. DIRECTORY_SEPARATOR . $file[1];

        if(file_exists($file)){
            require_once $file;
        }
    }

}