<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
 
class VIGsyncGmail{
	function VIGsyncForGmail($event,$arguments){
		if($_REQUEST['module'] == 'Administration' && $_REQUEST['action'] == 'index'){
			echo '<link rel="stylesheet" type="text/css" href="custom/include/VIGsyncGmail/VIGsyncGmailIcon.css">';
		}//end of if
	}//end of function
}//end of class