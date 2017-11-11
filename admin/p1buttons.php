<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : p1buttons.php
// Description : file to handle P1 Button Image.
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 20-09-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once('../includes/configs/sessionadminc.php');
include_once('../includes/functions/simpleimage.php');

/*-----Instantiate the class-----*/
$services_obj = new services();
$admin_obj = new Admin();
$common_obj = new Common();
$image = new SimpleImage();
$validator_obj = new Validator();
$payonebuttons_obj = new PayoneButtons();
$debug_obj = new Debug();

//Directory target path to save the uploaded image
$directory_path = "../data/images/original/";

/*-----Constants Definition-----*/
define("EXISTING_IMAGE_REMAINS", 1);
define("NEW_IMAGE_ADDED", 2);
define("ALL_BUTTONS", 0);
define("ACTIVE_BUTTONS", 1);
define("SUSPENDED_BUTTONS", 2);
define("P1_BUTTON_COUNT", 0);
define("SET_AS_DEFAULT", 1);
define("SET_AS_NOT_DEFAULT", 0);
define("DISPLAY_ALLOWED_SIZE", 100);//allowed size for payone image specify in kb
define("ALLOWED_SIZE", 102400);//allowed size for payone image specify in bytes
define("ALLOWED_WIDTH", 325);//allowed width for payone image
define("ALLOWED_HEIGHT", 195);//allowed height for payone image

$allowed_formats = array('image/pjpeg', 'image/jpeg', 'image/jpg', 'gif', 'image/x-png', 'image/png', 'image/gif');//Allowed formats for images

$debug = array('file'=>'p1buttons.php', 'line'=>'Authenticationlistsquery');
$authentication_list = $services_obj->get_drop_downlist('tbl_authentications','p1au_id,p1au_name','p1au_id in (1, 2, 3)', $debug);/*Authenticationlistsquery*/

$order_by = "p1pimg_flag != 0 ORDER BY p1pimg_added DESC";//order records by added descending

