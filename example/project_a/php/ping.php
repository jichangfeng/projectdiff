<?php

/**
 * Ping connection to network host
 * 
 * @param string $ip IP address
 * @param int $port Port number
 * @param float $timeout Timeout to use for connection. If not set the timeout set in php.ini will be used: ini_get("default_socket_timeout")
 * @return int 1 if the connection establishment; 0 otherwise.
 * @author Changfeng Ji <jichf@qq.com>
 */
function ping($ip, $port, $timeout = null) {
    $stream = @stream_socket_client($ip . ':' . $port, $errno, $errstr, $timeout ? $timeout : ini_get("default_socket_timeout"));
    if (!$stream) {
        return 0;
    } else {
        @stream_socket_shutdown($stream, STREAM_SHUT_RDWR);
        return 1;
    }
}
