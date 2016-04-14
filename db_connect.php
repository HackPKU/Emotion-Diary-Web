<?php
/**
 * Global shared lib for Emotion Diary.
 */
require_once 'config.php';

// Database connector.
function dbconnect() {
    $con = @mysql_connect(EMOTION_DIARY_DB_HOSTNAME, EMOTION_DIARY_DB_USERNAME, EMOTION_DIARY_DB_PASSWORD) or die("Cannot connect to database!");
    mysql_query("SET NAMES 'UTF8'");
    mysql_select_db(EMOTION_DIARY_DB_NAME);
}

?>