//to edit, activate, suspend, delete payone button image - starts
if(isset($_REQUEST['action']))
{
    if($_REQUEST['action'] == "edit")//to edit payone button image
    {
        $_SESSION['id'] = $id = trim($_REQUEST['id']);
        $debug = array('file'=>'p1buttons.php', 'line'=>'editpayonebuttons');
        $payonebuttons_obj->EditSelectPayoneButtons($id, $debug);/*editpayonebuttons*/
    }    
    else if($_REQUEST['action'] == "delete")//to delete payone button image
    {
        unset($_SESSION['uploadfile']);
        unset($_SESSION['edit_image']);
        unset($_SESSION['id']);
        unset($_SESSION['flag']);
        $id = trim($_REQUEST['id']);
        //query to find if selected image is default image
        $debug = array('file'=>'p1buttons.php', 'line'=>'selectsquery');
        $get_all_lists = $services_obj->get_AllList('tbl_payone_images',"p1pimg_id = '$id'", $debug);/*selectsquery*/
        $button_name = $get_all_lists[0]['p1pimg_name'];
        $button_image = $get_all_lists[0]['p1pimg_image'];
        if($get_all_lists[0]['p1pimg_default'] == 1)//if image is a default image - error message to show default button cannot be deleted
        {
            $error[] = "Default button cannot be deleted";
        }
        else
        {//if button image is not a default button
            //query to find button image is used in services
            $debug = array('file'=>'p1buttons.php', 'line'=>'selectquery');
            $get_all_lists = $services_obj->get_AllList('tbl_merchant_services',"p1pimg_id = '$id' AND p1ms_stflag NOT IN (0,3)", $debug);/*selectquery*/
            $count = count($get_all_lists);
            if($count == P1_BUTTON_COUNT)//if button image is not used in services - delete
            {
                $debug = array('file'=>'p1buttons.php', 'line'=>'deletepayonebuttons');
                $delete = $payonebuttons_obj->DeletePayoneButton($id, $debug);/*deletepayonebuttons*/
                if($delete)//if button is deleted successfully
                {
                    unlink($directory_path.$button_image);
                    unlink("../data/images/thumb/".$button_image);
                    $success[] = "<strong>$button_name</strong> deleted successfully";
                }
                else
                {//if button image is not deleted
                    $error[] = "<strong>$button_name</strong> not deleted";
                }
            }
            else
            {//if button image is used in services
                $error[] = "<strong>$button_name</strong> contains services, button cannot be deleted";
            }
        }        
    }
    else if($_REQUEST['action'] == "cancel")//To clear payone button page
    {
        $payonebuttons_obj->ClearPayoneButtons();
    }    
    else if($_REQUEST['action'] == "lists")//To fetch button images - All (0), Active (1), Suspended (2)
    {
        unset($_SESSION['uploadfile']);
        unset($_SESSION['edit_image']);
        unset($_SESSION['id']);
        unset($_SESSION['flag']);
        $_SESSION["state"] = $state_id = trim($_REQUEST['state']);
        $debug = array('file'=>'p1buttons.php', 'line'=>'orderbypayonebuttons');
        $order_by = $payonebuttons_obj->OrderbyPayoneButtons($state_id, $debug);/*orderbypayonebuttons*/
    }
    else if($_REQUEST['action'] == "activate")//To activate a button image
    {
        unset($_SESSION['uploadfile']);
        unset($_SESSION['edit_image']);
        unset($_SESSION['id']);
        unset($_SESSION['flag']);
        $activate_id = trim($_REQUEST['id']);
        $debug = array('file'=>'p1buttons.php', 'line'=>'activatesuspendedbutton');
        $activate = $payonebuttons_obj->ActivateSuspendedPayoneButtons($activate_id, $debug);/*activatesuspendedbutton*/
        $debug = array('file'=>'p1buttons.php', 'line'=>'getbuttonname');
        $button_name = $payonebuttons_obj->GetPayoneButtonName($activate_id, $debug);/*getbuttonname*/ 
        if($activate)//if button image is activated
        {
            $success[] = "<strong>$button_name</strong> activated";
            unset($_SESSION['state']);
        }
        else
        {//if button image is not activated
            $error[] = "<strong>$button_name</strong> activation failed";
        }       
    }
    else if($_REQUEST['action'] == "suspend")//To suspend a button image
    {
        unset($_SESSION['uploadfile']);
        unset($_SESSION['edit_image']);
        unset($_SESSION['id']);
        $suspend_id = trim($_REQUEST['id']);
        //query to find if default image
        $debug = array('file'=>'p1buttons.php', 'line'=>'selectequery');
        $get_all_lists = $services_obj->get_AllList('tbl_payone_images',"p1pimg_id = '$suspend_id'", $debug);/*selectequery*/
        $debug = array('file'=>'p1buttons.php', 'line'=>'getbuttonname');
        $button_name = $payonebuttons_obj->GetPayoneButtonName($suspend_id, $debug);/*getbuttonname*/ 
        if($get_all_lists[0]['p1pimg_default'] == 1)//if default button image
        {
            $error[] = "Default button cannot be suspended";
        }
        else
        {//if not default button image
            //query to find button image used in service
            $debug = array('file'=>'p1buttons.php', 'line'=>'selectedquery');
            $get_all_lists = $services_obj->get_AllList('tbl_merchant_services',"p1pimg_id = '$suspend_id' AND p1ms_stflag NOT IN (0,3)", $debug);/*selectedquery*/
            $count = count($get_all_lists);
            if($count == P1_BUTTON_COUNT)//if button image not used in service
            {
                $debug = array('file'=>'p1buttons.php', 'line'=>'suspendbuttonimage');
                $suspend = $payonebuttons_obj->SuspendPayoneButtons($suspend_id, $debug);/*suspendbuttonimage*/
                if($suspend)//if button image is suspended
                {
                    $success[] = "<strong>$button_name</strong> suspended";
                    unset($_SESSION['state']);
                }
                else
                {//if button image is not suspended
                    $error[] = "<strong>$button_name</strong> suspend failed";
                }
            }
            else
            {//if button image is used in service
                $error[] = "<strong>$button_name</strong> contains services, button cannot be suspended";
            }
        }
    }
    else if($_REQUEST['action'] == "upload")//To upload button image
    {
        $smarty->assign('txt_p1button_image_name', trim($_REQUEST['txt_p1button_image_name']));
        $files = $_FILES['txt_p1button_image_file'];
        $image_size = getimagesize($_FILES['txt_p1button_image_file']['tmp_name']);
        //validate and upload button image
        $debug = array('file'=>'p1buttons.php', 'line'=>'uploadbuttonimage');        
        $upload_error = $payonebuttons_obj->UploadPayoneButtons($files, $image_size, $allowed_formats, $directory_path, $debug);/*uploadbuttonimage*/
		if($upload_error)//if error in uploading error is generated
		{
			foreach($upload_error as $errors)
			{
				if($errors)//if error is found
				{
					$error[] = $errors;
					unset($_SESSION['uploadfile']);
					unset($_SESSION['edit_image']);
				}
			}
		}
    }    
}
//to edit, activate, suspend, delete payone button image - ends

