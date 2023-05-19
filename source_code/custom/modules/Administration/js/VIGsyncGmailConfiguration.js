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
$('#active_gsync_gmail').on('change',function(){
	SUGAR.ajaxUI.showLoadingPanel(); // show loader
 	var activeGsyncGmailVal = 0;
 	if($(this).is(':checked')){
 		$(this).val('1');
 		activeGsyncGmailVal = 1;
 	}else{
 		$(this).val('0');
 		activeGsyncGmailVal = 0;
 	}//end of else

 	//add gsync gmail config data
 	$.ajax({
 		url:"index.php?entryPoint=VIAddGsyncGmailConfiguration",
 		type:"POST",
 		data:{activeGsyncGmailVal : activeGsyncGmailVal},
 		success:function(result){
 			SUGAR.ajaxUI.hideLoadingPanel(); // hide loader
 			if(activeGsyncGmailVal == 1){
 				alert(mod.LBL_ACTIVE_GSYNC_GMAIL);
 			}else{
 				alert(mod.LBL_INACTIVE_GSYNC_GMAIL);
 			}//end of else
 		}//end of function
 	});//end of ajax
});//end of function