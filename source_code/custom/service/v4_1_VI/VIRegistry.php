<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
 
require_once('service/v4_1/registry.php');
class registry_v4_1_VI extends registry_v4_1 {
    public function __construct($serviceClass) {
        parent::__construct($serviceClass);
    }
    protected function registerFunction() {
        parent::registerFunction();
        $this->serviceClass->registerFunction('vi_login', array('user_auth'=>'tns:user_auth'),
             array('return'=>'tns:entry_value'));

        $this->serviceClass->registerFunction(
            'vi_get_entry_list',
            array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'query'=>'xsd:string', 'order_by'=>'xsd:string','offset'=>'xsd:int', 'select_fields'=>'tns:select_fields', 'link_name_to_fields_array'=>'tns:link_names_to_fields_array', 'max_results'=>'xsd:int', 'deleted'=>'xsd:int'),
            array('return'=>'tns:get_entry_list_result_version2')
        );

        $this->serviceClass->registerFunction(
            'vi_set_entry',
            array('session'=>'xsd:string', 'module_name'=>'xsd:string',  'name_value_list'=>'tns:name_value_list'),
            array('return'=>'tns:new_set_entry_result')
        );

        $this->serviceClass->registerFunction(
            'vi_search_by_module',
            array('session'=>'xsd:string','search_string'=>'xsd:string', 'modules'=>'tns:select_fields', 'offset'=>'xsd:int', 'max_results'=>'xsd:int','assigned_user_id' => 'xsd:string', 'select_fields'=>'tns:select_fields'),
            array('return'=>'tns:return_search_result')
        );

        $this->serviceClass->registerFunction(
            'vi_get_available_modules',
            array('session'=>'xsd:string'),
            array('return'=>'tns:module_list')
        );

        $this->serviceClass->registerFunction(
            'vi_get_module_fields',
            array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'fields'=>'tns:select_fields'),
            array('return'=>'tns:new_module_fields')
        );

        $this->serviceClass->registerFunction(
            'vi_send_email',
            array('session'=>'xsd:string','email_id'=>'xsd:string','module_name' => 'xsd:string','record_id' => 'xsd:string','subject' => 'xsd:string', 'content'=>'xsd:string'),
            array('return'=>'tns:return_send_email_result')
        );

        $this->serviceClass->registerFunction(
            'vi_get_module_subpanel_records',
            array('session'=>'xsd:string','module_name' => 'xsd:string','record_id' => 'xsd:string'),
            array('return'=>'tns:return_subpanel_record_result')
        );

        $this->serviceClass->registerFunction(
            'vi_get_related_field_data',
            array('session'=>'xsd:string','module_name' => 'xsd:string','record_id' => 'xsd:string'),
            array('return'=>'tns:return_module_records_result')
        );

    }
}  
