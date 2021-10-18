<?php

/**
 * 获取一个唯一的ID字符串
 * @param boolean $uuid 是否是标准36位UUID字符串，默认为 FALSE，表示26位Base32字符串
 * @return string 返回一个唯一的ID字符串
 * @author Changfeng Ji <jichf@qq.com>
 */
function uuid($uuid = false) {
    $obj = Symfony\Component\Uid\Uuid::v4();
    return $uuid ? $obj->toRfc4122() : $obj->toBase32();
}
