<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : tables.php
// Description : File to handle tables information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 01-03-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------

	global $glb_api_tables;
	$glb_api_tables['admin'] = 'tbl_admin';
	$glb_api_tables['admin_column'] = array(
				       	'ad_id'    	  	=> 'p1ad_id',
					'ad_email'    		=> 'p1ad_email',
				       	'ad_username'    	=> 'p1ad_username',
					'ad_pass'    		=> 'p1ad_pass',
					'ad_lastlogin'    	=> 'p1ad_lastlogin',
					'ad_stflag'    		=> 'p1ad_stflag',
					'ad_added'    		=> 'p1ad_added',
					'ad_updated'    	=> 'p1ad_updated',
					'cms_account_id'        => 'p1cms_account_id'
					       );
	$glb_api_tables['admin_log'] = 'tbl_admin_log';
	$glb_api_tables['admin_log_column'] = array(
				       	'al_id'    	  	=> 'p1al_id',
					'ad_id'    		=> 'p1ad_id',
				       	'al_session_id'    	=> 'p1al_session_id',
					'al_user_agent'    	=> 'p1al_user_agent',
					'al_logged_in_on'    	=> 'p1al_logged_in_on',
					'al_session_starts_at'  => 'p1al_session_starts_at',
					'al_logged_out_on'    	=> 'p1al_logged_out_on',
					'al_ip_addr'    	=> 'p1al_ip_addr',
					'al_ct_name'    	=> 'p1al_ct_name',
					'st_id'    		=> 'p1st_id',
					'co_id'    		=> 'p1co_id',
					'al_stflag'    		=> 'p1al_stflag',
					'al_session_status'    	=> 'p1al_session_status'
					       );
	$glb_api_tables['admin_profile'] = 'tbl_admin_profile';
	$glb_api_tables['admin_profile_column'] = array(
				       	'ap_id'    	  	=> 'p1ap_id',
					'ad_id'    		=> 'p1ad_id',
				       	'ap_fullname'    	=> 'p1ap_fullname',
					'ap_nickname'    	=> 'p1ap_nickname',
					'ap_display_name'    	=> 'p1ap_display_name',
					'ap_signature'    	=> 'p1ap_signature'
					       );
	$glb_api_tables['admin_visit'] = 'tbl_admin_visit';
	$glb_api_tables['admin_visit_column'] = array(
				       	'av_id'    	  	=> 'p1av_id',
					'al_id'    		=> 'p1al_id',
				       	'av_title'    		=> 'p1av_title',
					'av_url'    		=> 'p1av_url',
					'av_date'    		=> 'p1av_date',
					'av_stflag'    		=> 'p1av_stflag'
					       );
	$glb_api_tables['categories'] = 'tbl_categories';
	$glb_api_tables['categories_column'] = array(
				       	'ca_id'    	  	=> 'p1ca_id',
					'me_id'    		=> 'p1me_id',
				       	'ca_parent'    		=> 'p1ca_parent',
					'ca_name'    		=> 'p1ca_name',
					'ca_description'    	=> 'p1ca_description',
					'ca_longdesc'    	=> 'p1ca_longdesc',
					'ca_image'    		=> 'p1ca_image',
					'ca_order'    		=> 'p1ca_order',
					'ca_stflag'   	 	=> 'p1ca_stflag',
					'ca_added'    		=> 'p1ca_added',
					'ca_updated'    	=> 'p1ca_updated'
					       );

	$glb_api_tables['country'] = 'tbl_country';
	$glb_api_tables['country_column'] = array(
				       	'co_id'    	  	=> 'p1co_id',
					'co_name'    	  	=> 'p1co_name',
				       	'co_code'    		=> 'p1co_code',
					'co_location'    	=> 'p1co_location',
					'co_stflag'    		=> 'p1co_stflag',
					'co_added'  		=> 'p1co_added'
					
					);
	$glb_api_tables['currency'] = 'tbl_currency';
	$glb_api_tables['currency_column'] = array(
				       	'cu_id'    	  	=> 'p1cu_id',
					'cu_name'    	  	=> 'p1cu_name',
				       	'cu_symbol'    		=> 'p1cu_symbol',
					'cu_code'    		=> 'p1cu_code',
					'cu_description'    	=> 'p1cu_description',
					'cu_ratio' 	 	=> 'p1cu_ratio',
					'cu_stflag'  		=> 'p1cu_stflag',
					'cu_added'  		=> 'p1cu_added',
					'cu_updated'  		=> 'p1cu_updated'
					);

	$glb_api_tables['languages'] = 'tbl_languages';
	$glb_api_tables['languages_column'] = array(
				       	'lg_id'    	  	=> 'p1lg_id',
					'lg_name'    	  	=> 'p1lg_name',
				       	'lg_code'    		=> 'p1lg_code',
					'lg_description'    	=> 'p1lg_description',
					'lg_charset'    	=> 'p1lg_charset',
					'lg_co_id' 	 	=> 'p1co_id',
					'lg_stflag' 	 	=> 'p1lg_stflag',
					'lg_added'  		=> 'p1lg_added'
					);

	$glb_api_tables['merchants'] = 'tbl_merchants';
	$glb_api_tables['merchants_column'] = array(
				       	'me_id'    	  	=> 'p1me_id',
					'me_email'    	  	=> 'p1me_email',
				       	'me_pass'    	  	=> 'p1me_pass',
					'me_lastlogin'    	=> 'p1me_lastlogin',
					'me_stflag'    		=> 'p1me_stflag',
					'me_api_username'    	=> 'p1me_api_username',
					'me_api_pass'    	=> 'p1me_api_pass',
					'me_client_id'    	=> 'p1me_client_id',
					'me_secret_phrase'   	=> 'p1me_secret_phrase',
					'me_ai_username'	=> 'p1me_ai_username',
					'me_ai_pass'		=> 'p1me_ai_pass',
					'me_tollfree_num'   	=> 'p1me_tollfree_num',
					'cms_nickname'      	=> 'p1me_cms_nickname',
					'me_device_priority'	=> 'p1me_device_priority',
					'sq_id' 		=> 'p1sq_id',
					'me_sq_answer'		=> 'p1me_sq_answer',
					'me_added'		=> 'p1me_added',
					'me_updated'		=> 'p1me_updated',
					'cms_account_id'        => 'p1cms_account_id'
					       );
	$glb_api_tables['merchants_profile'] = 'tbl_merchants_profile';
	$glb_api_tables['merchants_profile_column'] = array(
				       	'mp_id'    	  	=> 'p1mp_id',
					'me_id'    	  	=> 'p1me_id',
					'mp_firstname'    	=> 'p1mp_firstname',
					'mp_lastname'    	=> 'p1mp_lastname',
					'mp_company'    	=> 'p1mp_company',
					'mp_position'    	=> 'p1mp_position',
					'mp_mobile'    		=> 'p1mp_mobile',
					'mp_website'            => 'p1mp_website',
					'mp_address1'		=> 'p1mp_address1',
					'mp_address2'		=> 'p1mp_address2',
					'mp_zipcode'		=> 'p1mp_zipcode',
					'mp_ct_name'		=> 'p1mp_ct_name',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'tz_id'			=> 'p1tz_id',
					'lg_id'			=> 'p1lg_id',
					'cu_id'			=> 'p1cu_id',
					'mp_pbeneficiary'	=> 'p1mp_pbeneficiary',
					'mp_pbank'		=> 'p1mp_pbank',
					'mp_paddress1'		=> 'p1mp_paddress1',
					'mp_paddress2'		=> 'p1mp_paddress2',
					'mp_pcity'		=> 'p1mp_pcity',
					'mp_pstate'		=> 'p1mp_pstate',
					'mp_pcountry'		=> 'p1mp_pcountry',
					'mp_pzip'		=> 'p1mp_pzip',
					'mp_paccount'		=> 'p1mp_paccount',
					'mp_pibanno'		=> 'p1mp_pibanno',
					'mp_pswiftcode'		=> 'p1mp_pswiftcode',
					'mp_psortcode'		=> 'p1mp_psortcode',
					'mp_pclearing'		=> 'p1mp_pclearing',
					'mp_stpflag'		=> 'p1mp_stpflag',
					'mp_pupdated'		=> 'p1mp_pupdated'
				       );
	$glb_api_tables['merchants_sup_profile'] = 'tbl_merchants_support_profile';
	$glb_api_tables['merchants_sup_profile_column'] = array(
				       	'msp_id'    		=> 'p1msp_id',
					'me_id'    	  	=> 'p1me_id',
				       	'msp_corp_name'   	=> 'p1msp_corp_name',
					'msp_service_name'  	=> 'p1msp_service_name',
					'msp_website_url'   	=> 'p1msp_website_url',
					'msp_notify_url'   	=> 'p1msp_notify_url',
					'msp_return_url'   	=> 'p1msp_return_url',
					'msp_return_closeiFrame'  => 'p1msp_return_closeiFrame',
					'msp_failure_url'   	=> 'p1msp_failure_url',
					'msp_failure_closeiFrame' => 'p1msp_failure_closeiFrame',
					'msp_address1'      	=> 'p1msp_address1',
					'msp_address2'      	=> 'p1msp_address2',
					'msp_zipcode'    	=> 'p1msp_zipcode',
					'msp_ct_name'   	=> 'p1msp_ct_name',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'msp_tollfree_num'	=> 'p1msp_tollfree_num',
					'msp_email'		=> 'p1msp_email',
					'msp_stflag'		=> 'p1msp_stflag',
					'msp_updated'		=> 'p1msp_updated'
				       );
	$glb_api_tables['merchants_log'] = 'tbl_merchants_log';
	$glb_api_tables['merchants_log_column'] = array(
				       	'ml_id'    	  	=> 'p1ml_id',
					'me_id'    	  	=> 'p1me_id',
				       	'ml_session_id'    	=> 'p1ml_session_id',
					'ml_user_agent'    	=> 'p1ml_user_agent',
					'ml_logged_in_on'    	=> 'p1ml_logged_in_on',
					'ml_session_starts_at'  => 'p1ml_session_starts_at',
					'ml_logged_out_on'    	=> 'p1ml_logged_out_on',
					'ml_ip_addr'    	=> 'p1ml_ip_addr',
					'ml_ct_name'       	=> 'p1ml_ct_name',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'ml_stflag'		=> 'p1ml_stflag',
					'ml_session_status'	=> 'p1ml_session_status'
					);
	$glb_api_tables['merchantservices'] = 'tbl_merchant_services';
	$glb_api_tables['merchantservices_column'] = array(
				       	'ser_id'    	  	=> 'p1ms_id',
					'ser_merchantid'  	=> 'p1me_id',
				     	'ser_name'    		=> 'p1ms_name',
					'ser_logo'    		=> 'p1ms_logo',
					'ser_comments'  	=> 'p1ms_comments',
					'ser_authid'  		=> 'p1au_id',
                   			'ser_payment_id'    	=> 'p1_payment_id',
					'ser_catid'  		=> 'p1ca_id',
					'ser_teid'  		=> 'p1te_id',
                    			'ser_te_spid'       	=> 'p1te_spid',
                    			'ser_adminid'  	  	=> 'p1ad_id',
                     			'ser_pimg_id'      	=> 'p1pimg_id',
					'ser_currid'   	  	=> 'p1cu_id',
					'ser_tflag'  		=> 'p1ms_tflag',
					'ser_iflag'  		=> 'p1ms_iflag',
					'ser_number_auto'   	=> 'p1ms_number_auto',
                   			 'ser_stflag'        	=> 'p1ms_stflag',
					'ser_added'  		=> 'p1ms_added',
					'ser_updated'  		=> 'p1ms_updated'
					);
	$glb_api_tables['merchantsitems'] = 'tbl_merchants_items';
	$glb_api_tables['merchantsitems_column'] = array(
				       	'mitem_id'    	  	=> 'p1mi_id',
					'mitem_mserID' 	  	=> 'p1ms_id',
				       	'mitem_merchantId'	=> 'p1me_id',
					'mitem_sessId'    	=> 'p1mi_sessId',
					'mitem_name'    	=> 'p1mi_name',
					'mitem_code' 	 	=> 'p1mi_code',
					'mitem_image'  		=> 'p1mi_image',
					'mitem_des'  		=> 'p1mi_description',
					'mitem_stflag'  	=> 'p1mi_stflag',
					'mitem_added'  		=> 'p1mi_added',
					'mitem_updated'  	=> 'p1mi_updated',
					'p1cms_product_id' 	=> 'p1cms_product_id'					
					);
	
	$glb_api_tables['mitemattr'] = 'tbl_merchants_item_attribs';
	$glb_api_tables['mitemattr_column'] = array(
				       	'mattr_id'    	  	=> 'p1ma_id',
					'mattr_itmid' 	  	=> 'p1mi_id',
				       	'mattr_attr'		=> 'p1ma_attrib',
					'mattr_value'  		=> 'p1ma_value',
					'mattr_stflag' 		=> 'p1ma_stflag',
					'mattr_added' 	 	=> 'p1ma_added',
					'mattr_updated'		=> 'p1ma_updated',
					'p1cms_product_id' 	=> 'p1cms_product_id'
					);
	
	$glb_api_tables['secret_questions'] = 'tbl_secret_questions';
	$glb_api_tables['secret_questions_column'] = array(
				       	'sq_id'    	  	=> 'p1sq_id',
					'sq_question' 	  	=> 'p1sq_question',
				       	'sq_status'		=> 'p1sq_status',
					'me_id'			=> 'p1me_id',
					'sq_added'  		=> 'p1sq_added'
					);

	$glb_api_tables['states'] = 'tbl_states';
	$glb_api_tables['states_column'] = array(
				       	'st_id'    	  	=> 'p1st_id',
					'co_id'    	  	=> 'p1co_id',
				       	'st_name'    		=> 'p1st_name',
					'st_code'    		=> 'p1st_code',
					'st_stflag'    		=> 'p1st_stflag',
					'st_added'  		=> 'p1st_added'
					);

	$glb_api_tables['transactions'] = 'tbl_transactions';
	$glb_api_tables['transactions_column'] = array(
				       	'p1tr_id'    	  	=> 'p1tr_id',
					'p1me_id' 	  	=> 'p1me_id',
				       	'p1ms_id'		=> 'p1ms_id',
					'p1up_id'  		=> 'p1up_id',
					'p1tr_amount' 		=> 'p1tr_amount',
					'p1tr_currency' 	=> 'p1tr_currency',
					'p1tr_comments'		=> 'p1tr_comments',
					'p1tr_p1status'		=> 'p1tr_p1status',
					'p1tr_confirmation'	=> 'p1tr_confirmation',
					'p1tr_refurl' 		=> 'p1tr_refurl',
					'p1tr_ip_address'  	=> 'p1tr_ip_address',
					'p1tr_ct_name'		=> 'p1tr_ct_name',
					'p1tr_client_trans_id'	=> 'p1tr_client_trans_id',
					'p1tr_resp_code'	=> 'p1tr_resp_code',
					'p1st_id'		=> 'p1st_id',
					'p1co_id'		=> 'p1co_id',
					'p1tr_date'		=> 'p1tr_date',
					'p1tr_stflag'		=> 'p1tr_stflag'
					);

	$glb_api_tables['transaction_results'] = 'tbl_transaction_results';
	$glb_api_tables['transaction_results_column'] = array(
				       	'p1tu_id'    	  	=> 'p1tu_id',
					'p1tr_id' 	  	=> 'p1tr_id',
				       	'p1tu_name'		=> 'p1tu_name',
					'p1tu_value'  		=> 'p1tu_value',
					'p1tu_date' 		=> 'p1tu_date'
					);
	
	$glb_api_tables['timezone'] = 'tbl_timezone';
	$glb_api_tables['timezone_column'] = array(
				       	'tz_id'    	  	=> 'p1tz_id',
					'tz_name'    	  	=> 'p1tz_name',
				       	'tz_code'    		=> 'p1tz_code',
					'tz_type'    		=> 'p1tz_type',
					'tz_offset'    		=> 'p1tz_offset',
					'tz_location' 	 	=> 'p1tz_location',
					'co_id'  		=> 'p1co_id',
					'tz_description'  	=> 'p1tz_description',
					'tz_stflag'  		=> 'p1tz_stflag',
					'tz_added'  		=> 'p1tz_added'
					);

	$glb_api_tables['users_profile'] = 'tbl_users_profile';
	$glb_api_tables['users_profile_column'] = array(
				       	'p1up_id'    	  	=> 'p1up_id',
					'p1up_tel' 	  	=> 'p1up_tel',
				       	'p1up_dob'		=> 'p1up_dob',
					'p1up_firstname'  	=> 'p1up_firstname',
					'p1up_lastname'		=> 'p1up_lastname',
					'p1up_address1'     	=> 'p1up_address1',
					'p1up_address2'	  	=> 'p1up_address2',
				       	'p1up_ct_name'		=> 'p1up_ct_name',
					'p1st_id'  		=> 'p1st_id',
					'p1co_id'		=> 'p1co_id',
					'p1up_zip'		=> 'p1up_zip',
					'p1up_stflag'  		=> 'p1up_stflag',
					'p1up_added'		=> 'p1up_added'
					);
     $glb_api_tables['api_response_msg'] = 'tbl_api_response_msg';
     $glb_api_tables['api_response_msg_column'] = array(
					'arm_id'           	=> 'p1arm_id',
					'au_id'            	=> 'p1au_id',
					'arm_resp_code'    	=> 'p1arm_resp_code',
					'arm_resp_appl_from'  	=> 'p1arm_resp_appl_from',
					'arm_resp_appl_to' 	=> 'p1arm_resp_appl_to',
					'arm_trans_type_a'	=> 'p1arm_trans_type_a',
					'arm_trans_type_b'      => 'p1arm_trans_type_b',
					'arm_trans_type_o'   	=> 'p1arm_trans_type_o',
					'arm_trans_type_ivs'    => 'p1arm_trans_type_ivs',
					'arm_trans_type_mblox'  => 'p1arm_trans_type_mblox',
					'arm_colg_id'  		=> 'p1colg_id',
					'arm_st_flag'    	=> 'p1arm_st_flag',
					'arm_added_date'    	=> 'p1arm_added_date',
					'arm_updated_date'    	=> 'p1arm_updated_date'
                    );

	$glb_api_tables['ocn_list'] = 'tbl_ocn_list';
	$glb_api_tables['ocn_list_column'] = array(
				       	'p1ocn_id'    	  	=> 'p1ocn_id',
					'p1ocn_value' 	  	=> 'p1ocn_value',
				       	'p1ocn_des'		=> 'p1ocn_des',
					'p1ocn_type'		=> 'p1ocn_type',
					'p1ocn_status'  	=> 'p1ocn_status',
					'p1ocn_added_date'	=> 'p1ocn_added_date',
					'p1ocn_modify_date'     => 'p1ocn_modify_date'
					);

	$glb_api_tables['notify_transactions'] = 'tbl_notify_transactions';
	$glb_api_tables['notify_transactions_column'] = array(
				    	'p1notr_id'    	  	=> 'p1notr_id',
					'p1tr_id' 	  	=> 'p1tr_id',
					'p1tr_type_id' 	  	=> 'p1tr_type_id',
				    	'p1notr_notify_url'	=> 'p1notr_notify_url',
					'p1notr_url'  		=> 'p1notr_url',
					'p1notr_p1fun_values'  	=> 'p1notr_p1fun_values',
					'p1notr_client_tran' 	=> 'p1notr_client_tran',
					'p1notr_unique_id' 	=> 'p1notr_unique_id',
					'p1notr_start_time'	=> 'p1notr_start_time',
					'p1notr_end_time'    	=> 'p1notr_end_time',
					'p1notr_hit_count'	=> 'p1notr_hit_count',
					'p1notr_status'    	=> 'p1notr_status'
					);

	$glb_api_tables['csr'] = 'tbl_csr';
	$glb_api_tables['csr_column'] = array(
					'csr_id'		=> 'p1csr_id',
					'ad_id'			=> 'p1ad_id',
					'me_id'			=> 'p1me_id',
					'csr_email'		=> 'p1csr_email',
					'csr_pass'		=> 'p1csr_pass',
					'csr_ac_actvn'		=> 'p1csr_ac_actvn',
					'csr_rst_pwd_token'	=> 'p1csr_rst_pwd_token',
					'csr_lastlogin'		=> 'p1csr_lastlogin',
					'csr_last_access'	=> 'p1csr_last_access',
					'csr_session_status'	=> 'p1csr_session_status',
					'csr_sts_flag'		=> 'p1csr_sts_flag',
					'csr_added'		=> 'p1csr_added',
					'csr_updated'		=> 'p1csr_updated'
					);

	$glb_api_tables['csr_log'] = 'tbl_csr_log';
	$glb_api_tables['csr_log_column'] = array(
					'cl_id'			=> 'p1cl_id',
					'csr_id'		=> 'p1csr_id',
					'cl_session_id'		=> 'p1cl_session_id',
					'cl_user_agent'		=> 'p1cl_user_agent',
					'cl_logged_in'		=> 'p1cl_logged_in',
					'cl_session_starts_at'	=> 'p1cl_session_starts_at',
					'cl_logged_out'		=> 'p1cl_ip_addr',
					'cl_city'		=> 'p1cl_city',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'cl_sts_flag'		=> 'p1cl_sts_flag',
					'cl_session_status'	=> 'p1cl_session_status'
					);

	$glb_api_tables['csr_profile'] = 'tbl_csr_profile';
	$glb_api_tables['csr_profile_column'] = array(
					'cp_id'			=> 'p1cp_id',
					'csr_id'		=> 'p1csr_id',
					'cp_nick_name'		=> 'p1cp_nick_name',
					'cp_designation'	=> 'p1cp_designation',
					'cp_first_name'		=> 'p1cp_first_name',
					'cp_last_name'		=> 'p1cp_last_name',
					'cp_mobile'		=> 'p1cp_mobile',
					'cp_address1'		=> 'p1cp_address1',
					'cp_address2'		=> 'p1cp_address2',
					'cp_city'		=> 'p1cp_city',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'cp_zipcode'		=> 'p1cp_zipcode'
					);

	$glb_api_tables['btnivs_validation'] = 'tbl_btnivs_validation';
	$glb_api_tables['btnivs_validation_column'] = array(
					'biv_id'		=> 'p1biv_id',
					'csr_id'		=> 'p1csr_id',
					'biv_client_tran'	=> 'p1biv_client_tran',
					'biv_ip_address'	=> 'p1biv_ip_address',
					'biv_date'		=> 'p1biv_date',
					'biv_stflag'		=> 'p1biv_stflag'
					);

	$glb_api_tables['btnivs_validation_results'] = 'tbl_btnivs_validation_results';
	$glb_api_tables['btnivs_validation_results_column'] = array(
					'bivr_id'		=> 'p1bivr_id',
					'biv_id'		=> 'p1biv_id',
					'bivr_name'		=> 'p1bivr_name',
					'bivr_value'		=> 'p1bivr_value'
					);

	$glb_api_tables['btnivs_client_information'] = 'tbl_btnivs_client_information';
	$glb_api_tables['btnivs_client_information_column'] = array(
					'bici_id'		=> 'p1bici_id',
					'biv_client_tran'	=> 'p1biv_client_tran',
					'bici_tel'		=> 'p1bici_tel',
					'bici_dob'		=> 'p1bici_dob',
					'bici_firstname'	=> 'p1bici_firstname',
					'bici_lastname'		=> 'p1bici_lastname',
					'bici_address1'		=> 'p1bici_address1',
					'bici_address2'		=> 'p1bici_address2',
					'bici_city'		=> 'p1bici_city',
					'st_id'			=> 'p1st_id',
					'co_id'			=> 'p1co_id',
					'bici_zip'		=> 'p1bici_zip'
					);
	$glb_api_tables['zip_codes'] = 'tbl_zip_codes';
	$glb_api_tables['zip_codes_column'] = array(
					'zip_id'		=> 'p1zip_id',
					'zip_code'		=> 'p1zip_code',
					'zip_statecode'		=> 'p1zip_statecode'
					);

    $glb_api_tables['payone_images'] = 'tbl_payone_images';
    $glb_api_tables['payone_images_column'] = array(
					'pimg_id'              	=> 'p1pimg_id',
					'au_id'                 => 'p1au_id',
					'pimg_name'        	=> 'p1pimg_name',
					'pimg_image'       	=> 'p1pimg_image',
					'pimg_default'      	=> 'p1pimg_default',
					'pimg_flag'           	=> 'p1pimg_flag',
					'pimg_added'       	=> 'p1pimg_added',
					'pimg_updated'    	=> 'p1pimg_updated'
    );


	$glb_api_tables['country_language_reference'] = 'tbl_country_language_reference';
	$glb_api_tables['country_language_reference_column'] = array(
					'colg_id'		=> 'p1colg_id',
					'co_id'			=> 'p1co_id',
					'lg_id'			=> 'p1lg_id',
					'colg_stflag'		=> 'p1colg_stflag',
					'colg_official'		=> 'p1colg_official'
					);