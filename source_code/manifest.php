<?php
 
$manifest = array (
    0 => 
        array (
            'acceptable_sugar_versions' => 
            array (
                0 => '',
            ),
        ),
    1 => 
        array (
            'acceptable_sugar_flavors' => 
            array (
                0 => 'CE',
                1 => 'PRO',
                2 => 'ENT',
            ),
        ),
    'readme' => '',
    'key' => '',
    'author' => 'Variance Infotech PVT. LTD',
    'description' => 'Gsync for Gmail Plugin',
    'icon' => '',
    'is_uninstallable' => true,
    'name' => 'Gsync for Gmail',
    'published_date' => '2021-12-06 12:58:54',
    'type' => 'module',
    'version' => 'v1.0',
    'remove_tables' => 'prompt',
);
$installdefs = array (
    'id' => 'GsyncforGmail',
    'beans' => //remove this bean or replace with your own module name.
        array (
            array (
              'module' => 'VIGsyncGmailLicenseAddon',
              'class' => 'VIGsyncGmailLicenseAddon',
              'path' => 'modules/VIGsyncGmailLicenseAddon/VIGsyncGmailLicenseAddon.php',
              'tab' => false,
            ),
        ),
    'post_install' => array(  0 =>  '<basepath>/scripts/post_install.php',),
    'post_execute' => array(  0 =>  '<basepath>/scripts/post_execute.php',),
    'post_uninstall' => array(  0 =>  '<basepath>/scripts/post_uninstall.php',),
    'pre_execute' => array(  0 =>  '<basepath>/scripts/pre_execute.php',),
    'copy' => array (
        0 => 
            array (
                'from' => '<basepath>/custom/Extension/application/Ext/EntryPointRegistry/VIGsyncGmailEntryPoint.php',
                'to' => 'custom/Extension/application/Ext/EntryPointRegistry/VIGsyncGmailEntryPoint.php',
            ),
        1 => 
            array (
                'from' => '<basepath>/custom/Extension/application/Ext/LogicHooks/VIGsyncGmail_Hook.php',
                'to' => 'custom/Extension/application/Ext/LogicHooks/VIGsyncGmail_Hook.php',
            ),
        2 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/ActionViewMap/VIGsyncGmailAction_View_Map.ext.php',
                'to' => 'custom/Extension/modules/Administration/Ext/ActionViewMap/VIGsyncGmailAction_View_Map.ext.php',
            ),
        3 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Administration/VIGsyncGmailAdministration.ext.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Administration/VIGsyncGmailAdministration.ext.php',
            ),
        4 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.de_DE.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.de_DE.lang.php',
            ),
        5 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.en_us.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.en_us.lang.php',
            ),
        6 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.es_ES.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.es_ES.lang.php',
            ),
        7 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.fr_FR.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.fr_FR.lang.php',
            ),
        8 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.hu_HU.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.hu_HU.lang.php',
            ),
        9 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.it_IT.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.it_IT.lang.php',
            ),
        10 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.nl_NL.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.nl_NL.lang.php',
            ),
        11 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.pt_BR.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.pt_BR.lang.php',
            ),
        12 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.ru_RU.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.ru_RU.lang.php',
            ),
        13 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.ua_UA.lang.php',
                'to' => 'custom/Extension/modules/Administration/Ext/Language/VIGsyncGmail.ua_UA.lang.php',
            ),
        14 => 
            array (
                'from' => '<basepath>/custom/Extension/modules/VIGsyncGmailLicenseAddon/Ext/ActionViewMap/VIGsyncGmailLicenseAddon_actionviewmap.php',
                'to' => 'custom/Extension/modules/VIGsyncGmailLicenseAddon/Ext/ActionViewMap/VIGsyncGmailLicenseAddon_actionviewmap.php',
            ),
        15 => 
            array (
                'from' => '<basepath>/custom/include/VIGsyncGmail/VIGsyncGmail.php',
                'to' => 'custom/include/VIGsyncGmail/VIGsyncGmail.php',
            ),
        16 => 
            array (
                'from' => '<basepath>/custom/include/VIGsyncGmail/VIGsyncGmailIcon.css',
                'to' => 'custom/include/VIGsyncGmail/VIGsyncGmailIcon.css',
            ),
        17 => 
            array (
                'from' => '<basepath>/custom/modules/Administration/css/VIGsyncGmail.css',
                'to' => 'custom/modules/Administration/css/VIGsyncGmail.css',
            ),
        18 => 
            array (
                'from' => '<basepath>/custom/modules/Administration/js/VIGsyncGmailConfiguration.js',
                'to' => 'custom/modules/Administration/js/VIGsyncGmailConfiguration.js',
            ),
        19 => 
            array (
                'from' => '<basepath>/custom/modules/Administration/tpl/vi_gsyncgmailconfig.tpl',
                'to' => 'custom/modules/Administration/tpl/vi_gsyncgmailconfig.tpl',
            ),
        20 => 
            array (
                'from' => '<basepath>/custom/modules/Administration/views/view.vi_gsyncgmailconfig.php',
                'to' => 'custom/modules/Administration/views/view.vi_gsyncgmailconfig.php',
            ),
        21 => 
            array (
                'from' => '<basepath>/custom/modules/VIGsyncGmailLicenseAddon/',
                'to' => 'custom/modules/VIGsyncGmailLicenseAddon',
            ),
        22 => 
            array (
                'from' => '<basepath>/custom/service/v4_1_VI/',
                'to' => 'custom/service/v4_1_VI/',
            ),
        23 => 
            array (
                'from' => '<basepath>/custom/VIGsyncGmail/VIAddGsyncGmailConfiguration.php',
                'to' => 'custom/VIGsyncGmail/VIAddGsyncGmailConfiguration.php',
            ),
        24 => 
            array (
                'from' => '<basepath>/images/GsyncGmail.png',
                'to' => 'themes/default/images/GsyncGmail.png',
            ),
        25 => 
            array (
                'from' => '<basepath>/images/GsyncGmail.svg',
                'to' => 'themes/default/images/GsyncGmail.svg',
            ),
        26 => 
            array (
                'from' => '<basepath>/images/GsyncGmail.png',
                'to' => 'themes/SuiteP/images/GsyncGmail.png',
            ),
        27 => 
            array (
                'from' => '<basepath>/images/GsyncGmail.svg',
                'to' => 'themes/SuiteP/images/GsyncGmail.svg',
            ),    
        28 => 
            array (
                'from' => '<basepath>/modules/VIGsyncGmailLicenseAddon/',
                'to' => 'modules/VIGsyncGmailLicenseAddon/',
            ),
        29 => 
            array (
                'from' => '<basepath>/custom/modules/Administration/css/VIGsyncGmailSuite7R.css',
                'to' => 'custom/modules/Administration/css/VIGsyncGmailSuite7R.css',
            ),
    ),
);