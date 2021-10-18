<?php

/**
 * 获取一个唯一的ID字符串
 * @param boolean $uuid 是否是标准36位UUID字符串，默认为 FALSE，表示移除连字号（-）的32位字符串
 * @return string 返回一个唯一的ID字符串
 * @author Changfeng Ji <jichf@qq.com>
 */
function uuid($uuid = false) {
    $charid = strtoupper(md5(uniqid(rand(), true)));
	$hyphen = chr(45);
	$guid = substr($charid, 0, 8) . $hyphen
			. substr($charid, 8, 4) . $hyphen
			. substr($charid, 12, 4) . $hyphen
			. substr($charid, 16, 4) . $hyphen
			. substr($charid, 20, 12);
    if (!$uuid) {
        $guid = strtr($guid, array('-' => ''));
    }
    return trim($guid, '{}');
}
