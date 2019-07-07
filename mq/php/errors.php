<?php

/**
 * 错误类
 * Class Errors
 * Usage:
 * $err = Errors::newErr();
 * $err->msg = 'xxx';
 * $err->code = 1;
 * Or:
 * $err = Errors::newErr('xxx', 1);
 *
 * @author wuzhc 20190516
 */
class Errors
{
    public $msg;
    public $code;
    public $data;

    /**
     * Errors constructor.
     *
     * @param string $msg
     * @param int    $code
     * @param array  $data
     * @author wuzhc 20190516
     */
    public function __construct($msg = '', $code = 1, $data = [])
    {
        $this->msg = $msg;
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * @param string $msg
     * @param int    $code
     * @return Errors
     * @author wuzhc 20190516
     */
    public static function newErr($msg = '', $code = 1)
    {
        return new self($msg, $code);
    }

    /**
     * 参数错误
     *
     * @param string $msg
     * @return Errors
     */
    public static function newParamErr($msg = '')
    {
        return new self($msg, 21);
    }

    /**
     * @return string
     * @author wuzhc 20190516
     */
    public function __toString()
    {
        if ($this->msg) {
            return 'Error:' . $this->msg . ', Code: ' . $this->code;
        } else {
            return '';
        }
    }
}