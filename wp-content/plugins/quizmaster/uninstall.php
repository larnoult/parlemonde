<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

include_once 'lib/helper/QuizMaster_Helper_DbUpgrade.php';

$db = new QuizMaster_Helper_DbUpgrade();
$db->delete();

delete_option('quizMaster_dbVersion');
delete_option('quizMaster_version');

delete_option('quizMaster_addRawShortcode');
delete_option('quizMaster_jsLoadInHead');
delete_option('quizMaster_touchLibraryDeactivate');
delete_option('quizMaster_corsActivated');
delete_option('quizMaster_toplistDataFormat');
delete_option('quizMaster_emailSettings');
delete_option('quizMaster_statisticTimeFormat');