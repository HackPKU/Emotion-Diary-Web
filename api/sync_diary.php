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
report_success(array("diaries" => create_mini_diary($result)));
