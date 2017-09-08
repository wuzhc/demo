<?php

class U
{
    public static $s;

    private function __construct()
    {

    }

    public static function w()
    {
        if(!self::$s){
            self::$s=new U();
        }
        return self::$s;
    }

    public function p()
    {
        echo 'µ¥ÀýÄ£Ê½';
    }

    private function __clone()
    {

    }
}

$u = U::w();
$u->p();