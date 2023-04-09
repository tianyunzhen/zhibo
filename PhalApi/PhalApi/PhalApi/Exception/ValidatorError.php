<?php
/**
 * PhalApi_Exception_InternalServerError 参数验证异常错误
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-05
 */

class PhalApi_Exception_ValidatorError extends PhalApi_Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            T('{message}', array('message' => $message)), 422 + $code
        );
    }
}
