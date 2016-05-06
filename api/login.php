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
check_version();
$con = db_connect();

$name = filter($con, $_POST["name"]);
$email = filter($con, $_POST["email"]);
$password = filter($con, $_POST["password"]);
$platform = filter($con, $_POST["platform"]);

if ((strlen($name) == 0 && strlen($email) == 0) || strlen($password) == 0) {
    report_error(ERROR_MISSING_PARAMETER);
}

if (!($platform == "iOS" || $platform == "Android" || $platform == "Web")) {
    $platform = "Unknown";
}

$result = null;
if (strlen($name) > 0) {
    $result = $con->query("SELECT * FROM user WHERE name = '$name'");
} else {
    $result = $con->query("SELECT * FROM user WHERE email = '$email'");
}
check_sql_error($con);
if (mysqli_affected_rows($con) == 0) {
    report_error(1, "用户名不存在");
}
$result = mysqli_fetch_array($result);
if (md5_password($password) != strtoupper($result["password"])) {
    report_error(2, "密码错误");
}

$userid = $result["userid"];
$token = null;
$name = $result["name"];
do {
    $token = random_string();
    $con->query("SELECT * FROM token WHERE token = '$token'");
    check_sql_error($con);
} while (mysqli_affected_rows($con) > 0);
$con->query("INSERT INTO token (token, userid, platform) VALUES ('$token', '$userid', '$platform')");
check_sql_error($con);
report_success(array("userid" => $userid, "token" => $token, "name" => $name));
