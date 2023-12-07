<?php
 
chdir('../../..');
require_once('service/v4_1/SugarWebServiceImplv4_1.php');
class MySugarRestServiceImpl_4_1_VI extends SugarWebServiceImplv4_1 {
    
    //login
    function vi_login($user_auth){
        $name_value_list = array();
        $GLOBALS['log']->info("Begin: SugarWebServiceImpl->login({$user_auth['user_name']})");
        global $sugar_config, $system_config;
        
        $error = new SoapError();
        $user = new User();
        $success = false;

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        //rrs
        $system_config = new Administration();
        $system_config->retrieveSettings('system');
        $authController = new AuthenticationController();
        //rrs
        if (!empty($user_auth['encryption']) && $user_auth['encryption'] === 'PLAIN' && $authController->authController->userAuthenticateClass != "LDAPAuthenticateUser") {
            $user_auth['password'] = md5($user_auth['password']);
        }
        $isLoginSuccess = $authController->login($user_auth['user_name'], $user_auth['password'], array('passwordEncrypted' => true));
        $usr_id=$user->retrieve_user_id($user_auth['user_name']);
        if ($usr_id) {
            $user->retrieve($usr_id);
        }
        
        if ($isLoginSuccess) {
            if ($_SESSION['hasExpiredPassword'] =='1') {
                $error->set_error('password_expired');
                $GLOBALS['log']->fatal('password expired for user ' . $user_auth['user_name']);
                LogicHook::initialize();
                $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
                self::$helperObject->setFaultObject($error);
                return;
            }
            if (!empty($user) && !empty($user->id) && !$user->is_group) {
                $success = true;
                global $current_user;
                $current_user = $user;
            }
        } elseif ($usr_id && isset($user->user_name) && ($user->getPreference('lockout') == '1')) {
            $error->set_error('lockout_reached');
            $GLOBALS['log']->fatal('Lockout reached for user ' . $user_auth['user_name']);
            LogicHook::initialize();
            $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
            self::$helperObject->setFaultObject($error);
            return;
        } elseif (function_exists('openssl_decrypt') && $authController->authController->userAuthenticateClass == "LDAPAuthenticateUser"
                && (empty($user_auth['encryption']) || $user_auth['encryption'] !== 'PLAIN')) {
            $password = self::$helperObject->decrypt_string($user_auth['password']);
            $authController->loggedIn = false; // reset login attempt to try again with decrypted password
            if ($authController->login($user_auth['user_name'], $password) && isset($_SESSION['authenticated_user_id'])) {
                $success = true;
            }
        } elseif ($authController->authController->userAuthenticateClass == "LDAPAuthenticateUser"
                 && (empty($user_auth['encryption']) || $user_auth['encryption'] == 'PLAIN')) {
            $authController->loggedIn = false; // reset login attempt to try again with md5 password
            if ($authController->login($user_auth['user_name'], md5($user_auth['password']), array('passwordEncrypted' => true))
                && isset($_SESSION['authenticated_user_id'])) {
                $success = true;
            } else {
                $error->set_error('ldap_error');
                LogicHook::initialize();
                $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
                self::$helperObject->setFaultObject($error);
                return;
            }
        }


        if ($success) {
            session_start();
            global $current_user;
            //$current_user = $user;
            self::$helperObject->login_success($name_value_list);
            $current_user->loadPreferences();
            $_SESSION['is_valid_session']= true;
            $_SESSION['ip_address'] = query_client_ip();
            $_SESSION['user_id'] = $current_user->id;
            $_SESSION['type'] = 'user';
            $_SESSION['avail_modules']= self::$helperObject->get_user_module_list($current_user);
            $_SESSION['authenticated_user_id'] = $current_user->id;
            $_SESSION['unique_key'] = $sugar_config['unique_key'];
            $GLOBALS['log']->info('End: SugarWebServiceImpl->login - successful login');
            $current_user->call_custom_logic('after_login');
            $nameValueArray = array();
            global $current_language;
            $nameValueArray['user_id'] = self::$helperObject->get_name_value('user_id', $current_user->id);
            $nameValueArray['user_name'] = self::$helperObject->get_name_value('user_name', $current_user->full_name);
            $nameValueArray['user_language'] = self::$helperObject->get_name_value('user_language', $current_language);
            $cur_id = $current_user->getPreference('currency');
            $nameValueArray['user_currency_id'] = self::$helperObject->get_name_value('user_currency_id', $cur_id);
            $nameValueArray['user_is_admin'] = self::$helperObject->get_name_value('user_is_admin', is_admin($current_user));
            $nameValueArray['user_default_team_id'] = self::$helperObject->get_name_value('user_default_team_id', $current_user->default_team);
            $nameValueArray['user_default_dateformat'] = self::$helperObject->get_name_value('user_default_dateformat', $current_user->getPreference('datef'));
            $nameValueArray['user_default_timeformat'] = self::$helperObject->get_name_value('user_default_timeformat', $current_user->getPreference('timef'));

            $num_grp_sep = $current_user->getPreference('num_grp_sep');
            $dec_sep = $current_user->getPreference('dec_sep');
            $nameValueArray['user_number_seperator'] = self::$helperObject->get_name_value('user_number_seperator', empty($num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $num_grp_sep);
            $nameValueArray['user_decimal_seperator'] = self::$helperObject->get_name_value('user_decimal_seperator', empty($dec_sep) ? $sugar_config['default_decimal_seperator'] : $dec_sep);

            $nameValueArray['mobile_max_list_entries'] = self::$helperObject->get_name_value('mobile_max_list_entries', $sugar_config['wl_list_max_entries_per_page']);
            $nameValueArray['mobile_max_subpanel_entries'] = self::$helperObject->get_name_value('mobile_max_subpanel_entries', $sugar_config['wl_list_max_entries_per_subpanel']);


            $currencyObject = new Currency();
            $currencyObject->retrieve($cur_id);
            $nameValueArray['user_currency_name'] = self::$helperObject->get_name_value('user_currency_name', $currencyObject->name);
            $_SESSION['user_language'] = $current_language;
            $modulesAccess = $this->vi_get_available_modules(session_id());

            $userProfile['user_name'] = $user->name;
            $userProfile['first_name'] = $user->first_name;
            $userProfile['last_name'] = $user->last_name;
            if($user->photo != ''){
                $imageUrl = $sugar_config['site_url']."/index.php?entryPoint=download&id=".$user->id."_photo&type=Users";
            }else{
                $imageUrl = '';
            }
            $userProfile['phone_mobile'] = $user->phone_mobile;
            $userProfile['email'] = $user->email1;
            $userProfile['image_url'] = $imageUrl;
            $userProfile['profile_url'] = $sugar_config['site_url']."/index.php?module=Users&action=EditView&record=".$user->id;

            return array('id'=>session_id(), 'module_name'=>'Users', 'name_value_list'=>$nameValueArray,'modules_access' => $modulesAccess,'user_profile_details' => $userProfile);
        }
        LogicHook::initialize();
        $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
        $error->set_error('invalid_login');
        self::$helperObject->setFaultObject($error);
        $GLOBALS['log']->error('End: SugarWebServiceImpl->login - failed login');
    }

    //retrive module records
    function vi_get_entry_list($session, $module_name, $query, $order_by, $offset, $select_fields, $link_name_to_fields_array, $max_results, $deleted, $favorites = false){
        $order_by = "date_entered DESC";
        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->get_entry_list');
        global  $beanList, $beanFiles;
        $error = new SoapError();
        $using_cp = false;
        if ($module_name == 'CampaignProspects') {
            $module_name = 'Prospects';
            $using_cp = true;
        }
        
        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->get_entry_list - FAILED on checkSessionAndModuleAccess');
            return;
        } // if

        if (!self::$helperObject->checkQuery($error, $query, $order_by)) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->get_entry_list');
            return;
        } // if
        global $sugar_config;
        // If the maximum number of entries per page was specified, override the configuration value.
        if ($max_results > 0) {
            $sugar_config['list_max_entries_per_page'] = $max_results;
        } // if

