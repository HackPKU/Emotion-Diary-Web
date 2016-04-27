<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/27
 * Time: 下午12:45
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

$con->query("SELECT * FROM diary WHERE userid = '$userid' AND diaryid = $diaryid");
check_sql_error($con);
if (mysqli_affected_rows($con) == 0) {
    report_error(1, "该日记不存在");
}

$con->query("DELETE FROM diary WHERE diaryid = $diaryid");
check_sql_error($con);
report_success();
