<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/27
 * Time: 下午3:51
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$image = filter($con, $_POST["image"]);
$type = filter($con, $_POST["type"]);

$max_length = 0;
switch ($type) {
    case "icon":
        $max_length = 50;
        break;
    case "selfie":
        $max_length = 100;
        break;
    case "image":
        $max_length = 200;
        break;
    default:
        report_error(ERROR_ILLEGAL_PARAMETER, "图片类型不合法");
        break;
}
save_image($image, $type, $max_length);
