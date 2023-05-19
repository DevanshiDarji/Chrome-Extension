<?php
/*********************************************************************************
 * This file is part of package GSync for Gmail.
 * 
 * Author : Variance InfoTech PVT LTD (http://www.varianceinfotech.com)
 * All rights (c) 2020 by Variance InfoTech PVT LTD
 *
 * This Version of Gsync for Gmail is licensed software and may only be used in 
 * alignment with GSync License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * written consent of Variance InfoTech PVT LTD
 * 
 * You can contact via email at info@varianceinfotech.com
 * 
 ********************************************************************************/
require_once('modules/VIGsyncGmailLicenseAddon/license/VIGsyncGmailOutfittersLicense.php');
require_once('include/MVC/Controller/SugarController.php');
global $sugar_config;
global $theme;
$dynamicURL = $sugar_config['site_url'];
$url = $dynamicURL."/index.php?module=VIGsyncGmailLicenseAddon&action=license";
$sqlLicenseCheck = "SELECT * from config where name = 'lic_gsync-for-gmail'";
$result = $GLOBALS['db']->query($sqlLicenseCheck);
$selectResultData = $GLOBALS['db']->fetchRow($GLOBALS['db']->query($sqlLicenseCheck));
if(!empty($selectResultData)){
    $validate_license = VIGsyncGmailOutfittersLicense::isValid('VIGsyncGmailLicenseAddon');
    if($validate_license !== true) {
        if(is_admin($current_user)) {
            SugarApplication::appendErrorMessage('VIGsyncGmailLicenseAddon is no longer active due to the following reason: '.$validate_license.' Users will have limited to no access until the issue has been addressed <a href='.$url.'>Click Here</a>');
        }//end of if
        echo '<h2><p class="error">VIGsyncGmailLicenseAddon is no longer active</p></h2><p class="error">Please renew your subscription or check your license configuration.</p><a href='.$url.'>Click Here</a>';
    }else{
        foreach ($admin_group_header as $key => $value) {
            $values[] = $value[0];
        }//end of foreach   
        if (in_array("Other", $values)){
            $array['GsyncGmail'] = array('GsyncGmail',
                                                      $mod_strings["LBL_GSYNC_GMAIL"],
                                                      $mod_strings["LBL_GSYNC_GMAIL_DESCRIPTION"],
                                                      './index.php?module=Administration&action=vi_gsyncgmailconfig',
                                                      'gsync-gmail');
            $admin_group_header['Other'][3]['Administration'] = array_merge($admin_group_header['Other'][3]['Administration'],$array);
        }else{
            $admin_option_defs = array();
            $admin_option_defs['Administration']['GsyncGmail'] = array(
                //Icon name. Available icons are located in ./themes/default/images
                'GsyncGmail',

                //Link name label 
                $mod_strings["LBL_GSYNC_GMAIL"],

                //Link description label
                $mod_strings["LBL_GSYNC_GMAIL_DESCRIPTION"],

                //Link URL
                './index.php?module=Administration&action=vi_gsyncgmailconfig',
                'gsync-gmail',
            );
            $admin_group_header['Other'] = array(
                //Section header label
                'Other',

                //$other_text parameter for get_form_header()
                '',

                //$show_help parameter for get_form_header()
                false,

                //Section links
                $admin_option_defs,

                //Section description label
                ''
            );
        }//end of else   
    }//end of else
}else{
    foreach ($admin_group_header as $key => $value) {
        $values[] = $value[0];
    }//end of foreach
    if (in_array("Other", $values)) {
        $array['GsyncGmail'] = array('GsyncGmail',$mod_strings["LBL_GSYNC_GMAIL"],
                                                          $mod_strings["LBL_GSYNC_GMAIL_DESCRIPTION"],
                                                          './index.php?module=VIGsyncGmailLicenseAddon&action=license',
                                                          'gsync-gmail');
        $admin_group_header['Other'][3]['Administration'] = array_merge($admin_group_header['Other'][3]['Administration'],$array);
    }else{
        $admin_option_defs = array();
        $admin_option_defs['Administration']['GsyncGmail'] = array(
            //Icon name. Available icons are located in ./themes/default/images
            'GsyncGmail',

            //Link name label 
            $mod_strings["LBL_GSYNC_GMAIL"],

            //Link description label
            $mod_strings["LBL_GSYNC_GMAIL_DESCRIPTION"],

            //Link URL
            './index.php?module=VIGsyncGmailLicenseAddon&action=license',
            'gsync-gmail'
        );
        $admin_group_header['Other'] = array(
            //Section header label
            'Other',

            //$other_text parameter for get_form_header()
            '',

            //$show_help parameter for get_form_header()
            false,

            //Section links
            $admin_option_defs,

            //Section description label
            ''
        );
    }//end of else
}//end of else