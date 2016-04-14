<?php
/**
 * Global shared lib for Emotion Diary.
 */
require_once 'config.php';

/**
 * Database connector.
 */
function db_connect() {
    $con = mysqli_connect(EMOTION_DIARY_DB_HOSTNAME, EMOTION_DIARY_DB_USERNAME, EMOTION_DIARY_DB_PASSWORD, EMOTION_DIARY_DB_NAME);
    $con->query("SET NAMES 'UTF8'");
    return $con;
}

?>
