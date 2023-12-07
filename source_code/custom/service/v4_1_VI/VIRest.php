<?php
if (!defined('sugarEntry')) define('sugarEntry', true);
 
require_once('MySugarRestServiceImpl_4_1_VI.php');
$webservice_path = 'service/core/SugarRestService.php';    
$webservice_class = 'SugarRestService';    
$webservice_impl_class = 'MySugarRestServiceImpl_4_1_VI';

$registry_path = 'custom/service/v4_1_VI/VIRegistry.php';
$registry_class = 'registry_v4_1_VI';
$location = 'custom/service/v4_1_VI/VIRest.php';
require_once('service/core/webservice.php');  