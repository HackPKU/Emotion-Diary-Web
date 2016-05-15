<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/15
 * Time: 下午2:02
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

/**
 * @param string $dir Request URL directory relative to api directory
 * @param array $post Request parameters
 * @return array Request result
 */
function api_post($dir, $post) {
    $post["version"] = 1.0;
    $post["platform"] = "Web";
    $post["userid"] = $_COOKIE["userid"];
    $post["token"] = $_COOKIE["token"];
    $url = dirname(dirname(dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]))) . "/api" . $dir;
    $options = array('http' => array(
        'method' => 'POST',
        'content' => http_build_query($post),
    ),
    );
    $result = file_get_contents($url, false, stream_context_create($options));
    return json_decode($result, true); // 以 array 返回json解码的数据
}
