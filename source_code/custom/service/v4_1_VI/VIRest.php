<?php
if (!defined('sugarEntry')) define('sugarEntry', true);
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
require_once('MySugarRestServiceImpl_4_1_VI.php');
$webservice_path = 'service/core/SugarRestService.php';    
$webservice_class = 'SugarRestService';    
$webservice_impl_class = 'MySugarRestServiceImpl_4_1_VI';

$registry_path = 'custom/service/v4_1_VI/VIRegistry.php';
$registry_class = 'registry_v4_1_VI';
$location = 'custom/service/v4_1_VI/VIRest.php';
require_once('service/core/webservice.php');  