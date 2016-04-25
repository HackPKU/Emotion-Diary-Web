<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/4/25
 * Time: 下午2:27
 */

require_once '../db_connect.php';

/**
 * Stop reporting errors to client
 */
if (!EMOTION_DIARY_REPORT_ERRORS) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

/**
 * @param object $con Database connection
 * @param string $data The data to be filtered
 * @return string Filtered data
 */
function filter($con, $data) {
    if ($data != mysqli_real_escape_string($con, $data)) {
        report_error(-2, "参数中含有非法字符");
    }
    return $data;
}

/**
 * @param int $code Error code
 * @param string $message Error message
 * @param bool $shouldExit Whether php should exit after reporting the error
 */
function report_error($code = -1, $message = "未知错误", $shouldExit = true) {
    if ($code == 0) { // 0 为成功代码
        $code = -1;
    }
    echo json_encode(array("code" => $code, "message" => $message, "data" => null));
    if ($shouldExit) {
        exit();
    }
}

/**
 * @param object $con Database connection
 * @param bool $shouldExit Whether php should exit after reporting the error
 */
function check_sql_error($con, $shouldExit = true) {
    if (mysqli_error($con)) {
        $message = "数据库错误";
        if (EMOTION_DIARY_REPORT_ERRORS) {
            $message = mysqli_error($con);
        }
        echo json_encode(array("code" => -1, "message" => $message, "data" => null));
        if ($shouldExit) {
            exit();
        }
    }
}

/**
 * @param mixed $data Data to return
 */
function report_success($data = null) {
    echo json_encode(array("code" => 0, "message" => null, "data" => $data));
}

/**
 * @param string $dir Request URL directory relative to current directory
 * @param array $post Request parameters
 * @return array Request result
 */
function request_post($dir, $post) {
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
 * @param int $length Length of the random string
 * @return string The generated random string
 */
function random_string($length = 32) {
    if ($length <= 0) {
        $length = 1;
    }
    $str = "";
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;
    for($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}