<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/27
 * Time: 上午10:32
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$token = filter($con, $_POST["token"]);
$con->query("DELETE FROM token WHERE token = '$token'");
check_sql_error($con);
report_success();