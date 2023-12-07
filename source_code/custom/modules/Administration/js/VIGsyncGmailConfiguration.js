 
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