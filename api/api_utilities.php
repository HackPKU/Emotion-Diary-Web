<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/25
 * Time: 下午2:27
 */

//** Error codes. **//
define('ERROR_UNKNOWN', -110);
define('ERROR_ILLEGAL_PARAMETER', -100);
define('ERROR_SERVER_ERROR', -50);
define('ERROR_VERSION_NOT_SUPPORTED', -10);
define('ERROR_LOGIN_CHECK_FAILED', -5);
define('ERROR_MISSING_PARAMETER', -1);

require_once '../db_connect.php';

/**
 * Stop reporting errors to client
 */
if (!EMOTION_DIARY_REPORT_ERRORS) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

/**
 * @param float $minVersion Min supported version.
 */
function check_version($minVersion = EMOTION_DIARY_MIN_VERSION) {
    $version = floatval($_POST["version"]);
    if ($version < $minVersion) {
        report_error(ERROR_VERSION_NOT_SUPPORTED);
    }
}

/**
 * @param mysqli $con Database connection
 */
function check_login($con) {
    $token = filter($con, $_POST["token"]);
    $userid = intval(filter($con, $_POST["userid"]));
    $platform = filter($con, $_POST["platform"]);
    if (strlen($token) == 0 || strlen($userid) == 0 || strlen($platform) == 0) {
        report_error(ERROR_MISSING_PARAMETER);
    }
    $result = $con->query("SELECT * FROM token WHERE userid = '$userid' AND token = '$token'");
    check_sql_error($con);
    if (mysqli_affected_rows($con) == 0) {
        report_error(ERROR_LOGIN_CHECK_FAILED);
    }
    $result = mysqli_fetch_array($result);
    $time = strtotime($result["latest_time"]) - time();
    if ($platform == "Unknown") {
        $time += 24 * 3600; // 1天
    } else if ($platform == "Web") {
        $time += 3 * 24 * 3600; // 3天
    } else if ($platform == "iOS" || $platform == "Android") {
        $time += 15 * 24 * 3600; // 15天
    }
    if ($time < 0) {
        report_error(ERROR_LOGIN_CHECK_FAILED, "会话超时, 请重新登录");
    }

    // 更新 token
    $nowTime = date("Y-m-d G:i:s", time());
    $con->query("UPDATE token set latest_time = '$nowTime' WHERE token = '$token'");
    check_sql_error($con);
}

/**
 * Filter the HTTP request to prevent SQL injection
 * @param mysqli $con Database connection
 * @param string $data The data to be filtered
 * @param bool $report_error Whether php should report potential injection
 * @return string Filtered data
 */
function filter($con, $data, $report_error = true) {
    $safe_data = mysqli_real_escape_string($con, $data);
    if ($report_error && $data != $safe_data) {
        report_error(ERROR_ILLEGAL_PARAMETER);
    }
    return $safe_data;
}

/**
 * @param int $code Error code
 * @param string $message Error message
 * @param bool $should_exit Whether php should exit after reporting the error
 */
function report_error($code = ERROR_UNKNOWN, $message = "", $should_exit = true) {
    if ($code == 0) { // 0 为成功代码
        $code = ERROR_UNKNOWN;
    }
    if (strlen($message) == 0) {
        switch ($code) {
            case ERROR_UNKNOWN:
                $message = "未知错误";
                break;
            case ERROR_ILLEGAL_PARAMETER:
                $message = "参数中含有非法字符";
                break;
            case ERROR_SERVER_ERROR:
                $message = "服务器错误";
                break;
            case ERROR_VERSION_NOT_SUPPORTED:
                $message = "客户端版本过低，请升级";
                break;
            case ERROR_LOGIN_CHECK_FAILED:
                $message = "尚未登录";
                break;
            case ERROR_MISSING_PARAMETER:
                $message = "参数缺失";
                break;
            default:
                break;
        }
    }
    echo json_encode(array("code" => $code, "message" => $message));
    if ($should_exit) {
        exit();
    }
}

/**
 * @param mysqli $con Database connection
 * @param bool $should_exit Whether php should exit after reporting the error
 */
function check_sql_error($con, $should_exit = true) {
    if (mysqli_error($con)) {
        $message = "";
        if (EMOTION_DIARY_REPORT_ERRORS) {
            $message = mysqli_error($con);
        }
        echo json_encode(array("code" => ERROR_SERVER_ERROR, "message" => $message));
        if ($should_exit) {
            exit();
        }
    }
}

/**
 * @param mixed $data Data to return
 */
function report_success($data = null) {
    echo json_encode(array("code" => 0, "data" => $data));
}

/**
 * @param string $dir Request URL directory relative to current directory
 * @param array $post Request parameters
 * @return array Request result
 */
function request_post($dir, $post) {
    $post["version"] = $_POST["version"];
    $post["userid"] = $_POST["userid"];
    $post["token"] = $_POST["token"];
    $url = dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . $dir;
    $options = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($post),
        ),
    );
    $result = file_get_contents($url, false, stream_context_create($options));
    return json_decode($result, true); // 以 array 返回json解码的数据
}

/**
 * @param string $data The data to be checked
 * @return bool Whether the data contains special characters
 */
function contain_special_chars($data) {
    return (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$data) > 0);
}

/**
 * @param string $data The data to be checked
 * @return bool Whether the data is a valid E-mail
 */
function is_email($data) {
    return ($data == filter_var($data, FILTER_VALIDATE_EMAIL));
}

/**
 * @param string $data The data to be checked
 * @return bool Whether the data is a valid URL
 */
function is_url($data) {
    return ($data == filter_var($data, FILTER_VALIDATE_URL));
}

/**
 * @param string $password The password to convert
 * @return string The converted password
 */
function md5_password($password) {
    return strtoupper(md5(strtoupper($password) . EMOTION_DIARY_SALT));
}

/**
 * @param int $length Length of the random string
 * @return string The generated random string
 */
function random_string($length = 16) {
    if ($length <= 0) {
        $length = 1;
    }
    $str = "";
    $str_pol = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $max = strlen($str_pol) - 1;
    for($i = 0; $i < $length; $i++) {
        $str .= $str_pol[rand(0, $max)];
    }
    return $str;
}

/**
 * @param string $data The data to be checked
 * @param int $length The max length of the data
 * @return bool Whether the data is a random string
 * @see random_string()
 */
function is_random_string($data, $length = 16) {
    return (preg_match("/^[0-9a-zA-Z]{0,$length}$/",$data) > 0);
}

/**
 * @param string $image BASE64 Encoded image string
 * @param string $type Image type, also is the directory's name
 * @param int $max_length Max image length, in KB
 * @return string
 */
function save_image($image, $type, $max_length = 200) {
    $image_string = base64_decode($image);
    if (strlen($image_string) > $max_length * 1024) {
        report_error(1, "图片大小超过了" . $max_length . "KB");
    }
    if (!imagecreatefromstring($image_string)) {
        report_error(2, "图片解析失败");
    }
    $dir = "../images/$type";
    if (!is_dir($dir)) {
        if(!mkdir($dir)) {
            report_error(3, "创建目录失败");
        }
    }
    $file_name = null;
    do {
        $file_name = random_string(8);
    } while (file_exists($dir . "/" . $file_name . ".jpg"));
    if (file_put_contents($dir . "/" . $file_name . ".jpg", $image_string)){
        report_success(array("file_name" => $file_name));
    } else {
        report_error(4, "图片储存失败");
    }
}
