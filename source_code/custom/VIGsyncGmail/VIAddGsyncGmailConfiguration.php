<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
 
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