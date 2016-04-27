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

$diaryid = intval(filter($con, $_POST["diaryid"]));
$userid = intval(filter($con, $_POST["userid"]));
$emotion = intval(filter($con, $_POST["emotion"]));
$selfie = filter($con, $_POST["selfie"]);
$images = filter($con, $_POST["images"]);
$tags = filter($con, $_POST["tags"]);
$text = filter($con, $_POST["text"], false);
$location_name = filter($con, $_POST["location_name"]);
$location_long = floatval(filter($con, $_POST["location_long"]));
$location_lat = floatval(filter($con, $_POST["location_lat"]));
$weather = filter($con, $_POST["weather"]);
$function = filter($con, $_POST["function"]);

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
if (strlen($location_name) > 128) {
    report_error(7, "地点过长");
}
if ($location_lat < -90 || $location_lat > 90 || $location_long < -180 || $location_long > 180) {
    report_error(8, "地理位置不正确");
}
if (strlen($weather) > 32) {
    report_error(9, "天气过长");
}

if ($function == "edit") {
    $con->query("SELECT * FROM diary WHERE userid = '$userid' AND diaryid = $diaryid");
    check_sql_error($con);
    if (mysqli_affected_rows($con) == 0) {
        report_error(10, "该日记不存在");
    }
    $updates = "emotion = '$emotion', selfie = '$selfie', images = '$image', tags = '$tags', text = '$text'";
    $updates .= ", location_name = '$location_name', location_long = '$location_long', location_lat = '$location_lat', weather = '$weather'";
    $con->query("UPDATE diary SET $updates WHERE diaryid = '$diaryid'");
    check_sql_error($con);
    report_success();
} else {
    $cols = "(userid, emotion, selfie, images, tags, text, location_name, location_long, location_lat, weather)";
    $vals = "('$userid', '$emotion', '$selfie', '$images', '$tags', '$text', '$location_name', '$location_long', '$location_lat', '$weather')";
    $con->query("INSERT INTO diary $cols VALUES $vals");
    check_sql_error($con);
    report_success(array("diaryid" => mysqli_insert_id($con)));
}