//ADD/UPDATE - button image starts
if(isset($_REQUEST['save']))
{
    $authentication_id = $_REQUEST['authentication_name'];
    $txt_p1button_image_name = trim($_REQUEST['txt_p1button_image_name']);
    $checked_default = ($_REQUEST['checked_default'] != '') ? SET_AS_DEFAULT : SET_AS_NOT_DEFAULT;
    $uploadfile = trim($_REQUEST['uploadfile']);
    $txt_p1button_image_name = $validator_obj->escape_string($txt_p1button_image_name);
    $smarty->assign('txt_p1button_image_name', trim($_REQUEST['txt_p1button_image_name']));

    $save_error = array();
    
    if($txt_p1button_image_name == '')//To check Payone Button Name is entered
    {
        $save_error[] = "Enter Payone Button Name";
    }
    elseif(!preg_match("/^[A-Za-z0-9\-\'\#\.\_\!\@\$\%\^\(\)\/ ]+$/", $txt_p1button_image_name))//To check valid Payone Button Name
    {           
        $save_error[] = "Enter valid Payone Button Name.";
    }    
    if($authentication_id == '' && $_REQUEST['edit_id'] == '')//To check Authentication Type is selected
    {
        $save_error[] = "Select Payment Type";
    }
    if($_REQUEST['save'] == "Save" && $_REQUEST['uploadfile'] == '')//To check Payone Button Image is uploaded
    {
        $files = $_FILES['txt_p1button_image_file'];
        if($files['name'] == '')//To check Payone Button Image is uploaded
        {
             $save_error[] = "Select Payone Button Image";
        }
    }
    if($_REQUEST['save'] == "Save" && $_REQUEST['edit_id'] != '')//To check Payone Button Image is remains/modified in edit page
    {
        if($uploadfile == '')//To check Payone Button Image
        {
            $flag_updated = EXISTING_IMAGE_REMAINS;//if flag_update is 1 existing image remains in edit
        }
        else
        {
            $flag_updated = NEW_IMAGE_ADDED;//if flag_update is 2 existing image is changed with new image in edit
        }
        if($checked_default == SET_AS_NOT_DEFAULT)//to check set as default option not set
        {
            //to set button as default button
            $debug = array('file'=>'p1buttons.php', 'line'=>'tosetbuttonasdefaultbutton');
            $count = $payonebuttons_obj->SetasdefaultPayoneButtons($authentication_id, $debug);/*tosetbuttonasdefaultbutton*/
            if($count == P1_BUTTON_COUNT)//if default button is unchecked
            {
                $save_error[] = "Please set another button as default before resetting this button";
                $smarty->assign('marked_default', '1');
            }
        }
    }
    foreach($save_error as $errors)
    {
        if($errors)//if error in saving
        {
            $error[] = $errors;
        }
    }
    $count = count($error);

    //to save button image - starts
    if($_REQUEST['save'] == "Save" && $_REQUEST['edit_id'] == '')
    {
        if($count > P1_BUTTON_COUNT)//if errors assign to tpl
        {
            $smarty->assign('error', $error);
        }
        else
        {//if no errors
            $debug = array('file'=>'p1buttons.php', 'line'=>'checkbuttonalreadyexists');
            $save_count = $payonebuttons_obj->CheckPayoneButtonNameAlreadyExists($authentication_id, $txt_p1button_image_name, $debug);/*checkbuttonalreadyexists*/
            if($save_count == P1_BUTTON_COUNT)//if button name already exists
            {
                if($checked_default == SET_AS_DEFAULT)//if set as default option is selected
                {
                    $debug = array('file'=>'p1buttons.php', 'line'=>'addservices');
                    $last_default_id = $payonebuttons_obj->FetchLastDefaultButton($authentication_id, $debug);/*addservices*/

                    //set other buttons as not default
                    $debug = array('file'=>'p1buttons.php', 'line'=>'setbuttonasdefault');
                    $payonebuttons_obj->ResetDefaultPayoneButtons($authentication_id, $debug);/*setbuttonasdefault*/
                    $default = "and button set as default";
                }
                else
                {//if set as default option not set
                    $debug = array('file'=>'p1buttons.php', 'line'=>'setbuttonasdefault');
                    $count = $payonebuttons_obj->SetasdefaultPayoneButtons($authentication_id, $debug);/*setbuttonasdefault*/
                    if($count == P1_BUTTON_COUNT)//if button image set as default
                    {
                        $checked_default = SET_AS_DEFAULT;
                        $default = " and button set as default";
                    }
                }
                if($checked_default == 1)
                {
                    $fieldss = "p1pimg_id";
                    $conditions = "p1pimg_id != '' order by p1pimg_id desc";
                    $debug = array('file'=>'p1buttons.php', 'line'=>'SelectThisPayoneButtons');
                    $buttons_listss = $admin_obj->GetAdminSelect('tbl_payone_images', $fieldss, $conditions, $debug);/*SelectThisPayoneButtons*/
                    $new_id = $buttons_listss[0]['p1pimg_id'];
                    $new_id = $new_id + 1;
                }
                $debug = array('file'=>'p1buttons.php', 'line'=>'savepayonebutton');
                $image_add = $payonebuttons_obj->SavePayoneButton($authentication_id, $txt_p1button_image_name, $uploadfile, $checked_default, $debug);/*savepayonebutton*/
                if($image_add)//if button image is saved
                {
                    $success[] = "<strong>$txt_p1button_image_name</strong> added successfully ".$default;
                    unset($_SESSION['id']);
                    $smarty->assign('txt_p1button_image_name', '');
                    unset($_SESSION['edit_image']);
                    unset($_SESSION['uploadfile']);
                    unset($_REQUEST);
                    $order_by = "p1pimg_flag != 0 ORDER BY p1pimg_added DESC";
                    if($checked_default == 1)
                    {
                        $debug = array('file'=>'p1buttons.php', 'line'=>'updateservicebuttons');
                        $vars = $payonebuttons_obj->update_services($authentication_id, $last_default_id, $new_id, $debug);/*updateservicebuttons*/
                    }
                }
                else
                {//if button image is not saved
                    $error[] = "Payone Button add failed";
                }
            }
            else
            {//if button name already exists
                $error[] = "Button Name <strong>$txt_p1button_image_name</strong> already exists";
            }
        }
    }
    //to save button image in add- ends
    //to save button image in edit - starts
    else
    {
        if($count > P1_BUTTON_COUNT)//if errors assign to tpl
        {
            $smarty->assign('error', $error);
        }
        else
        {//if no errors found
            $debug = array('file'=>'p1buttons.php', 'line'=>'editcheckbuttonalreadyexists');
            $update_count = $payonebuttons_obj->EditCheckPayoneButtonNameAlreadyExists($authentication_id, $txt_p1button_image_name, $debug);/*editcheckbuttonalreadyexists*/
            if($update_count == P1_BUTTON_COUNT)//to change button name
            {
                if($checked_default == SET_AS_DEFAULT)//if set as default checked
                {
                    $debug = array('file'=>'p1buttons.php', 'line'=>'addservices');
                    $last_default_id = $payonebuttons_obj->FetchLastDefaultButton($authentication_id, $debug);/*addservices*/
                    $debug = array('file'=>'p1buttons.php', 'line'=>'updateservicebuttons');
                    $payonebuttons_obj->update_services($authentication_id, $last_default_id, $_SESSION['id'], $debug);/*updateservicebuttons*/
                    $debug = array('file'=>'p1buttons.php', 'line'=>'setotherbuttonsasnotdefault');
                    $payonebuttons_obj->ResetDefaultPayoneButtons($authentication_id, $debug);/*setotherbuttonsasnotdefault*/
                    $default = "and button set as default";
                }
                else
                {//if set as default not checked
                    $debug = array('file'=>'p1buttons.php', 'line'=>'tosetasdefaultbutton');
                    $count = $payonebuttons_obj->SetasdefaultPayoneButtons($authentication_id, $debug);/*tosetasdefaultbutton*/
                    if($count == P1_BUTTON_COUNT)//if button set as default
                    {
                        $checked_default = SET_AS_DEFAULT;
                        $default = "and button set as default";
                    }
                }
                $debug = array('file'=>'p1buttons.php', 'line'=>'updatepayonebutton');
                $image_update = $payonebuttons_obj->UpdatePayoneButton($flag_updated, $uploadfile, $_REQUEST['edit_image'], $authentication_id, $txt_p1button_image_name, $checked_default, $debug);/*updatepayonebutton*/
                if($image_update)//if button images is updated successfully
                {
                    $success = "<strong>$txt_p1button_image_name</strong> updated successfully ".$default;
                    $smarty->assign('txt_p1button_image_name', '');
                    unset($_SESSION['id']);
                    unset($_SESSION['edit_image']);
                    unset($_REQUEST);
                    unset($_SESSION['uploadfile']);
                    header('Location:p1buttons.php?page='.$_SESSION['page'].'&message='.$success);
                }
                else
                {//if update failed
                    $error[] = "Payone Button <strong>$txt_p1button_image_name</strong> update failed";
                }
            }
            else
            {//if button name already exists
                $error[] = "Button Name <strong>$txt_p1button_image_name</strong> already exists";
            }
        }
    }
    //to save button image in edit - starts
}
//ADD/UPDATE - ends

