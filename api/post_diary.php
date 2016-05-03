<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/26
 * Time: 下午4:32
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));
$emotion = intval(filter($con, $_POST["emotion"]));
$selfie = filter($con, $_POST["selfie"]);
$images = filter($con, $_POST["images"]);
$tags = filter($con, $_POST["tags"]);
$text = filter($con, $_POST["text"], false);
$place_name = filter($con, $_POST["place_name"]);
$place_long = floatval(filter($con, $_POST["place_long"]));
$place_lat = floatval(filter($con, $_POST["place_lat"]));
$weather = filter($con, $_POST["weather"]);
$create_time = filter($con, $_POST["create_time"]);

if ($emotion < 0 || $emotion > 100) {
    report_error(1, "心情值不正确");
}
if (!is_random_string($selfie, 8)) {
    report_error(2, "图片名不正确");
}
if (count(explode(" | ", $images)) > 9) {
    report_error(3, "图片数目过多");
}
foreach (explode(" | ", $images) as $image) {
    if (!is_random_string($image, 8)) {
        report_error(2, "图片名不正确");
    }
}
if (strlen($tags) > 512 || count(explode(" | ", $tags)) > 20) {
    report_error(4, "标签数目过多或过长");
}
if (strlen($text) == 0) {
    report_error(5, "正文不能为空");
}
if (strlen($text) > 65535) {
    report_error(6, "正文过长");
}
if (strlen($place_name) > 128) {
    report_error(7, "地点过长");
}
if ($place_lat < -90 || $place_lat > 90 || $place_long < -180 || $place_long > 180) {
    report_error(8, "地理位置不正确");
}
if (strlen($weather) > 32) {
    report_error(9, "天气过长");
}
if (strlen($create_time) > 0) {
    if (!strtotime($create_time)) {
        report_error(10, "时间不正确");
    }
} else {
    $create_time = date("Y-m-d G:i:s", time());
}

$cols = "(userid, emotion, selfie, images, tags, text, place_name, place_long, place_lat, weather, create_time)";
$vals = "('$userid', '$emotion', '$selfie', '$images', '$tags', '$text', '$place_name', '$place_long', '$place_lat', '$weather', '$create_time')";
$con->query("INSERT INTO diary $cols VALUES $vals");
check_sql_error($con);
report_success(array("diaryid" => mysqli_insert_id($con)));
