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
    report_error(-1, "参数缺失");
}

if (contain_special_chars($name)) {
    report_error(2, "用户名含有特殊字符");
}

if (strlen($password) < 6) {
    report_error(3, "密码过于简单");
}
if (!($sex == "male" || $sex == "female")) {
    $sex = "secret";
}

if (strlen($email) > 0 && !is_email($email)) {
    report_error(4, "邮箱格式错误");
}

$con->query("SELECT * FROM user WHERE name = '$name'");
check_sql_error($con);
if (mysqli_affected_rows($con) > 0) {
    report_error(1, "该用户名已被使用");
}

$con->query("INSERT INTO user (name, password, sex, email, icon) VALUES ('$name', '$password', '$sex', '$email', '$icon')");
check_sql_error($con);
$type = filter($con, $_POST["type"]);
$result = request_post("/login.php", array("name" => $name, "password" => $password, "type" => $type));
report_success(array("token" => $result["data"]["token"]));

