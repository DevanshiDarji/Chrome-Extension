<?php
 
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['VIGsyncGmailLicenseAddon'] = array(
    'table' => 'vigsyncgmaillicenseaddon',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Accounts are organizations or entities that are the target of selling, support, and marketing activities, or have already purchased products or services',
    'fields' => array(
    ),
);
if (!class_exists('VardefManager')) {
        require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('VIGsyncGmailLicenseAddon', 'VIGsyncGmailLicenseAddon', array('basic','assignable','security_groups'));
?>