if(isset($_REQUEST['message']))//if messages are set to show
{
    $success[] = $_REQUEST['message'];
}

//list all records with pagination starts
$limit = 5;
$page = "";

if(isset($_REQUEST['page']) && ($_REQUEST['page'] != ""))//if pagination is set
{
    if($_REQUEST['action'] != 'edit' && $_REQUEST['txt_p1button_image_name'] == '')//if pagination is set clear page
    {
        unset($_SESSION['uploadfile']);
        unset($_SESSION['id']);
        unset($_SESSION['edit_image']);
    }
    $_SESSION['page'] = $page = $_REQUEST['page'];
    $start = ($page - 1) * $limit;
}
else
{
    $start = 0;
}
$_SESSION['page'] = $page;

if(isset($_SESSION['state']) && $_SESSION['state'] != '')//if clicked all, active, suspend
{
    $page_show = "action=lists&state=".$_SESSION['state'];
}

if(isset($_REQUEST['type_filter']))//to search authentication method lists
{
    $filter_authentication_id = $_REQUEST['authentication_id'];
    if($filter_authentication_id != 0)//to search by authentication type
    {
        $order_by = "p1pimg_flag != 0 AND p1au_id = $filter_authentication_id ORDER BY p1pimg_added DESC";
        $_SESSION['page'] .= '&'.$page_show = "type_filter&authentication_id=$filter_authentication_id";
        $filter_condition1 = $order_by;
        $filter_condition2 = "p1pimg_flag = 1 and ".$order_by;
        $filter_condition3 = "p1pimg_flag = 2 and ".$order_by;
    }
    else
    {
        $filter_condition1 = "p1pimg_added";
        $filter_condition2 = "p1pimg_flag = 1";
        $filter_condition3 = "p1pimg_flag = 2";
    }
}
else
{
    $filter_condition1 = "p1pimg_flag != 0 AND p1pimg_added";
    $filter_condition2 = "p1pimg_flag = 1";
    $filter_condition3 = "p1pimg_flag = 2";
}
$varname = "page";
$targetpage = "p1buttons.php?$page_show";
$debug = array('file'=>'p1buttons.php', 'line'=>'selectallquery');
$image_lists = $services_obj->get_AllList('tbl_payone_images', $order_by, $debug);/*selectallquery*/
$total_records = count($image_lists);
$limit_cond = " LIMIT $start, $limit";
$debug = array('file'=>'p1buttons.php', 'line'=>'selectallwithpagination');
$image_lists = $services_obj->get_AllList('tbl_payone_images', $order_by.$limit_cond, $debug);/*selectallwithpagination*/
for($i=0;$i<$limit;$i++)
{
    if($image_lists[$i]['p1pimg_id']!="")//fetch merchants using payone button image
	{
        $debug = array('file'=>'p1buttons.php', 'line'=>'pagination');
    	$buttons_listss = $services_obj->get_drop_downlist("tbl_merchant_services as service,tbl_merchants_profile as profile","service.p1me_id","service.p1pimg_id = ".$image_lists[$i]['p1pimg_id']." AND service.p1me_id = profile.p1me_id AND service.p1ms_stflag NOT IN (0,3) GROUP BY service.p1me_id", $debug);/*pagination*/
    	$merchant_count[] = count($buttons_listss);
	}
}
$debug = array('file'=>'p1buttons.php', 'line'=>'paginations');
$pagination = $common_obj->Pagination($total_records, $limit, $targetpage, $page, $start, $varname, $debug);/*paginations*/
//list all records with pagination ends

