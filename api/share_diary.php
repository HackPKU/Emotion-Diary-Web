<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/26
 * Time: 下午11:23
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));
$diaryid = intval(filter($con, $_POST["diaryid"]));

if (strlen($userid) == 0 || strlen($diaryid) == 0) {
    report_error(ERROR_MISSING_PARAMETER);
}

$result = $con->query("SELECT * FROM diary WHERE diaryid = $diaryid");
check_sql_error($con);
$result = mysqli_fetch_array($result);

if (strlen($result["share_key"]) > 0) {
    report_error(1, "该日记已分享");
}
if ($userid != $result["userid"]) {
    report_error(2, "这不是您的日记");
}

$share_key = random_string(8);
$con->query("UPDATE diary SET share_key = '$share_key' WHERE diaryid = $diaryid");
check_sql_error($con);
report_success(array("share_key" => $share_key));