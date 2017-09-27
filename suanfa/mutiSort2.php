<?php
/**
 * array_mutisort 多维数组排序
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月27日
 * Time: 16:40
 */

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

$data[] = array('volume' => 67, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 1);
$data[] = array('volume' => 85, 'edition' => 6);
$data[] = array('volume' => 98, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 6);
$data[] = array('volume' => 67, 'edition' => 7);

// Pass the array, followed by the column names and sort flags
$sorted = array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
print_r($sorted);