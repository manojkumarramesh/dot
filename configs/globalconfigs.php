<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : globalconfigs.php
// Description : File to handle tables information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 03-03-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
//Assign values for global parameters
$glb_folder_data = FULL_PATH ."data/images/thumb/";
$glb_folder_css = FULL_PATH."data/styles/default/";
$glb_folder_css_plugin = FULL_PATH."includes/blueprint/plugins/fancy-type/";
$glb_folder_script = FULL_PATH."includes/scripts/";
$glb_folder_ivr_file= FULL_PATH."data/IVR/";
$glb_folder_script_api = FULL_PATH."includes/scripts/api/";
$glb_adm_tpl_path= FULL_PATH."templates/default/admin/";
$glb_cmn_tpl_path= FULL_PATH."templates/default/common/";
$glb_adm_path= FULL_PATH."admin/";
$glb_admin_email="admin@paymentone.com";
$glb_sha_script_phase="dcitest";
$glb_merchant_path=FULL_PATH;

// Call back notification waiting & checking interval(Mobile API)
$glb_notify_max_call= "1"; 
$glb_notify_sleep_time= "2";

//CMS Flow Log 
$glb_cms_debug = "0";

//API URL Values
$glb_api_url = "http://test-partner.paymentone.com:8080/translator-2.4/?";
$glb_p1fun_url= "http://p1fun.dci.in/userpoints.php?";
$glb_p1button_url= "http://p1button.dci.in";
$glb_mobileapi_url= "http://test-partner.paymentone.com:8080/translator-2.4/?";

//CMS Responses declaration starts here
//$glb_p1button_url= "http://p1button.dci.in";
// http://dev-partner.paymentone.com:8080/cms_i18n/
$glb_cms_account_url = "http://dev-partner.paymentone.com:8080/cms_i18n/accounts";
$glb_cms_product_url = "http://dev-partner.paymentone.com:8080/cms_i18n/products";
$glb_cms_button_product_url = "http://dev-partner.paymentone.com:8080/cms_i18n/buttonProducts";
$glb_cms_price_url = "http://dev-partner.paymentone.com:8080/cms_i18n/prices";
$glb_cms_country_url = "http://dev-partner.paymentone.com:8080/cms_i18n/countries";
$glb_cms_admin_email = "admin@paymentone.com";
$glb_cms_admin_pwd = "payone";

//Session Expire Time//
$glb_session_expire_time = 1800;

//For CMS error handling
$glb_cms_xml = "<?xml";
$glb_cms_error = "HTTP Status";
$glb_cms_conflict = "HTTP Status 409";
$glb_cms_auth = "HTTP Status 401";
$glb_cms_not_fnd ="HTTP Status 404";
$glb_cms_bad_req = "HTTP Status 400";

//Assign values for global parameters
$glb_tbl_admin= $glb_api_tables['admin'];
$glb_admin_column = $glb_api_tables['admin_column'];

$glb_tbl_admin_log= $glb_api_tables['admin_log'];
$glb_admin_log_column = $glb_api_tables['admin_log_column'];

$glb_tbl_admin_profile= $glb_api_tables['admin_profile'];
$glb_admin_profile_column = $glb_api_tables['admin_profile_column'];

$glb_tbl_admin_visit= $glb_api_tables['admin_visit'];
$glb_admin_visit_column = $glb_api_tables['admin_visit_column'];

$glb_tbl_categories= $glb_api_tables['categories'];
$glb_categories_column = $glb_api_tables['categories_column'];

$glb_tbl_country  = $glb_api_tables['country'];
$glb_country_column = $glb_api_tables['country_column'];

$glb_tbl_secret_questions  = $glb_api_tables['secret_questions'];
$glb_secret_questions_column = $glb_api_tables['secret_questions_column'];

$glb_tbl_states  = $glb_api_tables['states'];
$glb_states_column = $glb_api_tables['states_column'];

$glb_tbl_merchants = $glb_api_tables['merchants'];
$glb_merchants_column = $glb_api_tables['merchants_column'];

$glb_tbl_merchants_profile = $glb_api_tables['merchants_profile'];
$glb_merchants_profile_column = $glb_api_tables['merchants_profile_column'];

$glb_tbl_ms_profile = $glb_api_tables['merchants_sup_profile'];
$glb_ms_profile_column = $glb_api_tables['merchants_sup_profile_column'];

$glb_tbl_merchants_log = $glb_api_tables['merchants_log'];
$glb_merchants_log_column = $glb_api_tables['merchants_log_column'];

$glb_tbl_merchantservices = $glb_api_tables['merchantservices'];
$glb_merchantservices_column = $glb_api_tables['merchantservices_column'];

$glb_transaction = $glb_api_tables['transactions'];
$glb_transaction_column = $glb_api_tables['transactions_column'];

$glb_transaction_results = $glb_api_tables['transaction_results'];
$glb_transaction_results_column = $glb_api_tables['transaction_results_column'];

$glb_user_profile = $glb_api_tables['users_profile'];
$glb_user_profile_column = $glb_api_tables['users_profile_column'];

$glb_tbl_api_response_msg= $glb_api_tables['api_response_msg'];
$glb_api_response_msg_column = $glb_api_tables['api_response_msg_column'];

$glb_tbl_language= $glb_api_tables['languages'];
$glb_language_column = $glb_api_tables['languages_column'];

$glb_ocn_list = $glb_api_tables['ocn_list'];
$glb_ocn_list_column = $glb_api_tables['ocn_list_column'];

$glb_notify_transactions = $glb_api_tables['notify_transactions'];
$glb_notify_transactions_column = $glb_api_tables['notify_transactions_column'];

$glb_tbl_csr = $glb_api_tables['csr'];
$glb_csr_column = $glb_api_tables['csr_column'];

$glb_tbl_csr_log = $glb_api_tables['csr_log'];
$glb_csr_log_column = $glb_api_tables['csr_log_column'];

$glb_tbl_csr_profile = $glb_api_tables['csr_profile'];
$glb_csr_profile_column = $glb_api_tables['csr_profile_column'];

$glb_tbl_btnivs_validation = $glb_api_tables['btnivs_validation'];
$glb_btnivs_validation_column = $glb_api_tables['btnivs_validation_column'];

$glb_tbl_btnivs_validation_results = $glb_api_tables['btnivs_validation_results'];
$glb_btnivs_validation_results_column = $glb_api_tables['btnivs_validation_results_column'];

$glb_tbl_btnivs_client_information = $glb_api_tables['btnivs_client_information'];
$glb_tbl_btnivs_client_information_column = $glb_api_tables['btnivs_client_information_column'];

$glb_tbl_country_language_reference = $glb_api_tables['country_language_reference'];
$glb_country_language_reference_column = $glb_api_tables['country_language_reference_column'];

$glb_tbl_zip_codes = $glb_api_tables['zip_codes'];
$glb_zip_codes_column = $glb_api_tables['zip_codes_column'];
?>