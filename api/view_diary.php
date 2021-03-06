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
        report_error(1, "该日记不存在");
    }
} else {
    if ($share_key != $result["share_key"]) {
        report_error(2, "该共享不存在");
    }
}

$images = null;
$tags = null;
if (strlen($result["images"]) > 0) {
    $images = explode(" | ", $result["images"]);
}
if (strlen($result["tags"]) > 0) {
    $tags = explode(" | ", $result["tags"]);
}
$return = array(
    "emotion" => $result["emotion"],
    "selfie" => $result["selfie"],
    "images" => $images,
    "tags" => $tags,
    "text" => $result["text"],
    "place_name" => $result["place_name"],
    "place_long" => $result["place_long"],
    "place_lat" => $result["place_lat"],
    "weather" => $result["weather"],
    "create_time" => $result["create_time"],
    "is_shared" => (strlen($result["share_key"]) > 0),
);
report_success($return);
