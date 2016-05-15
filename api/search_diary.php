<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/15
 * Time: 上午11:20
 */

/**
 * Common functions for Emotion Diary API.
 */
require_once 'api_utilities.php';
check_version();
$con = db_connect();
check_login($con);

$userid = intval(filter($con, $_POST["userid"]));
$keywords = filter($con, $_POST["keywords"]);

if (strlen($keywords) == 0) {
    report_error(ERROR_MISSING_PARAMETER);
}

$keywords = explode(" | ", $keywords);

if (count($keywords) > 5) {
    report_error(1, "关键词过多");
}

$conditions = create_search_query($keywords);
$result = $con->query("SELECT * FROM diary WHERE userid = '$userid' AND $conditions");
check_sql_error($con);
report_success(array("diaries" => create_mini_diary($result)));

function create_search_query($keywords) {
    $return = "";
    for ($i = 0; $i < count($keywords); $i = $i + 1) {
        $keyword = $keywords[$i];
        $return .= "CONCAT_WS(',', text, tags, place_name, weather) LIKE '%$keyword%'";
        if ($i < count($keywords) - 1) {
            $return .= " AND ";
        }
    }
    return $return;
}
