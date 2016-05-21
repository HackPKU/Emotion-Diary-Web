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

$userid = intval(filter($con, $_POST["userid"]));
$name = filter($con, $_POST["name"]);
$password = filter($con, $_POST["password"]);
$new_password = filter($con, $_POST["new_password"]);
$sex = filter($con, $_POST["sex"]);
$email = filter($con, $_POST["email"]);
$icon = filter($con, $_POST["icon"]);
$personid = filter($con, $_POST["personid"]);
$function = filter($con, $_POST["function"]);

if ($function == "edit") {
    check_login($con);
}

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

if ($function == "edit") {
    $result = $con->query("SELECT * FROM user WHERE userid = '$userid'");
    check_sql_error($con);
    $result = mysqli_fetch_array($result);
    if (md5_password($password, $result["salt"]) != strtoupper($result["password"])) {
        report_error(6, "密码错误");
    }
}

$result = $con->query("SELECT * FROM user WHERE name = '$name'");
check_sql_error($con);
$result = mysqli_fetch_array($result);
if (!($function == "edit" && $userid == $result["userid"]) && mysqli_affected_rows($con) > 0) {
    report_error(7, "该用户名已被注册");
}

if (strlen($email) > 0) {
    $result = $con->query("SELECT * FROM user WHERE email = '$email'");
    check_sql_error($con);
    $result = mysqli_fetch_array($result);
    if (!($function == "edit" && $userid == $result["userid"]) && mysqli_affected_rows($con) > 0) {
        report_error(8, "该邮箱已被注册");
    }
}

if (strlen($personid) > 0) {
    $result = $con->query("SELECT * FROM user WHERE personid = '$personid'");
    check_sql_error($con);
    $result = mysqli_fetch_array($result);
    if (!($function == "edit" && $userid == $result["userid"]) && mysqli_affected_rows($con) > 0) {
        report_error(9, "该人脸已被注册");
    }
}

$salt = random_string(6);
if ($function == "edit") {
    $changed_password = (strlen($new_password) > 0);
    if (!$changed_password) {
        $new_password = $password;
    }
    $md5_password = md5_password($new_password, $salt);
    $con->query("UPDATE user SET name = '$name', password = '$md5_password', salt = '$salt', sex = '$sex', email = '$email', icon = '$icon', personid = '$personid' WHERE userid = '$userid'");
    check_sql_error($con);
    if ($changed_password) {
        $con->query("DELETE * FROM token WHERE userid = '$userid'");
        check_sql_error($con);
    }
} else {
    $md5_password = md5_password($password, $salt);
    $con->query("INSERT INTO user (name, password, salt, sex, email, icon, personid) VALUES ('$name', '$md5_password', '$salt', '$sex', '$email', '$icon', '$personid')");
    check_sql_error($con);
}

if ($function == "edit") {
    report_success();
} else {
    $platform = filter($con, $_POST["platform"]);
    $result = request_post("/login.php", array("name" => $name, "password" => $password, "platform" => $platform));
    report_success(array("userid" => $result["data"]["userid"], "token" => $result["data"]["token"]));
}
