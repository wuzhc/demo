<?php

class DoJob
{
    public static function job( $param = array() )
    {
        $time = time();
        echo "Time: {$time}, Func: ".get_class()."::".__FUNCTION__."(".json_encode($param).")\n";
    }
}