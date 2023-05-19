<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
class VIAddGsyncGmailConfiguration{
    public function __construct(){
        $this->VIAddGsyncGmailConfiguration();
    }//end of function 
    
    public function VIAddGsyncGmailConfiguration(){
    	$activeGsyncGmailVal = $_REQUEST['activeGsyncGmailVal'];

    	//get data
    	$selectGsyncGmailConfig = "SELECT * FROM vi_gsync_gmail_config";
		$selectGsyncGmailConfigRow = $GLOBALS['db']->fetchOne($selectGsyncGmailConfig);

		if(!empty($selectGsyncGmailConfigRow)){
			$updateData = "UPDATE vi_gsync_gmail_config SET active_gsync_gmail = $activeGsyncGmailVal";
			$updateDataResult = $GLOBALS['db']->query($updateData);
		}else{
			$insertData = "INSERT INTO vi_gsync_gmail_config(active_gsync_gmail)values($activeGsyncGmailVal)";
			$insertDataResult = $GLOBALS['db']->query($insertData);
		}//end of else
    }//end of function
}//end of class
new VIAddGsyncGmailConfiguration();