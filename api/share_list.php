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
report_success(array("diaries" => create_mini_diary($result)));
