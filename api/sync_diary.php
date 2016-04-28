<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/27
 * Time: 下午1:44
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));
$year = intval(filter($con, $_POST["year"]));
$month = intval(filter($con, $_POST["month"]));

if ($year < 2016 || $year > date("Y", time())) {
    report_error(1, "年份不正确");
}
if ($month < 0 || $month > 12) { // month = 0 获取全年数据
    report_error(2, "月份不正确");
}

$conditions = "YEAR(create_time) = '$year'";
if ($month > 0) {
    $conditions .= " AND MONTH(create_time) = '$month'";
}
$result = $con->query("SELECT * FROM diary WHERE userid = '$userid' AND $conditions ORDER BY create_time");
check_sql_error($con);
$return = array();
while ($diary = mysqli_fetch_array($result)) {
    $mini_diary = array();
    $mini_diary["diaryid"] = $diary["diaryid"];
    $mini_diary["emotion"] = $diary["emotion"];
    $mini_diary["selfie"] = $diary["selfie"];
    $mini_diary["has_image"] = explode(" | ", $diary["images"]) > 0;
    $mini_diary["has_tag"] = explode(" | ", $diary["tags"]) > 0;
    $mini_diary["short_text"] = substr($diary["text"], 0, 140);
    $mini_diary["place_name"] = $diary["place_name"];
    $mini_diary["weather"] = $diary["weather"];
    $mini_diary["create_time"] = $diary["create_time"];
    array_push($return, $mini_diary);
}
report_success($return);