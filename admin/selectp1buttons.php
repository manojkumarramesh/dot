<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : selectp1buttons.php
// Description : file to handle payone buttons ajax related functions 
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 23-09-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once( '../includes/configs/init.php' );
include_once('../includes/configs/sessionadminc.php');

/*-----Instantiate the class-----*/
$payonebuttons_obj = new PayoneButtons();
$services_obj = new services();
$admin_obj = new Admin();
$debug_obj = new Debug();

/*----- Constant Definition for switch case ----- */
define('FETCH_DEFAULT_BUTTON_IMAGE','fetch_default_button_image');
define('FETCH_MERCHANTS_USING_THIS_BUTTON','fetch_merchants_using_this_button');
define('FETCH_ALL_BUTTONS_FOR','fetch_all_buttons_for');
define('SELECT_THIS_BUTTON','select_this_button');
define('CLEAR_BUTTON_IMAGE','clear_image');
define('REQUEST_NOT_FOUND', 404);

/*----- get action request ----- */
$action = trim($_REQUEST['actions']);

switch($action)
{
    case FETCH_DEFAULT_BUTTON_IMAGE:
        $payment_id = trim($_REQUEST['id']);
        $debug = array('file'=>'selectp1buttons.php', 'line'=>'fetch_default_button_image');
        $result = $payonebuttons_obj->FetchDefaultButton($payment_id, $debug);/*fetch_default_button_image*/        
        echo $result;
        break;
    case FETCH_MERCHANTS_USING_THIS_BUTTON:
        $button_id = trim($_REQUEST['id']);
        $button_name = trim($_REQUEST['name']);
        $debug = array('file'=>'selectp1buttons.php', 'line'=>'fetch_merchants_using_payone_button');
        $payonebuttons_obj->GetMerchantsUsingPayoneButton($button_id, $button_name, $debug);/*fetch_merchants_using_payone_button*/
        $smarty->display($glb_adm_tpl_path.'p1merchants.tpl');
        break;
    case FETCH_ALL_BUTTONS_FOR:
        $payment_id = trim($_REQUEST['payment_id']);
        $debug = array('file'=>'selectp1buttons.php', 'line'=>'fetch_all_buttons_for');
        $payonebuttons_obj->FetchAllPayoneButtons($payment_id, $debug);/*fetch_all_buttons_for*/ 
        $smarty->display($glb_adm_tpl_path.'selectp1buttons.tpl');
        break;
    case SELECT_THIS_BUTTON:
        $image_id = trim($_REQUEST['image_id']);
        $debug = array('file'=>'selectp1buttons.php', 'line'=>'select_this_button');
        $result = $payonebuttons_obj->SelectThisPayoneButton($image_id, $debug);/*select_this_button*/
        echo $result;
        break;
    case CLEAR_BUTTON_IMAGE:
        unset($_SESSION['new_default_image']);
        break;
    default:
        echo REQUEST_NOT_FOUND;
        break;  
} // end switch

?>