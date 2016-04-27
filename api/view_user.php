<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/27
 * Time: 上午10:54
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));
$name = filter($con, $_POST["name"]);

if (strlen($name) == 0) {
    report_error(ERROR_MISSING_PARAMETER);
}

$result = $con->query("SELECT * FROM user WHERE name = '$name'");
check_sql_error($con);
if (mysqli_affected_rows($con) == 0) {
    report_error(1, "该用户不存在");
}
$result = mysqli_fetch_array($result);
$return = array(
    "name" => $result["name"],
    "sex" => $result["sex"],
    "icon" => $result["icon"],
);
if ($userid == $result["userid"]) { // 自己才能查看邮箱
    $return["email"] = $result["email"];
}

report_success($return);
