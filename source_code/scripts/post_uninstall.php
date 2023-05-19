<?php
/*********************************************************************************
 * This file is part of package GSync for Gmail.
 * 
 * Author : Variance InfoTech PVT LTD (http://www.varianceinfotech.com)
 * All rights (c) 2020 by Variance InfoTech PVT LTD
 *
 * This Version of GSync for Gmail is licensed software and may only be used in 
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * written consent of Variance InfoTech PVT LTD
 * 
 * You can contact via email at info@varianceinfotech.com
 * 
 ********************************************************************************/
global $db;
$db->dropTableName('vi_gsync_gmail_config');

$sqlSlackIntegration = "DELETE from config where name = 'gsync-for-gmail'";
$result = $GLOBALS['db']->query($sqlSlackIntegration);

$sqlLicenseKey = "DELETE from config where name = 'lic_gsync-for-gmail'";
$result2 = $GLOBALS['db']->query($sqlLicenseKey);