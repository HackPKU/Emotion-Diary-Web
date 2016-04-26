<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/25
 * Time: 上午10:52
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();

$name = filter($con, $_POST["name"]);
$password = filter($con, $_POST["password"]);
$sex = filter($con, $_POST["sex"]);
$email = filter($con, $_POST["email"]);
$icon = filter($con, $_POST["icon"]);

if (strlen($name) == 0 || strlen($password) == 0) {
    report_error(ERROR_MISSING_PARAMETER);
}

if (contain_special_chars($name)) {
    report_error(1, "用户名含有特殊字符");
}

if (strlen($name) > 32) {
    report_error(2, "用户名过长");
}

if (!($sex == "male" || $sex == "female")) {
    $sex = "secret";
}

if (strlen($email) > 0 && !is_email($email)) {
    report_error(3, "邮箱格式错误");
}

if (strlen($email) > 128) {
    report_error(4, "邮箱过长");
}

if (!is_random_string($icon, 8)) {
    report_error(5, "图片名不正确");
}

$con->query("SELECT * FROM user WHERE name = '$name'");
check_sql_error($con);
if (mysqli_affected_rows($con) > 0) {
    report_error(6, "该用户名已被注册");
}

if (strlen($email) > 0) {
    $con->query("SELECT * FROM user WHERE email = '$email'");
    check_sql_error($con);
    if (mysqli_affected_rows($con) > 0) {
        report_error(7, "该邮箱已被注册");
    }
}

$con->query("INSERT INTO user (name, password, sex, email, icon) VALUES ('$name', '$password', '$sex', '$email', '$icon')");
check_sql_error($con);
$type = filter($con, $_POST["type"]);
$result = request_post("/login.php", array("name" => $name, "password" => $password, "type" => $type));
report_success(array("userid" => $result["data"]["userid"], "token" => $result["data"]["token"]));

