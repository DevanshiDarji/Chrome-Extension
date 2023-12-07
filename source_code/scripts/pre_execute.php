<?php
 
$now = date("Y-m-d");
//EntryPoint
if(file_exists('custom/VIGsyncGmail/VIAddGsyncGmailConfiguration.php')) {
    $nowVIAddGsyncGmailConfiguration = 'VIAddGsyncGmailConfiguration'.$now.'.'.'php';
    rename("custom/VIGsyncGmail/VIAddGsyncGmailConfiguration.php","custom/VIGsyncGmail/".$nowVIAddGsyncGmailConfiguration);
}

//include
if(file_exists('custom/include/VIGsyncGmail/VIGsyncGmail.php')) {
    $nowVIGsyncGmail = 'VIGsyncGmail'.$now.'.'.'php';
    rename("custom/include/VIGsyncGmail/VIGsyncGmail.php","custom/include/VIGsyncGmail/".$nowVIGsyncGmail);
}
if(file_exists('custom/include/VIGsyncGmail/VIGsyncGmailIcon.css')) {
    $nowVIGsyncGmailIcon = 'VIGsyncGmailIcon'.$now.'.'.'css';
    rename("custom/include/VIGsyncGmail/VIGsyncGmailIcon.css","custom/include/VIGsyncGmail/".$nowVIGsyncGmailIcon);
}

//Administration
if(file_exists('custom/modules/Administration/css/VIGsyncGmail.css')) {
    $nowVIGsyncGmailCSS = 'VIGsyncGmail'.$now.'.'.'css';
    rename("custom/modules/Administration/css/VIGsyncGmail.css","custom/modules/Administration/css/".$nowVIGsyncGmailCSS);
}
if(file_exists('custom/modules/Administration/js/VIGsyncGmailConfiguration.js')) {
    $nowVIGsyncGmailConfiguration = 'VIGsyncGmailConfiguration'.$now.'.'.'js';
    rename("custom/modules/Administration/js/VIGsyncGmailConfiguration.js","custom/modules/Administration/js/".$nowVIGsyncGmailConfiguration);
}
if(file_exists('custom/modules/Administration/tpl/vi_gsyncgmailconfig.tpl')) {
    $nowVIGsyncGmailConfigTPL = 'vi_gsyncgmailconfig'.$now.'.'.'tpl';
    rename("custom/modules/Administration/tpl/vi_gsyncgmailconfig.tpl","custom/modules/Administration/tpl/".$nowVIGsyncGmailConfigTPL);
}
if(file_exists('custom/modules/Administration/views/view.vi_gsyncgmailconfig.php')) {
    $nowVIGsyncGmailConfigPHP = 'view.vi_gsyncgmailconfig'.$now.'.'.'php';
    rename("custom/modules/Administration/views/view.vi_gsyncgmailconfig.php","custom/modules/Administration/views/".$nowVIGsyncGmailConfigPHP);
}

//images
if(file_exists('themes/default/images/GsyncGmail.png')) {
    $nowDefaultGsyncGmailPNG = 'GsyncGmail'.$now.'.'.'png';
    rename("themes/default/images/GsyncGmail.png","themes/default/images/".$nowDefaultGsyncGmailPNG);
}
if(file_exists('themes/default/images/GsyncGmail.svg')) {
    $nowDefaultGsyncGmailSVG = 'GsyncGmail'.$now.'.'.'svg';
    rename("themes/default/images/GsyncGmail.svg","themes/default/images/".$nowDefaultGsyncGmailSVG);
}