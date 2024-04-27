<?php


if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define('DBUSER', "root"); // declared as constants
    define('DBPASS', "");
    define('DBNAME', "project_db"); // myblog_db <-- in tutorial
    define('DBHOST', "localhost");
} else {
    define('DBUSER', "root"); // declared as constants
    define('DBPASS', "");
    define('DBNAME', "project_db"); // myblog_db <-- in tutorial
    define('DBHOST', "localhost");
}
