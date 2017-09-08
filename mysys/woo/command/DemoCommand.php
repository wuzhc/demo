<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 15:14
 */

namespace woo\command;


use woo\controller\Request;

class DemoCommand extends Command
{
    public function doExecute(Request $request)
    {
        echo 'demo command';
    }
}