$debug = array('file'=>'p1buttons.php', 'line'=>'fetchAllpayonebuttonscount');
$all_count = $payonebuttons_obj->FetchPayoneButtons(ALL_BUTTONS, $filter_condition1, $debug);/*fetchAllpayonebuttonscount*/

$debug = array('file'=>'p1buttons.php', 'line'=>'fetchActivepayonebuttonscount');
$active_count = $payonebuttons_obj->FetchPayoneButtons(ACTIVE_BUTTONS, $filter_condition2, $debug);/*fetchActivepayonebuttonscount*/

$debug = array('file'=>'p1buttons.php', 'line'=>'fetchSuspendedpayonebuttonscount');
$suspended_count = $payonebuttons_obj->FetchPayoneButtons(SUSPENDED_BUTTONS, $filter_condition3, $debug);/*fetchSuspendedpayonebuttonscount*/

if($_REQUEST['action'] == "edit" || $_REQUEST['edit_id'] != '')//assign page name for edit button image
{
    $smarty->assign('pagename', 'Edit Payone Button');
}
else
{//assign page name for new button image
    $smarty->assign('pagename', 'New Payone Button');
}

//assign values to tpl
$smarty->assign('pagetitle', 'PaymentOne : Payone Buttons');
$smarty->assign('authentication_list', $authentication_list);
$smarty->assign('total_records', $total_records);
$smarty->assign('merchant_count', $merchant_count);
$smarty->assign('image_lists', $image_lists);
$smarty->assign('error', $error);
$smarty->assign('success', $success);
$smarty->assign('currentpage', 'p1button');
$smarty->assign('pagination',$pagination);
$smarty->assign('all_count', $all_count);
$smarty->assign('active_count', $active_count);
$smarty->assign('suspended_count', $suspended_count);

$page_contents = $glb_adm_tpl_path.'p1buttons.tpl';

$smarty->assign('header', $glb_adm_tpl_path.'header.tpl' );
$smarty->assign('sidebar', $glb_adm_tpl_path.'sidebar.tpl' );
$smarty->assign('content', $page_contents );
$smarty->assign('footer', $glb_adm_tpl_path.'footer.tpl' );
$smarty->display($glb_adm_tpl_path.'index.tpl');

?>