        $class_name = $beanList[$module_name];
        require_once($beanFiles[$class_name]);
        $seed = new $class_name();

        if (!self::$helperObject->checkACLAccess($seed, 'list', $error, 'no_access')) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->get_entry_list - FAILED on checkACLAccess');
            return;
        } // if

        if ($query == '') {
            $where = '';
        } // if
        if ($offset == '' || $offset == -1) {
            $offset = 0;
        } // if
        if ($deleted) {
            $deleted = -1;
        }
        if ($using_cp) {
            $response = $seed->retrieveTargetList($query, $select_fields, $offset, -1, -1, $deleted);
        } else {
            $response = self::$helperObject->get_data_list($seed, $order_by, $query, $offset, -1, -1, $deleted, $favorites);
        } // else
        $list = $response['list'];

        $output_list = array();
        $linkoutput_list = array();

        foreach ($list as $value) {
            if (isset($value->emailAddress)) {
                $value->emailAddress->handleLegacyRetrieve($value);
            } // if
            $value->fill_in_additional_detail_fields();

            $output_list[] = $this->vi_get_return_value_for_fields($value, $module_name, $select_fields);
            if (!empty($link_name_to_fields_array)) {
                $linkoutput_list[] = self::$helperObject->get_return_value_for_link_fields($value, $module_name, $link_name_to_fields_array);
            }
        } // foreach

        // Calculate the offset for the start of the next page
        $next_offset = $offset + sizeof($output_list);

        $returnRelationshipList = array();
        foreach ($linkoutput_list as $rel) {
            $link_output = array();
            foreach ($rel as $row) {
                $rowArray = array();
                foreach ($row['records'] as $record) {
                    $rowArray[]['link_value'] = $record;
                }
                $link_output[] = array('name' => $row['name'], 'records' => $rowArray);
            }
            $returnRelationshipList[]['link_list'] = $link_output;
        }

        $totalRecordCount = $response['row_count'];
        if (!empty($sugar_config['disable_count_query'])) {
            $totalRecordCount = -1;
        }

        $listviewUrl = $sugar_config['site_url']."/index.php?module=".$module_name."&action=listview";

        $GLOBALS['log']->info('End: SugarWebServiceImpl->get_entry_list - SUCCESS');
        return array('result_count'=>sizeof($output_list), 'total_count' => $totalRecordCount, 'next_offset'=>$next_offset,'listview_url' => $listviewUrl,'entry_list'=>$output_list, 'relationship_list' => $returnRelationshipList);
    }

    //save
    function vi_set_entry($session, $module_name, $name_value_list, $track_view = false){
        global $beanList, $beanFiles, $current_user;

        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->set_entry');
        if (self::$helperObject->isLogLevelDebug()) {
            $GLOBALS['log']->debug('SoapHelperWebServices->set_entry - input data is ' . var_export(
                $name_value_list,
                    true
            ));
        } // if
        $error = new SoapError();

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if
        if (!self::$helperObject->checkSessionAndModuleAccess(
            $session,
            'invalid_session',
            $module_name,
            'write',
            'no_access',
            $error
        )
        ) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->set_entry');

            return;
        } // if
        $class_name = $beanList[$module_name];
        require_once($beanFiles[$class_name]);
        $seed = new $class_name();
        foreach ($name_value_list as $name => $value) {
            if (is_array($value) && $value['name'] == 'id') {
                $seed->retrieve($value['value']);
                break;
            } elseif ($name === 'id') {
                $seed->retrieve($value);
            }
        }

        $return_fields = array();
        foreach ($name_value_list as $name => $value) {
            if ($module_name == 'Users' && !empty($seed->id) && ($seed->id != $current_user->id) && $name == 'user_hash') {
                continue;
            }
            if (!empty($seed->field_name_map[$name]['sensitive'])) {
                continue;
            }

            if (!is_array($value)) {
                $seed->$name = $value;
                $return_fields[] = $name;
            } else {
                if($seed->field_defs[$value['name']]['type'] == 'relate' || $seed->field_defs[$value['name']]['type'] == 'parent'){
                    $idName = $seed->field_defs[$value['name']]['id_name'];
                    $seed->$idName = $value['value'];
                }else{
                    $seed->{$value['name']} = $value['value'];
                }
               
                $return_fields[] = $value['name'];
            }
        }
        if (!self::$helperObject->checkACLAccess(
            $seed,
            'Save',
            $error,
                'no_access'
        ) || ($seed->deleted == 1 && !self::$helperObject->checkACLAccess(
                    $seed,
                    'Delete',
                    $error,
                    'no_access'
                ))
        ) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->set_entry');

            return;
        } // if

        $seed->save(self::$helperObject->checkSaveOnNotify());

        $return_entry_list = self::$helperObject->get_name_value_list_for_fields($seed, $return_fields);

        if ($seed->deleted == 1) {
            $seed->mark_deleted($seed->id);
        }

        if ($track_view) {
            self::$helperObject->trackView($seed, 'editview');
        }

        $GLOBALS['log']->info('End: SugarWebServiceImpl->set_entry');

        return array('id' => $seed->id, 'entry_list' => $return_entry_list);
    }

    //serach
    function vi_search_by_module($session, $search_string, $modules, $offset, $max_results, $assigned_user_id = '', $select_fields = array(), $unified_search_only = true, $favorites = false) {
        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->search_by_module');
        global  $beanList, $beanFiles;
        global $sugar_config,$current_language;

        $error = new SoapError();
        
        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        $output_list = array();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->search_by_module - FAILED on checkSessionAndModuleAccess');
            return;
        }
        global $current_user;
        if ($max_results > 0) {
            $sugar_config['list_max_entries_per_page'] = $max_results;
        }

        require_once('modules/Home/UnifiedSearchAdvanced.php');
        require_once 'include/utils.php';
        $usa = new UnifiedSearchAdvanced();
        if (!file_exists($cachefile = sugar_cached('modules/unified_search_modules.php'))) {
            $usa->buildCache();
        }

        include $cachefile;
        $modules_to_search = array();
        $unified_search_modules['Users'] =   array('fields' => array());

        $unified_search_modules['ProjectTask'] =   array('fields' => array());

        //If we are ignoring the unified search flag within the vardef we need to re-create the search fields.  This allows us to search
        //against a specific module even though it is not enabled for the unified search within the application.
        if (!$unified_search_only) {
            foreach ($modules as $singleModule) {
                if (!isset($unified_search_modules[$singleModule])) {
                    $newSearchFields = array('fields' => self::$helperObject->generateUnifiedSearchFields($singleModule) );
                    $unified_search_modules[$singleModule] = $newSearchFields;
                }
            }
        }


        foreach ($unified_search_modules as $module=>$data) {
            if (in_array($module, $modules)) {
                $modules_to_search[$module] = $beanList[$module];
            } // if
        } // foreach

        $GLOBALS['log']->info('SugarWebServiceImpl->search_by_module - search string = ' . $search_string);

        if (!empty($search_string) && isset($search_string)) {
            $search_string = trim(DBManagerFactory::getInstance()->quote(securexss(from_html(clean_string($search_string, 'UNIFIED_SEARCH')))));
            foreach ($modules_to_search as $name => $beanName) {
                $where_clauses_array = array();
                $unifiedSearchFields = array() ;
                foreach ($unified_search_modules[$name]['fields'] as $field=>$def) {
                    $unifiedSearchFields[$name] [ $field ] = $def ;
                    $unifiedSearchFields[$name] [ $field ]['value'] = $search_string;
                }

                require_once $beanFiles[$beanName] ;
                $seed = new $beanName();
                require_once 'include/SearchForm/SearchForm2.php' ;
                if ($beanName == "User"
                    || $beanName == "ProjectTask"
                    ) {
                    if (!self::$helperObject->check_modules_access($current_user, $seed->module_dir, 'read')) {
                        continue;
                    } // if
                    if (!$seed->ACLAccess('ListView')) {
                        continue;
                    } // if
                }
                $searchFields = array();
                foreach ($unifiedSearchFields as $key => $value) {
                    foreach ($value as $k => $val) {
                        if($k == 'email'){
                            $searchFields[$key]['email'] = $val;
                        }
                    }
                }
                
                if ($beanName != "User"
                    && $beanName != "ProjectTask"
                    ) {
                    $searchForm = new SearchForm($seed, $name) ;
                    $searchForm->setup(array($name => array()), $searchFields, '', 'saved_views' /* hack to avoid setup doing further unwanted processing */) ;
                    $where_clauses = $searchForm->generateSearchWhere() ;
                    
                    
                    require_once 'include/SearchForm/SearchForm2.php' ;
                    $searchForm = new SearchForm($seed, $name) ;

                    $searchForm->setup(array($name => array()), $searchFields, '', 'saved_views' /* hack to avoid setup doing further unwanted processing */) ;
                    $where_clauses = $searchForm->generateSearchWhere() ;
                    $emailQuery = false;
                    
                    $where = '';
                    if (count($where_clauses) > 0) {
                        $where = '('. implode(' ) OR ( ', $where_clauses) . ')';
                    }

                    $mod_strings = return_module_language($current_language, $seed->module_dir);

                    if (count($select_fields) > 0) {
                        $filterFields = $select_fields;
                    } else {
                        if (file_exists('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php')) {
                            require_once('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php');
                        } else {
                            require_once('modules/'.$seed->module_dir.'/metadata/listviewdefs.php');
                        }

                        $filterFields = array();
                        foreach ($listViewDefs[$seed->module_dir] as $colName => $param) {
                            if (!empty($param['default']) && $param['default'] == true) {
                                $filterFields[] = strtolower($colName);
                            }
                        }
                        if (!in_array('id', $filterFields)) {
                            $filterFields[] = 'id';
                        }
                    }

                    //Pull in any db fields used for the unified search query so the correct joins will be added
                    $selectOnlyQueryFields = array();
                    foreach ($unifiedSearchFields[$name] as $field => $def) {
                        if (isset($def['db_field']) && !in_array($field, $filterFields)) {
                            $filterFields[] = $field;
                            $selectOnlyQueryFields[] = $field;
                        }
                    }

                    //Add the assigned user filter if applicable
                    if (!empty($assigned_user_id) && isset($seed->field_defs['assigned_user_id'])) {
                        $ownerWhere = $seed->getOwnerWhere($assigned_user_id);
                        $where = "($where) AND $ownerWhere";
                    }

                    if ($beanName == "Employee") {
                        $where = "($where) AND users.deleted = 0 AND users.is_group = 0 AND users.employee_status = 'Active'";
                    }
                    if($beanName == 'Account'){
                        $filterFields = array('id','name','billing_address_city','billing_address_country');
                    }else if($beanName == 'Contact'){
                        $filterFields = array('id','first_name','last_name','primary_address_city','primary_address_country');
                    }else if($beanName == 'Lead'){
                        $filterFields = array('id','first_name','last_name','lead_source','website','primary_address_city','primary_address_country');
                    }//end of else
                   
                    $selectOnlyQueryFields = $filterFields;
                    $list_params = array();
                    
                    $ret_array = $seed->create_new_list_query('', $where, $filterFields, $list_params, 0, '', true, $seed, true);
                    
                    if (empty($params) or !is_array($params)) {
                        $params = array();
                    }
                    if (!isset($params['custom_select'])) {
                        $params['custom_select'] = '';
                    }
                    if (!isset($params['custom_from'])) {
                        $params['custom_from'] = '';
                    }
                    if (!isset($params['custom_where'])) {
                        $params['custom_where'] = '';
                    }
                    if (!isset($params['custom_order_by'])) {
                        $params['custom_order_by'] = '';
                    }
                    $main_query = $ret_array['select'] . $params['custom_select'] . $ret_array['from'] . $params['custom_from'] . $ret_array['where'] . $params['custom_where'] . ' ORDER BY date_entered DESC' . $params['custom_order_by'];
                } else {
                    if ($beanName == "User") {
                        $filterFields = array('id', 'user_name', 'first_name', 'last_name', 'email_address');
                        $main_query = "select users.id, ea.email_address, users.user_name, first_name, last_name from users ";
                        $main_query = $main_query . " LEFT JOIN email_addr_bean_rel eabl ON eabl.bean_module = '{$seed->module_dir}' LEFT JOIN email_addresses ea ON (ea.id = eabl.email_address_id) ";
                        $main_query = $main_query . "where ((users.first_name = '{$search_string}') or (users.last_name = '{$search_string}') or (users.user_name = '{$search_string}') or (ea.email_address = '{$search_string}')) and users.deleted = 0 and users.is_group = 0 and users.employee_status = 'Active'";
                    } // if
                    if ($beanName == "ProjectTask") {
                        $filterFields = array('id', 'name', 'project_id', 'project_name');
                        $main_query = "select {$seed->table_name}.project_task_id id,{$seed->table_name}.project_id, {$seed->table_name}.name, project.name project_name from {$seed->table_name} ";
                        $seed->add_team_security_where_clause($main_query);
                        $main_query .= "LEFT JOIN teams ON $seed->table_name.team_id=teams.id AND (teams.deleted=0) ";
                        $main_query .= "LEFT JOIN project ON $seed->table_name.project_id = project.id ";
                        $main_query .= "where {$seed->table_name}.name = '{$search_string}'";
                    } // if
                } // else
                $GLOBALS['log']->info('SugarWebServiceImpl->search_by_module - query = ' . $main_query);
                if ($max_results < -1) {
                    $result = $seed->db->query($main_query);
                } else {
                    if ($max_results == -1) {
                        $limit = $sugar_config['list_max_entries_per_page'];
                    } else {
                        $limit = $max_results;
                    }
                    $result = $seed->db->limitQuery($main_query, $offset,1);
                }
                $detailviewLink = '';
                
                $rowArray = array();
                while ($row = $seed->db->fetchByAssoc($result)) {
                    $detailviewLink = $sugar_config['site_url'].'/index.php?module='.$name.'&action=DetailView&record='.$row['id'];
                    $nameValueArray = array();

                    foreach ($filterFields as $field) {
                        $nameValue = array();
                        $nameValueArray[$field] = self::$helperObject->get_name_value($field, $row[$field]);
                       
                    } // foreach
                    $rowArray[] = $nameValueArray;
                } // while
                $accessData = array();
                $data = $this->vi_get_available_modules($session);

                $accessData = $data['modules'][$name];
                $notesModuleAccess = $data['modules']['Notes'];
                $output_list[] = array('name' => $name,
                                       'moduleLabel' => translate($name), 
                                       'records' => $rowArray,
                                       'detailviewLink' => $detailviewLink,
                                       'isDetailViewSupport' => $accessData['view'],
                                       'isEditPermission' => $accessData['edit'],
                                       'isNoteCreatePermission' => $accessData['edit'],
                                       'isNoteViewPermission' => $accessData['view']);
            } // foreach
            
            $GLOBALS['log']->info('End: SugarWebServiceImpl->search_by_module');
            return array('entry_list'=>$output_list);
        } // if
        return array('entry_list'=>$output_list);
    } // fn

    //get_available_modules
    function vi_get_available_modules($session){
        $filter = "default";
        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->get_available_modules');

        $error = new SoapError();

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->get_available_modules');
            return;
        } // if

        $modules = array();
        $modulesArray = array();
        $availModules = array_keys($_SESSION['avail_modules']); //ACL check already performed.
        foreach ($availModules as $key => $value) {
            if($value == 'Accounts' || $value == 'Leads' || $value == 'Contacts' || $value == 'Notes'){
                $modulesArray[] = $value; 
            }
        }
        switch ($filter) {
            case 'default':
                $modules = self::$helperObject->get_visible_modules($modulesArray);
               break;
            case 'all':
            default:
                $modules = $modulesArray;
        }
        $data = array();
        foreach ($modules as $key => $value) {
            foreach ($value['acls'] as $k => $v) {
                if($v['action'] == 'view' || $v['action'] == 'edit'){
                    $action[$v['action']] = $v['access'];
                }
                $data[$value['module_key']] = $action;      
            }
        }
        $GLOBALS['log']->info('End: SugarWebServiceImpl->get_available_modules');
        return array('modules'=> $data);
    }

    //get_module_fields
    function vi_get_module_fields($session, $module_name, $fields = array()){
        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->get_module_fields for ' . $module_name);
        global  $beanList, $beanFiles;
        $error = new SoapError();

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        $module_fields = array();

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->get_module_fields FAILED on checkSessionAndModuleAccess for ' . $module_name);
            return;
        } // if

        $class_name = $beanList[$module_name];
        require_once($beanFiles[$class_name]);
        $seed = new $class_name();
        $field = $seed->field_name_map;
        if ($seed->ACLAccess('ListView', true) || $seed->ACLAccess('DetailView', true) ||   $seed->ACLAccess('EditView', true)) {
            $return = self::$helperObject->get_return_module_fields($seed, $module_name, $fields);
            $GLOBALS['log']->info('End: SugarWebServiceImpl->get_module_fields SUCCESS for ' . $module_name);

            //get EditView fields - start 
            $addressFieldData = array();
            foreach($field as $value){
                if($value['type'] == 'varchar'){
                    if(isset($value['group'])){
                        $addressFieldData[] = $value['name'];
                    }//end of if
                }//end of if
            }//end of foreach

            require_once('modules/ModuleBuilder/parsers/ParserFactory.php');
            $view_array = ParserFactory::getParser('editview',$module_name);
            $panelArray = $view_array->_viewdefs['panels'];
            $editViewFields = array();
            foreach ($panelArray as $key => $value) {
                foreach ($value as $keys => $values) {
                    foreach($values as $k => $v) {
                        if(array_key_exists($v, $field)) {
                            $editViewFields[] = $v;
                        }//end of if
                    }//end of foreach
                }//end of foreach
            }//end of foreach

            $contextMenuFields = array();
            if($module_name == 'Contacts' || $module_name == 'Leads'){
                $contextMenuFields = array('first_name','last_name','email1','phone_mobile');
            }else if($module_name == 'Accounts'){
                $contextMenuFields = array('name','website','phone_office','email1');
            }

            $editViewFieldsData = array_merge($editViewFields,$addressFieldData);
            $module_fields['module_name'] = $return['module_name'];
            $module_fields['table_name'] = $return['table_name'];
            foreach ($return['module_fields'] as $key => $value) {
                if(in_array($key,$editViewFieldsData)){
                    if($key == 'email1'){
                        $value['required'] = 1;
                    }
                    if(in_array($key, $contextMenuFields)){
                        $value['isInContextMenu'] = 1;
                    }else{
                        $value['isInContextMenu'] = 0;
                    }
                    $moduleFields[$key] = $value;
               }//end of if
            }//end of foreach
            $module_fields['module_fields'] = $moduleFields;
            return $module_fields;
            //get EditView fields - end 
        }
        $error->set_error('no_access');
        self::$helperObject->setFaultObject($error);
        $GLOBALS['log']->error('End: SugarWebServiceImpl->get_module_fields FAILED NO ACCESS to ListView, DetailView or EditView for ' . $module_name);
    }

    //send Email
    function vi_send_email($session,$email_id,$module_name,$record_id,$subject,$content){
        $error = new SoapError();

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->vi_send_email');
            return;
        } // if
        global $current_user;
        require_once('modules/Emails/Email.php');
        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        
        $email = BeanFactory::newBean('Emails');
        $email->from_addr = $defaults['email'];
        $email->from_name = $defaults['name'];
        $email->name = $subject;
        $email->description_html = $content;
        $email->to_addrs_names = $email_id;
        $email->parent_type = $module_name;
        $email->parent_id = $record_id;
        $email->type ="out";
        $email->status = "sent";
        
        if($email->save()){
            return array("msg" => "Email Added Successfully.");
        }else{
            return array("msg" => "Email not Added.");
        }//end of else
    }//end of function

    //get subpanel records
    function vi_get_module_subpanel_records($session,$module_name,$record_id){
        global $sugar_config;
        $error = new SoapError();
        
        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->get_module_fields FAILED on checkSessionAndModuleAccess for ' . $module_name);
            return;
        } // if

        require_once('include/SubPanel/SubPanel.php');
        $subname = array();
        $targetModule = array();
        $subPanelName = array();
        $data = array();
        
        //activities subpanel
        $allActivitiesRecordId = array();

        //tasks
        $selData = "SELECT * FROM tasks WHERE ((parent_id = '$record_id' AND parent_type = '$module_name') OR (contact_id = '$record_id')) AND deleted = 0 AND (tasks.status='Not Started' OR tasks.status='In Progress' OR tasks.status='Pending Input') ORDER BY tasks.date_start";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Tasks','ForActivities',$selDataRow['id']);
            $allActivitiesRecordId[$selDataRow['id']] = $listFiledsValue;
        }
        
        //meetings
        $selData = "SELECT * FROM meetings WHERE parent_id = '$record_id' AND parent_type = '$module_name' AND deleted = 0 AND meetings.status='Planned' ORDER BY meetings.date_start";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Meetings','ForActivities',$selDataRow['id']);
            $allActivitiesRecordId[$selDataRow['id']] = $listFiledsValue;
        }

        //calls
        $selData = "SELECT * FROM calls WHERE parent_id = '$record_id' AND parent_type = '$module_name' AND deleted = 0 AND calls.status='Planned' ORDER BY calls.date_start";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Calls','ForActivities',$selDataRow['id']);
            $allActivitiesRecordId[$selDataRow['id']] = $listFiledsValue;
        }
        
        $data['Activities']['detailViewLink'] = $sugar_config['site_url']."/index.php?module=".$module_name."&action=DetailView&record=".$record_id;
        
        if(empty($allActivitiesRecordId)){
            $data['Activities']['records'] = array();
        }else{
            $allActivitiesRecordId = array_slice($allActivitiesRecordId, -3, 3, true);
            foreach ($allActivitiesRecordId as $key => $value) {
                $data['Activities']['records'][$key] = $value;
            }
        }

        //history
        $allHistoryRecordId = array();

        //tasks
        $selData = "SELECT * FROM tasks WHERE ((parent_id = '$record_id' AND parent_type = '$module_name') OR (contact_id = '$record_id')) AND deleted = 0 AND (tasks.status='Completed' OR tasks.status='Deferred')";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Tasks','ForHistory',$selDataRow['id']);
            $allHistoryRecordId[$selDataRow['id']] = $listFiledsValue;
        }

        //meetings
        $selData = "SELECT * FROM meetings WHERE parent_id = '$record_id' AND parent_type = '$module_name' AND deleted = 0 AND (meetings.status='Held' OR meetings.status='Not Held')";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Meetings','ForHistory',$selDataRow['id']);
            $allHistoryRecordId[$selDataRow['id']] = $listFiledsValue;
        }

        //calls
        $selData = "SELECT * FROM calls WHERE parent_id = '$record_id' AND parent_type = '$module_name' AND deleted = 0 AND (calls.status='Held' OR calls.status='Not Held')";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Calls','ForHistory',$selDataRow['id']);
            $allHistoryRecordId[$selDataRow['id']] = $listFiledsValue;
        }

        //notes
        $selData = "SELECT * FROM notes WHERE (parent_id = '$record_id' AND parent_type = '$module_name') OR (contact_id = '$record_id') AND deleted = 0";
        $selDataResult = $GLOBALS['db']->query($selData);
        while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
            $listFiledsValue = $this->get_subpanel_listview_fields('Notes','ForHistory',$selDataRow['id']);
            $allHistoryRecordId[$selDataRow['id']] = $listFiledsValue;
        }

        //emails
        if($module_name == 'Accounts'){
            $recordBean = BeanFactory::getBean($module_name,$record_id);
            $emailIdList = $this->vi_get_emails_list(array('link'=>'contacts'),$recordBean);

            foreach ($emailIdList as $key => $value) {
                $listFiledsValue = $this->get_subpanel_listview_fields('Emails','ForUnlinkedEmailHistory',$value);
                $allHistoryRecordId[$value] = $listFiledsValue;
            }    
        }else{
            $selData = "SELECT emails.id FROM emails JOIN emails_beans ON emails.id = emails_beans.email_id WHERE parent_id = '$record_id' AND parent_type = '$module_name' AND emails.deleted = 0 AND emails_beans.deleted = 0 AND emails_beans.bean_module = '$module_name'";
            $selDataResult = $GLOBALS['db']->query($selData);
            while($selDataRow = $GLOBALS['db']->fetchByAssoc($selDataResult)){
                $listFiledsValue = $this->get_subpanel_listview_fields('Emails','ForHistory',$selDataRow['id']);
                $allHistoryRecordId[$selDataRow['id']] = $listFiledsValue;
            }
        }
        
        $data['History']['detailViewLink'] = $sugar_config['site_url']."/index.php?module=".$module_name."&action=DetailView&record=".$record_id;

        if(empty($allHistoryRecordId)){
            $data['History']['records'] = array();
        }else{
            $allHistoryRecordId = array_slice($allHistoryRecordId, -3, 3, true);
            foreach ($allHistoryRecordId as $key => $value) {
                $data['History']['records'][$key] = $value;
            }
        }
        
        foreach (SubPanel::getModuleSubpanels($module_name) as $name => $label) {
            $relDef = SugarRelationshipFactory::getInstance()->getRelationshipDef($name);
            $subPanelData = $this->vi_get_module_subpanel_layout($module_name,"default","subpanel");
            if($relDef['relationship_type'] == "one-to-many"){
                if($relDef['rhs_module'] == $module_name){
                    $targetModule[] = $relDef['lhs_module'];
                    $subPanelName[$relDef['lhs_module']] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }else{
                    $targetModule[] = $relDef['rhs_module'];
                    $subPanelName[$relDef['rhs_module']] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }
            }else if($relDef['relationship_type'] == "many-to-many"){
                if($relDef['lhs_module'] == $module_name){
                    $targetModule[] = $relDef['rhs_module'];
                    $subPanelName[$relDef['rhs_module']] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }else{
                    $targetModule[] = $relDef['lhs_module'];
                    $subPanelName[$relDef['lhs_module']] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }
            }else{
                if($name == "aos_products"){
                    $targetModule[] = "AOS_Products";
                    $subPanelName['AOS_Products'] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }elseif($name == "projecttask"){
                    $targetModule[] = "ProjectTask";
                    $subPanelName['ProjectTask'] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }elseif($name == "sub_categories"){
                    $targetModule[] = "AOS_Product_Categories";
                    $subPanelName['AOS_Product_Categories'] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }elseif($name == "securitygroups"){
                    $targetModule[] = "SecurityGroups";
                    $subPanelName['SecurityGroups'] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];
                }else{
                    $targetModule[] = ucwords($name);
                    $subPanelName[ucwords($name)] = $subPanelData['data']['subpanel'][$name]['subpanel_name'];  
                }
            }
            $subname[] = sugar_ucfirst((!empty($label)) ? translate($label, $module_name) : $name);
        }
        foreach($targetModule as $key => $value) {
            if($value == 'Aos_products_purchases'){
                unset($targetModule[$key]);
            }
            if($value == 'Therevisions'){
                $targetModule[$key] = 'DocumentRevisions';
            }
            if($value == 'Opportunities_aos_contracts'){
                $targetModule[$key] = 'AOS_Contracts';
            }
            if($value == 'Products_services_purchased' || $value == "Delegates" || $value == 'Aos_products_purchases'){
                unset($targetModule[$key]);
            }
        }
        if(!empty($subname)){
            foreach($subname as $key => $value) {
                if($value == 'Products and Services Purchased' || $value == "Delegates" || $value == 'Purchases'){
                    unset($subname[$key]);
                }
            }
        }
        
        $subname = array_values($subname);
        $val = '';
        $targetModuleList = array_unique(array_combine($targetModule, $subname));

        foreach ($targetModuleList as $subPanelModule => $value) {
            $moduleSubPanelRecordId = $this->fetchRelatedModuleRecordsId($module_name,$record_id,$subPanelModule);
            
            foreach ($moduleSubPanelRecordId as $key => $val) {
                $moduleName = translate($key);
                $val = array_slice($val, -3, 3, true);
                foreach ($val as $k => $recordId) {
                    $data[$moduleName]['detailViewLink'] = $sugar_config['site_url']."/index.php?module=".$module_name."&action=DetailView&record=".$record_id;
                    if($recordId == 'Record Not Found'){
                        $data[$moduleName]['records'] = array();
                    }else{
                        $listFields = $this->get_subpanel_listview_fields($subPanelModule,$subPanelName[$subPanelModule],$recordId);
                        $data[$moduleName]['records'][$recordId] = $listFields;
                    }
                }
                
            }
        }
        return array("relatedmodule" => $data);
    }//end of function

    function vi_get_module_subpanel_layout($module_name, $a_type, $a_view){
        $acl_check = true;
        $md5 = false;
        $GLOBALS['log']->info('Begin: SugarWebServiceImpl->get_module_layout');

        global  $beanList, $beanFiles;
        
        $class_name = $beanList[$module_name];
        require_once($beanFiles[$class_name]);
        $seed = new $class_name();
            
            
        $aclViewCheck = (strtolower($a_view) == 'subpanel') ? 'DetailView' : ucfirst(strtolower($a_view)) . 'View';
                
        if (!$acl_check || $seed->ACLAccess($aclViewCheck, true)) {
            $a_vardefs = self::$helperObject->get_module_view_defs($module_name, $a_type, $a_view);
            if ($md5) {
                $results[$a_view] = md5(serialize($a_vardefs));
            } else {
                $results[$a_view] = $a_vardefs;
            }
        }
        
        $GLOBALS['log']->info('End: SugarWebServiceImpl->get_module_layout ->> '.print_r($results, true));

        return array('data' => $results);
    }

    function fetchRelatedModuleRecordsId($primaryModule,$recordId,$subPanelModule){
        $relatedRecordIds = array();
        if($primaryModule == 'Documents' && $subPanelModule == 'DocumentRevisions'){
            $selDocumentId = "SELECT id FROM document_revisions WHERE document_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
            $selDocumentIdResult = $GLOBALS['db']->query($selDocumentId);
            $documentId = array();
            while($selDocumentIdRow = $GLOBALS['db']->fetchByAssoc($selDocumentIdResult)){
                $documentId[] = $selDocumentIdRow['id'];
            }
            $relatedRecordIds[$subPanelModule] = $documentId;
        }else{
            $bean = BeanFactory::getBean($primaryModule,$recordId);
            $relatedBean = BeanFactory::newBean($subPanelModule);
            $tablename = $relatedBean->getTableName();
            if($tablename == "project_task"){
                $tablename = "projecttask";
            }
            if($bean->load_relationship($tablename)){
                if(!empty($bean->$tablename->get())){
                    $relatedRecordIds[$subPanelModule] = $bean->$tablename->get();
                }else{
                    $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                }
            }else{
                if($primaryModule != ''){
                    $rhsModule = "";
                    $lhsModule = "";
                    foreach(SubPanel::getModuleSubpanels($primaryModule) as $name => $label){
                        $relDef = SugarRelationshipFactory::getInstance()->getRelationshipDef($name);
                        $relName = $relDef['name'];
                        if($name == "surveys_surveyresponses"){
                            $selSurveyResponseId = "SELECT id FROM surveyresponses WHERE survey_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                            $selSurveyResponseIdResult = $GLOBALS['db']->query($selSurveyResponseId);
                            $surveyResponseId = array();
                            while($selSurveyResponseRow = $GLOBALS['db']->fetchByAssoc($selSurveyResponseIdResult)){
                                $surveyResponseId[] = $selSurveyResponseRow['id'];
                            }
                            if(!empty($surveyResponseId)){
                                $relatedRecordIds[$subPanelModule] = $surveyResponseId;
                            }else{
                                $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                            }
                        }
                        if($name == 'therevisions'){
                            $selDocumentId = "SELECT id FROM document_revisions WHERE document_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                            $selDocumentIdResult = $GLOBALS['db']->query($selDocumentId);
                            $documentId = array();
                            while($selDocumentIdRow = $GLOBALS['db']->fetchByAssoc($selDocumentIdResult)){
                                $documentId[] = $selDocumentIdRow['id'];
                            }
                            if(!empty($documentId)){
                                $relatedRecordIds[$subPanelModule] = $documentId;
                            }else{
                                $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                            }
                        }
                        if($name == "sub_categories"){
                            $selProductCategoryId = "SELECT id FROM aos_product_categories WHERE parent_category_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                            $selProductCategoryIdResult = $GLOBALS['db']->query($selProductCategoryId);
                            $productCategoryId = array();
                            while($selProductCategoryRow = $GLOBALS['db']->fetchByAssoc($selProductCategoryIdResult)){
                                $productCategoryId[] = $selProductCategoryRow['id'];
                            }
                            if(!empty($productCategoryId)){
                                $relatedRecordIds[$subPanelModule] = $productCategoryId;
                            }else{
                                $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                            }
                        }
                        if(isset($relDef['relationships'])){
                            $rhsModule = $relDef['relationships'][$relName]['rhs_module'];
                            $lhsModule = $relDef['relationships'][$relName]['lhs_module'];
                            $relNames = array();
                            if($subPanelModule == $rhsModule || $subPanelModule == $lhsModule){
                                $relNames[] = $relName;
                                foreach ($relNames as $k => $v) {
                                    if($bean->load_relationship($v)){
                                        if(!empty($bean->$v->get())){
                                            $relatedRecordIds[$subPanelModule] = $bean->$v->get();
                                        }else{
                                            $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                        }           
                                    }
                                }
                            }else{
                                if($subPanelModule == "SecurityGroups"){
                                    $selSecurityGroupId = "SELECT securitygroup_id FROM securitygroups_records WHERE record_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                    $selSecurityGroupIdResult = $GLOBALS['db']->query($selSecurityGroupId);
                                    $securityGroupsId = array();
                                    while($selSecurityGroupRow = $GLOBALS['db']->fetchByAssoc($selSecurityGroupIdResult)){
                                        $securityGroupsId[] = $selSecurityGroupRow['securitygroup_id'];
                                    }
                                    if(!empty($securityGroupsId)){
                                        $relatedRecordIds[$subPanelModule] = $securityGroupsId;
                                    }else{
                                        $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                    }
                                }
                                if($subPanelModule == "Accounts"){
                                    $sql = "SELECT id FROM accounts WHERE parent_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                    $result = $GLOBALS['db']->query($sql);
                                    $accountsId = array();
                                    while($row = $GLOBALS['db']->fetchByAssoc($result)){
                                        $accountsId[] = $row['id'];
                                    }
                                    if(!empty($accountsId)){
                                        $relatedRecordIds[$subPanelModule] = $accountsId;
                                    }else{
                                        $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                    }
                                }

                                if($subPanelModule == "Contacts" && $primaryModule == 'Contacts'){
                                    $sql = "SELECT reports_to_id FROM contacts WHERE id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                    $result = $GLOBALS['db']->query($sql);
                                    $contactsId = array();
                                    while($row = $GLOBALS['db']->fetchByAssoc($result)){
                                        if($row['reports_to_id'] != ''){
                                            $contactsId[] = $row['reports_to_id'];
                                        }
                                    }
                                    if(!empty($contactsId)){
                                         $relatedRecordIds[$subPanelModule] = $contactsId;
                                    }else{
                                        $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                    }
                                }
                            }
                        }else{
                            if($subPanelModule == "Accounts"){
                                $sql = "SELECT id FROM accounts WHERE parent_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                $result = $GLOBALS['db']->query($sql);
                                $accountsId = array();
                                while($row = $GLOBALS['db']->fetchByAssoc($result)){
                                    $accountsId[] = $row['id'];
                                }
                                if(!empty($accountsId)){
                                     $relatedRecordIds[$subPanelModule] = $accountsId;
                                }else{
                                    $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                }
                            }
                            if($subPanelModule == "Contacts" && $primaryModule == 'Contacts'){
                                $sql = "SELECT reports_to_id FROM contacts WHERE id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                $result = $GLOBALS['db']->query($sql);
                                $contactsId = array();
                                while($row = $GLOBALS['db']->fetchByAssoc($result)){
                                    if($row['reports_to_id'] != ''){
                                        $contactsId[] = $row['reports_to_id'];
                                    }
                                }
                                if(!empty($contactsId)){
                                     $relatedRecordIds[$subPanelModule] = $contactsId;
                                }else{
                                    $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                }
                            }
                            if($subPanelModule == "SecurityGroups"){
                                $sql = "SELECT securitygroup_id FROM securitygroups_records WHERE record_id = '$recordId' AND deleted = 0 ORDER BY date_entered ASC";
                                $result = $GLOBALS['db']->query($sql);
                                $securityGroupsId = array();
                                while($row = $GLOBALS['db']->fetchByAssoc($result)){
                                    $securityGroupsId[] = $row['securitygroup_id'];
                                }
                                if(!empty($securityGroupsId)){
                                     $relatedRecordIds[$subPanelModule] = $securityGroupsId;
                                }else{
                                    $relatedRecordIds[$subPanelModule] = array('Record Not Found');
                                }
                            }
                        }
                    }
                }
            }
        }
        return $relatedRecordIds;
    }

    function get_subpanel_listview_fields($moduleName,$subpanelName,$recordId){
        global $sugar_config;
        if ( file_exists( 'modules/' . $moduleName . '/metadata/subpanels/'.$subpanelName.'.php') )
            require('modules/' . $moduleName . '/metadata/subpanels/'.$subpanelName.'.php');
  
        if ( file_exists( 'custom/modules/' . $moduleName . '/metadata/subpanels/'.$subpanelName.'.php'))
            require('custom/modules/' . $moduleName . '/metadata/subpanels/'.$subpanelName.'.php');
        
        $moduleBean =  BeanFactory::getBean($moduleName,$recordId);
        $fields = $moduleBean->getFieldDefinitions();
        
        $fieldValues = array();
        foreach ($subpanel_layout['list_fields'] as $key => $value) {
            if(!isset($value['usage'])){
                if(array_key_exists($key,$fields)){
                    $fieldDef = $fields[$key];
                    $fieldLabel = translate($fieldDef['vname'],$moduleName);
                    if(strpos($fieldLabel, ':')){
                        $fieldLabel = substr_replace($fieldLabel, "", -1);
                    }//end of if
                    $fieldValues[$fieldLabel] = $moduleBean->$key;
                }
            }
        }
        
        $fieldValues['detailViewLink'] = $sugar_config['site_url'].'/index.php?module='.$moduleName.'&action=DetailView&record='.$recordId;
        ksort($fieldValues);
        return $fieldValues;      
    }

    function vi_get_emails_list($params,$bean) {
        $relation = $params['link'];
        if (empty($bean->$relation)) {
            $bean->load_relationship($relation);
        }
        if (empty($bean->$relation)) {
            $GLOBALS['log']->error("Bad relation '$relation' for bean '{$bean->object_name}' id '{$bean->id}'");

            return array();
        }
        $rel_module = $bean->$relation->getRelatedModuleName();
        $rel_join = $bean->$relation->getJoin(array(
            'join_table_alias' => 'link_bean',
            'join_table_link_alias' => 'linkt',
        ));
        $rel_join = str_replace("{$bean->table_name}.id", "'{$bean->id}'", $rel_join);
        $return_array['select'] = 'SELECT DISTINCT emails.id ';
        $return_array['from'] = 'FROM emails ';

        $return_array['join'] = array();

        // directly assigned emails
        $return_array['join'][] = "
            SELECT
                eb.email_id,
                'direct' source
            FROM
                emails_beans eb
            WHERE
                eb.bean_module = '{$bean->module_dir}'
                AND eb.bean_id = '{$bean->id}'
                AND eb.deleted=0
        ";

        // Related by directly by email
        $return_array['join'][] = "
            SELECT DISTINCT
                eear.email_id,
                'relate' source
            FROM
                emails_email_addr_rel eear
            INNER JOIN
                email_addr_bean_rel eabr
            ON
                eabr.bean_id ='{$bean->id}'
                AND eabr.bean_module = '{$bean->module_dir}'
                AND eabr.email_address_id = eear.email_address_id
                AND eabr.deleted=0
            WHERE
                eear.deleted=0
        ";

        $showEmailsOfRelatedContacts = empty($bean->field_defs[$relation]['hide_history_contacts_emails']);
        if (!empty($GLOBALS['sugar_config']['hide_history_contacts_emails']) && isset($GLOBALS['sugar_config']['hide_history_contacts_emails'][$bean->module_name])) {
            $showEmailsOfRelatedContacts = empty($GLOBALS['sugar_config']['hide_history_contacts_emails'][$bean->module_name]);
        }
        if ($showEmailsOfRelatedContacts) {
            // Assigned to contacts
            $return_array['join'][] = "
                SELECT DISTINCT
                    eb.email_id,
                    'contact' source
                FROM
                    emails_beans eb
                $rel_join AND link_bean.id = eb.bean_id
                WHERE
                    eb.bean_module = '$rel_module'
                    AND eb.deleted=0
            ";
            // Related by email to linked contact
            $return_array['join'][] = "
                SELECT DISTINCT
                    eear.email_id,
                    'relate_contact' source
                FROM
                    emails_email_addr_rel eear
                INNER JOIN
                    email_addr_bean_rel eabr
                ON
                    eabr.email_address_id=eear.email_address_id
                    AND eabr.bean_module = '$rel_module'
                    AND eabr.deleted=0
                $rel_join AND link_bean.id = eabr.bean_id
                WHERE
                    eear.deleted=0
            ";
        }

        $return_array['join'] = ' INNER JOIN (' . implode(' UNION ', $return_array['join']) . ') email_ids ON emails.id=email_ids.email_id ';

        $return_array['where'] = ' WHERE emails.deleted=0 ';

        //$return_array['join'] = '';
        $return_array['join_tables'][0] = '';

        if ($bean->object_name == 'Case' && !empty($bean->case_number)) {
            $where = str_replace('%1', $bean->case_number, $bean->getEmailSubjectMacro());
            $return_array['where'] .= "\n AND (email_ids.source = 'direct' OR emails.name LIKE '%$where%')";
        }
        $selEmails = $return_array['select'].$return_array['from'].$return_array['join'].$return_array['where'];
        $selEmailsResult = $GLOBALS['db']->query($selEmails);
        $emailsId = array();
        while($selEmailsRow = $GLOBALS['db']->fetchByAssoc($selEmailsResult)){
            $emailsId[] = $selEmailsRow['id'];
        }
        return $emailsId;
    }

    function vi_get_return_value_for_fields($value, $module, $fields) {
        global $sugar_config;
        $GLOBALS['log']->info('Begin: SoapHelperWebServices->get_return_value_for_fields');
        global $current_user;
        
        if ($module == 'Users' && $value->id != $current_user->id) {
            $value->user_hash = '';
        }
        $value = clean_sensitive_data($value->field_defs, $value);
        
        $GLOBALS['log']->info('End: SoapHelperWebServices->get_return_value_for_fields');

        return array(
            'id' => $value->id,
            'module_name' => $module,
            'detailview_url' => $sugar_config['site_url'].'/index.php?module='.$module.'&action=DetailView&record='.$value->id,
            'name_value_list' => $this->vi_get_name_value_list_for_fields($value, $fields)
        );
    }

    function vi_get_name_value_list_for_fields($value, $fields) {
        $GLOBALS['log']->info('Begin: SoapHelperWebServices->get_name_value_list_for_fields');
        global $app_list_strings;
        global $invalid_contact_fields;

        $list = array();
        if (!empty($value->field_defs)) {

            if (empty($fields)) {
                $fields = array_keys($value->field_defs);
            }
            if (isset($value->assigned_user_name) && in_array('assigned_user_name', $fields)) {
                $list['assigned_user_name'] = $this->vi_get_name_value('assigned_user_name', $value->assigned_user_name,"relate");
            }
            if (isset($value->modified_by_name) && in_array('modified_by_name', $fields)) {
                $list['modified_by_name'] = $this->vi_get_name_value('modified_by_name', $value->modified_by_name,"relate");
            }
            if (isset($value->created_by_name) && in_array('created_by_name', $fields)) {
                $list['created_by_name'] = $this->vi_get_name_value('created_by_name', $value->created_by_name,"relate");
            }

            $filterFields = self::$helperObject->filter_fields($value, $fields);


            foreach ($filterFields as $field) {
                $var = $value->field_defs[$field];
                if (isset($value->{$var['name']})) {
                    $val = $value->{$var['name']};
                    $type = $var['type'];

                    if (strcmp($type, 'date') == 0) {
                        $val = substr($val, 0, 10);
                    } elseif (strcmp($type, 'enum') == 0 && !empty($var['options'])) {
                        //$val = $app_list_strings[$var['options']][$val];
                    }
                    if($var['name'] == 'date_entered'){
                        $val = $this->vi_gettimelapse($val);
                    }
                    $list[$var['name']] = $this->vi_get_name_value($var['name'], $val,$type);
                } // if
            } // foreach
        } // if
        $GLOBALS['log']->info('End: SoapHelperWebServices->get_name_value_list_for_fields');
        if (self::$helperObject->isLogLevelDebug()) {
            $GLOBALS['log']->debug('SoapHelperWebServices->get_name_value_list_for_fields - return data = ' . var_export(
                $list,
                    true
            ));
        } // if

        return $list;
    } // fn

    function vi_get_name_value($field, $value,$type) {
        return array('name' => $field, 'value' => $value, 'type' => $type);
    }

    function vi_get_related_field_data($session,$module_name,$record_id){
        $error = new SoapError();

        $activeGsyncGmailVal = $this->vi_checkgsyncgmailstatus();            
        if($activeGsyncGmailVal == 0){
            $error->set_error('error_gsync_gmail');
            self::$helperObject->setFaultObject($error);
            return;
        }//end of if

        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
            $GLOBALS['log']->error('End: SugarWebServiceImpl->get_entry_list - FAILED on checkSessionAndModuleAccess');
            return;
        } // if

        $allRecordIds = get_bean_select_array(false, get_singular_bean_name($module_name), "deleted","");

        $recordsData = array();
        foreach($allRecordIds as $key => $value){
            $bean = BeanFactory::getBean($module_name,$key);
            $recordsData[] = array("id" => $key, "label" => $bean->name);
        }

        $data = array("id" => $record_id,"records" => $recordsData);

        return $data;
    }

    function vi_gettimelapse($startDate) {
        global $timedate;

        $nowTs = $timedate->getNow()->ts;

        if (null !== ($userStartDate = $timedate->fromUser($startDate))) {
            $userStartDateTs = $userStartDate->ts;
        } else {
            LoggerManager::getLogger()->warn('Invalid $startDate');

            return '';
        }

        $seconds = $nowTs - $userStartDateTs;
        $minutes = $seconds / 60;
        $seconds = $seconds % 60;
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        $days = floor($hours / 24);
        $hours = $hours % 24;
        $weeks = floor($days / 7);
        $days = $days % 7;
        $result = '';
        if ($weeks == 1) {
            return translate('LBL_TIME_LAST_WEEK', 'SugarFeed') . ' ';
        } elseif ($weeks > 1) {
            $result .= $weeks . ' ' . translate('LBL_TIME_WEEKS', 'SugarFeed') . ' ';
            if ($days > 0) {
                $result .= ' ' . translate('LBL_TIME_AND', 'SugarFeed') . ' ';
                $result .= $days . ' ' . translate('LBL_TIME_DAYS', 'SugarFeed') . ' ';
            }
        } else {
            if ($days == 1) {
                $result .= $days . ' day ';
            } elseif ($days > 1) {
                $result .= $days . ' ' . translate('LBL_TIME_DAYS', 'SugarFeed') . ' ';
            } else {
                if ($hours == 1) {
                    $result .= $hours . ' ' . translate('LBL_TIME_HOUR', 'SugarFeed') . ' ';
                } else {
                    if($hours != 0){
                        $result .= $hours . ' ' . translate('LBL_TIME_HOURS', 'SugarFeed') . ' ';
                    }
                }
                if ($hours < 6) {
                    if ($minutes == 1) {
                        $result .= $minutes . ' ' . translate('LBL_TIME_MINUTE', 'SugarFeed') . ' ';
                    } else {
                        $result .= $minutes . ' ' . translate('LBL_TIME_MINUTES', 'SugarFeed') . ' ';
                    }
                }
                if ($hours == 0 && $minutes == 0) {
                    if ($seconds == 1) {
                        $result = $seconds . ' ' . translate('LBL_TIME_SECOND', 'SugarFeed') . ' ';
                    } else {
                        $result = $seconds . ' ' . translate('LBL_TIME_SECONDS', 'SugarFeed') . ' ';
                    }
                }
            }
        }

        return $result.''.translate('LBL_TIME_AGO', 'SugarFeed');
    }

    function vi_checkgsyncgmailstatus(){
        global $error_defs;
        $error_defs['error_gsync_gmail']['name'] = 'Gsync for Gmail Feature';
        $error_defs['error_gsync_gmail']['number'] = 2020;
        $error_defs['error_gsync_gmail']['description'] = 'Gsync for Gmail Feature has disabled from SuiteCRM. Please Enable it for Using feature.';

        $selectGsyncGmailConfig = "SELECT * FROM vi_gsync_gmail_config";
        $selectGsyncGmailConfigRow = $GLOBALS['db']->fetchOne($selectGsyncGmailConfig);

        if(!empty($selectGsyncGmailConfigRow)){
            $activeGsyncGmailVal = $selectGsyncGmailConfigRow['active_gsync_gmail'];
        }else{
            $activeGsyncGmailVal = 0;
        }//end of else

        return $activeGsyncGmailVal;
    }
}