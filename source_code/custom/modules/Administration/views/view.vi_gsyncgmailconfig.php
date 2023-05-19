<?php
/*********************************************************************************
* This file is part of package Gsync for Gmail.
* 
* Author : Variance InfoTech PVT LTD (http://www.varianceinfotech.com)
* All rights (c) 2020 by Variance InfoTech PVT LTD
*
* This Version of Gsync for Gmail is licensed software and may only be used in 
* alignment with the License Agreement received with this Software.
* This Software is copyrighted and may not be further distributed without
* written consent of Variance InfoTech PVT LTD
* 
* You can contact via email at info@varianceinfotech.com
* 
********************************************************************************/
require_once('include/MVC/View/SugarView.php');
class Viewvi_gsyncgmailconfig extends SugarView {
	public function __construct() {
		parent::init();
	}//end of function

	public function display() {
		global $mod_strings, $theme; //global variable

		//get data
		$selectGsyncGmailConfig = "SELECT * FROM vi_gsync_gmail_config";
		$selectGsyncGmailConfigRow = $GLOBALS['db']->fetchOne($selectGsyncGmailConfig);

		if(!empty($selectGsyncGmailConfigRow)){
			$activeGsyncGmailVal = $selectGsyncGmailConfigRow['active_gsync_gmail'];
		}else{
			$activeGsyncGmailVal = 0;
		}//end of else

		$url = "https://suitehelp.varianceinfotech.com";

		$helpBoxContent = $this->getGsyncGmailHelpBoxHtml($url);
		
		$smarty = new Sugar_Smarty();

		$smarty->assign("MOD",$mod_strings);
		$smarty->assign("THEME",$theme);
		$smarty->assign("ACTIVE_GSYNC_GMAIL",$activeGsyncGmailVal);
		$smarty->assign('HELP_BOX_CONTENT',$helpBoxContent);
		
		parent::display();
		$smarty->display('custom/modules/Administration/tpl/vi_gsyncgmailconfig.tpl');
	}//end of function

	public function getGsyncGmailHelpBoxHtml($url){
	    global $suitecrm_version, $theme, $current_language;
    
	    $helpBoxContent = '';
	    $curl = curl_init();

	    $postData = json_encode(array("suiteCRMVersion" => $suitecrm_version, "themeName" => $theme, 'currentLanguage' => $current_language));
	    
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
	    
	    $data = curl_exec($curl);
	    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
	    if($httpCode == 200){
	        $helpBoxContent = $data;
	    }//end of if
	    curl_close($curl);

	    return $helpBoxContent;
	}//end of function  
}//end of class