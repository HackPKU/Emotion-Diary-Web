<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/25
 * Time: 下午7:55
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
$con = db_connect();

$name = filter($con, $_POST["name"]);
$password = filter($con, $_POST["password"]);
$type = filter($con, $_POST["type"]);

if (strlen($name) == 0 || strlen($password) == 0) {
    report_error(-1, "参数缺失");
}

if (!($type == "ios" || $type == "android" || $type == "web")) {
    $type = "unknown";
}

$result = $con->query("SELECT * FROM user WHERE name = '$name'");
check_sql_error($con);
if (mysqli_affected_rows($con) == 0) {
    report_error(1, "用户名不存在");
}
$result = mysqli_fetch_array($result);
if ($password != $result["password"]) {
    report_error(2, "密码错误");
}

$userid = $result["userid"];
$token = random_string();
$con->query("INSERT INTO token (token, userid, type) VALUES ('$token', '$userid', '$type')");
check_sql_error($con);
report_success(array("token" => $token));
