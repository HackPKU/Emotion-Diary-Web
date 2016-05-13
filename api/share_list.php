<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/13
 * Time: 下午6:05
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));

$result = $con->query("SELECT * FROM diary WHERE userid = '$userid' AND LENGTH(share_key) > 0 ORDER BY create_time");
check_sql_error($con);
$return = array();
while ($diary = mysqli_fetch_array($result)) {
    $mini_diary = array();
    $mini_diary["diaryid"] = $diary["diaryid"];
    $mini_diary["emotion"] = $diary["emotion"];
    $mini_diary["selfie"] = $diary["selfie"];
    $mini_diary["has_image"] = (strlen($diary["images"]) > 0);
    $mini_diary["has_tag"] = (strlen($diary["tags"]) > 0);
    $mini_diary["short_text"] = substr($diary["text"], 0, 140);
    $mini_diary["place_name"] = $diary["place_name"];
    $mini_diary["weather"] = $diary["weather"];
    $mini_diary["create_time"] = $diary["create_time"];
    array_push($return, $mini_diary);
}
report_success(array("diaries" => $return));