<?php
 
global $db;
$db->dropTableName('vi_gsync_gmail_config');

$sqlSlackIntegration = "DELETE from config where name = 'gsync-for-gmail'";
$result = $GLOBALS['db']->query($sqlSlackIntegration);

$sqlLicenseKey = "DELETE from config where name = 'lic_gsync-for-gmail'";
$result2 = $GLOBALS['db']->query($sqlLicenseKey);