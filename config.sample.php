<?php
/**
 * Configuration for Emotion Diary.
 *
 * You need to copy this file to `config.php` to make it work.
 *
 * This file contains the following configrations:
 *
 * - MySQL settings.
 *
 * Reference:
 * - https://github.com/WordPress/WordPress/blob/master/wp-config-sample.php
 */

//** MySQL settings. **//
/** The database username. */
define('EMOTION_DIARY_DB_USERNAME', 'database_username_here');

/** The database password. */
define('EMOTION_DIARY_DB_PASSWORD', 'database_password_here');

/** The database name. */
define('EMOTION_DIARY_DB_NAME', 'database_name_here');

/** The database hostname. */
define('EMOTION_DIARY_DB_HOSTNAME', 'localhost');

//** Security settings. **//
/** The password encryption salt. */
define('EMOTION_DIARY_SALT', 'salt_here');

//** Debug settings. **//
/** Whether server should report errors. */
define('EMOTION_DIARY_REPORT_ERRORS', false);

//** Version settings. **//
/** Min supported client version. */
define('EMOTION_DIARY_MIN_VERSION', 1.0);

//** Time zone settings. **//
date_default_timezone_set('PRC'); // PRC为“中华人民共和国”
