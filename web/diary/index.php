<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/5/15
 * Time: 下午2:02
 */

/**
 * Common functions for Emotion Diary Web.
 */
require_once '../web_utilities.php';

$result = api_post("/view_diary.php", array("diaryid" => $_GET["diaryid"], "share_key" => $_GET["share_key"]));

?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Emotion Diary</title>
</head>

<body>

<?php

echo "<div align='center'>";

if ($result["code"] == 0) {
    echo "<div><img src='../../images/selfie/" . $result["data"]["selfie"] . ".jpg' width='100'></div>";
    echo "<div>心情:" . $result["data"]["emotion"] . "</div>";
    echo "<div>日期:" . $result["data"]["create_time"] . "</div>";
    echo "<div>正文:</div><div>" . $result["data"]["text"] . "</div>";
    foreach ($result["data"]["images"] as $image) {
        echo "<div><img src='../../images/image/" . $image . ".jpg' width='200'></div>";
    }
}else {
    echo "<div>出错了:" . $result["message"] . "$message</div>";
}

echo "</div>";

?>

</body>

</html>