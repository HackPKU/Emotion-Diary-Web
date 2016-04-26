<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/26
 * Time: 下午7:41
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();

$userid = intval(filter($con, $_POST["userid"]));
$diaryid = intval(filter($con, $_POST["diaryid"]));
$share_key = filter($con, $_POST["share_key"]);

// share_key 存在时表明是共享的日记
if (strlen($share_key) == 0) {
    check_login($con);
} else {
    if (!is_random_string($share_key, 8)) {
        report_error(2, "该共享不存在");
    }
}

$result = $con->query("SELECT * FROM diary WHERE diaryid = $diaryid");
check_sql_error($con);
$result = mysqli_fetch_array($result);

if (strlen($share_key) == 0) {
    if ($userid != $result["userid"]) {
        report_error(1, "这不是您的日记");
    }
} else {
    if ($share_key != $result["share_key"]) {
        report_error(2, "该共享不存在");
    }
}

$images = explode(" | ", $result["images"]);
$tags = explode(" | ", $result["tags"]);
$return = array(
    "emotion" => $result["emotion"],
    "selfie" => $result["selfie"],
    "images" => $images,
    "tags" => $tags,
    "text" => $result["text"],
    "location_name" => $result["location_name"],
    "location_long" => $result["location_long"],
    "location_lat" => $result["location_lat"],
    "weather" => $result["weather"],
    "create_time" => $result["create_time"],
    "edit_time" => $result["edit_time"]
);
report_success($return);
