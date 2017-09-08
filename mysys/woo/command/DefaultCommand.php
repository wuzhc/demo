<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 14:35
 */

namespace woo\command;



use woo\controller\Request;

class DefaultCommand extends Command
{

    public function doExecute(Request $request)
    {
        $request->addFeedback('welcome to woo');
        echo 'hello world';
        include 'woo/view/main.php';
    }

}