<?php

/**
 * 公用函数类库
 *
 * @author Changfeng Ji <jichangfeng.bj@acewill.cn>
 */
class Unit {

    /**
     * 封装数据为用于 AJAX 输出的 JSON 格式字符串
     * 
     * 备注：为兼容旧版本，返回结果中包含 success，当 code 为 0 时 success 为 true，否则 success 为 false
     * 
     * @param int $code 状态码，值为 0 时表示执行成功，其它值则为错误状态码。默认为 0
     * @param string $msg 状态信息
     * @param mixed $data 数据
     * @param boolean $captureOutput 是否捕获输出，若为 true 则返回输出结果，否则直接输出并退出程序。默认为 false
     * @return string JSON 格式字符串
     * @author Changfeng Ji <jichangfeng.bj@acewill.cn>
     */
    public static function jsonExit($code = 0, $msg = '', $data = '', $captureOutput = false) {
        $value = array('code' => $code, 'msg' => $msg, 'data' => $data);
        $value['success'] = $code == 0 ? true : false;
        if ($captureOutput) {
            return $value;
        } else {
            exit(json_encode($value));
        }
    }

}
