<?php
//---------------------------------------------------------------------------
// File name   : services.php
// Description : file to handle service module
// © 2010-2011 PaymentOne Corportation. All rights reserved.
// Created date : 02-03-2010
// Modified date: 06-04-2011
// --------------------------------------------------------------------------
/*----- Include files -----*/
include_once( '../includes/configs/init.php' ); 
define("CUR_NAVIGATE_PAGE","services");// constant used in sessionadmininc.php 
include('../includes/configs/sessionadminc.php');
require_once("../includes/functions/ajaxfileuploader.inc.php");

/*----- Instantiate the class start-----*/
$services_obj = new services();
$validation_obj=new Validator();
$common_obj= new Common();
/*----- Instantiate the class end -----*/

/*
Constant declaration
*/
define("ITEM_STATUS_ACTIVE",1);
define("STATUS_ACTIVE",1);
define("STATUS_INACTIVE",0);
define("ITEM_STATUS_INACTIVE",0);
define("MOBILE_AUTHENTICATION",4);
define("NO_ERROR",0);
define("CATEGORY_STATUS_INACTIVE",0);
define("CATEGORY_STATUS_ACTIVE",1);
define("SERVICE_STATUS_ACTIVE",1);
define("SERVICE_STATUS_SUSPEND",2);
define("SERVICE_STATUS_DELETE",3);
define("DUMMY_SERVICE","yes");
define("CAT_ADD_ADMIN",0);

/* 
	Class - Add,Edit,view,activate,suspend,delete service 
*/
class serviceModule
{
    var $debug_obj;

    /*
         Constructor call the modular functions
    */
    public function __construct()
    {
        if(!isset($_SESSION['merchant_id']))
	{
	    header("location:index.php");
	    exit;
	}
	global $smarty,$debug_obj;
	$this->debug_obj = $debug_obj;
	$smarty->assign('currentpage', 'home');
	$smarty->assign('pagetitle', 'PaymentOne');
	$function_call=array("listing"=>"listServices","view"=>"viewService","edit"=>"editService","add"=>"addService","suspend"=>"suspendService","activate"=>"activateService","delete"=>"deleteService","products"=>"listProducts","upservice"=>"updateService","viewproduct"=>"viewProduct");
	$action=trim($_REQUEST['action']);
	if (array_key_exists($action, $function_call)) {
	    $this->$function_call[$action]();
	}
	else{
	    header("Location:services.php?action=listing");
	    exit;
	}
     }

     /* 
	 List all services - 
     */
     function listServices()
     {
	unset($_SESSION['new_default_image']);
	global $smarty,$glb_adm_tpl_path;
	$debug = array('file'=>'services.php', 'line'=>'services_listing_paging');
	$this->servicePaging($debug);/*services_listing_paging*/
	$smarty->assign('page_name', 'admin_services');
	$page_contents = $glb_adm_tpl_path.'services.tpl';
	$smarty->assign('content', $page_contents);
     }

     /* 
	 List all products - 
     */	
     function listProducts()
     {	
	global $smarty,$glb_adm_tpl_path,$services_obj,$glb_cms_product_url,$common_obj,$validation_obj,$glb_cms_country_url;
	$merchant_id=$_SESSION['merchant_id'];
	$cms_merchant_id=$_SESSION['cms_account_id'];
	$sid=trim($_GET['sid']);
	$debug = array('file'=>'services.php', 'line'=>'servicedetails');
	$service_values= $services_obj->get_countlists("tbl_merchant_services a,tbl_authentications b","a.*,b.p1au_code as pay_code","a.p1ms_id=$sid AND a.p1au_id=b.p1au_id AND a.p1me_id=$merchant_id",$debug);/*servicedetails*/

	// redirection to avoid hack by URL
	if($service_values[0]['p1ms_id']=="")
	{
		header("Location:erroraccess.php?page_err=yes");
		exit;
	}
	
	if($service_values[0]['p1ms_stflag']==3)
	{
		header("Location:services.php?action=listing");
	     	exit;
	}

	if(isset($_REQUEST['co_code']) && $_REQUEST['co_code']!="")
	{	
		$price_country_code=$_REQUEST['co_code'];
	} 
	else 
	{
		$price_country_code="US";
	}
	
	$debug = array('file'=>'services.php', 'line'=>'products_display');	
	// Retrieve all the items from the CMS
	$cms_items=$this->viewProductList($sid,"Products display : CMS Response:",$debug);/*products_display*/	
	$smarty->assign('display_on',$service_values[0]['p1ms_iflag']);
	if($_POST['service_edit']=="edit")
	{
	    if(count($cms_items)==0)
	    {		
		$smarty->assign('item_no',"Please add products.");
		$smarty->assign('display_on',$_POST['item_display']);
	    }
	    else
	    {
		$sid=trim($_POST['service_id']);
		$item_status=trim($_POST['item_display']);
		$status=($item_status!="")?$item_status :0 ;
		$debug = array('file'=>'services.php', 'line'=>'update_service');
		
		if($service_values[0]['p1ms_stflag']==0)
		{
			$pflag=1;
		}
		else
		{
			$pflag=$service_values[0]['p1ms_stflag'];
		}
		$updatefields="p1ms_iflag=".$status.",p1ms_updated=now(),p1ms_stflag=$pflag";
		$update_service = $services_obj->get_itm_update("tbl_merchant_services",$updatefields,"p1ms_id=".$sid,$debug);/*update_service*/
		if($update_service)
		{
		     unset($_SESSION['dummyserid']);
		     $_SESSION['success']="service_updated";
		     if(trim($_POST['page_target'])=="edit")
		         header("Location:services.php?action=products&sid=".$sid."&do=edit");
		     else
		         header("Location:merchant.php?page=templates&sid=".$sid."&assign=template");
		     exit;
		}
	    }
	}
	
	//get CMS merchant based country list
	$debug = array('file'=>'services.php', 'line'=>'listProductsCountry');
	$countrylist = $services_obj->merchantCountry($glb_cms_country_url,"?merchant_id=$cms_merchant_id",$debug);/*listProductsCountry*/
		 
	//check price array exit or not
	$rowstemp=array();
	if(count($cms_items)>0)
	{
		foreach($cms_items as $row) 
		{	
			foreach ($row as $key => $value) 
			{
				$loop_pid=$row["product_id"];
				if($key=="price") 
				{
					foreach($value as $pricekey)
					{
						foreach($pricekey as $pricevalue)
						{
							if($pricevalue["type"]=="minimum")
							{
								$mini_array[$loop_pid][$pricevalue["operator_code"]][$pricevalue["priority"]]=$pricevalue["price_amount"];
							}
						}
					}
					if (array_key_exists($key, $rowstemp)) 
					{
						$rowstemp[$key][] = $value;
					}
					else 
					{
						$valuestemp = array($value);
						$rowstemp[$key] = $valuestemp;	
					}
				}
			}
		}
	}
	if (array_key_exists("price", $rowstemp)) 
	{
		$array_exit="1";
	} 
	else 
	{
		$array_exit="0";
	}
	//end check price array exit or not

	if(isset($_SESSION['success']))
	{
	     $smarty->assign('succmsg',$_SESSION['success']);
	}
		
	unset($_SESSION['success']);
	$smarty->assign('price_array_exit', $array_exit);
	$smarty->assign('country_code', $price_country_code);
	$smarty->assign('service_id', $sid);
	$smarty->assign('countrylist', $countrylist);	
	$smarty->assign('cms_items', $cms_items);
	$smarty->assign('service', $service_values);
	$smarty->assign('page_name', 'Add Products');
	$smarty->assign('min_price', $mini_array);
	$smarty->assign('content', $glb_adm_tpl_path.'products.tpl');
    }

	/* 
          View products - view products from CMS
       */
     function viewProduct()
     {
	global $smarty,$glb_adm_tpl_path,$services_obj,$glb_cms_product_url,$common_obj,$validation_obj,$glb_cms_country_url,$glb_adm_path;
	$merchant_id=$_SESSION['cms_account_id'];
	$sid=trim($_GET['serid']);
	$me_id=$_SESSION['merchant_id'];
	if(isset($_REQUEST['co_code']) && $_REQUEST['co_code']!="")
	{	
		$price_country_code=$_REQUEST['co_code'];
	} else {
		$price_country_code="US";
	}

	$debug = array('file'=>'services.php', 'line'=>'getservicedetails');
	$serviceDetails= $services_obj->get_AllList('tbl_merchant_services',"p1ms_id=$sid AND p1me_id=$me_id",$debug);/*getservicedetails*/
	$serviceName=($serviceDetails[0]['p1ms_name']);		
	
	// Retrieve all the products from the CMS
	$debug = array('file'=>'services.php', 'line'=>'products_display');	 
	$cms_items=$this->viewProductList($sid,"Products display : CMS Response:",$debug);/*products_display*/	
	$total_price = count($cms_items);

	//check price array exit or not
	$rowstemp=array();
	foreach ($cms_items as $row) 
	{	
		foreach ($row as $key => $value) 
		{	
			$loop_pid=$row["product_id"];
			if($key=="price") 
			{	
				foreach($value as $pricekey)
				{
					foreach($pricekey as $pricevalue)
					{
						if($pricevalue["type"]=="minimum")
						{
							$mini_array[$loop_pid][$pricevalue["operator_code"]][$pricevalue["priority"]]=$pricevalue["price_amount"];
						}
					}
				}	
		
				if (array_key_exists($key, $rowstemp)) 
				{
					$rowstemp[$key][] = $value;
				}
				else 
				{
					$valuestemp = array($value);
					$rowstemp[$key] = $valuestemp;	
				}
			}
		}
	}
		
	if (array_key_exists("price", $rowstemp)) 
	{
		$array_exit="1";
	} else {
		$array_exit="0";
	}
	//end check price array exit or not
	
	//get CMS merchant based country list
	$debug = array('file'=>'services.php', 'line'=>'merchantCountry');	
	$countrylist = $services_obj->merchantCountry($glb_cms_country_url,"?merchant_id=$merchant_id&service_id=$sid",$debug);/*merchantCountry*/			
	$smarty->assign('action', $_REQUEST['action']);
	$smarty->assign('price_array_exit', $array_exit);
	$smarty->assign('country_code', $price_country_code);
	$smarty->assign('display_on',$serviceDetails[0]['p1ms_iflag']);
	$smarty->assign('service_id', $sid);
	$smarty->assign('cms_items', $cms_items);
	$smarty->assign('countrylist', $countrylist);
	$smarty->assign('serviceName', $serviceName);
	$smarty->assign('page_name', 'Add Products');
	$smarty->assign('min_price', $mini_array);
	$smarty->assign('content', $glb_adm_tpl_path.'viewproducts.tpl');
    }

     /* 
          View service - view products from CMS
     */
     function viewService()
     {
         global $glb_p1button_url,$services_obj,$glb_adm_tpl_path,$smarty;
	 $merchant_id=$_SESSION['merchant_id'];
	 $serId = trim($_GET['serid']);
	 $debug = array('file'=>'services.php', 'line'=>'getservicedetails');
	 $listall= $services_obj->get_AllList('tbl_merchant_services',"p1ms_id=$serId AND p1me_id=$merchant_id",$debug);/*getservicedetails*/

	 // redirection to avoid hack by URL
	 if($listall[0]['p1ms_id']=="")
	 {
	      header("Location:erroraccess.php?page_err=yes");
	      exit;
	 }	

	  if($listall[0]['p1ms_stflag']==3)
	{
		header("Location:services.php?action=listing");
	     	exit;		
	}       	
	 // For payone button starts
	 $serId=trim($listall[0]['p1ms_id']);
	 $debug = array('file'=>'services.php', 'line'=>'getp1buttons');
	 $view_button = $services_obj->get_AllList('tbl_payone_images',"p1pimg_id =".$listall[0]['p1pimg_id'],$debug);/*getp1buttons*/
	 $image = explode('.',$view_button[0]['p1pimg_image']);
	 $decode = $view_button[0]['p1pimg_image'].'P@y'.$serId;
	 // payone button ends
	
	 $debug = array('file'=>'services.php', 'line'=>'service_getnames');
	 $result_names=$services_obj->get_countlists("tbl_templates temp,tbl_categories cat","temp.p1te_name as tmpname,cat.p1ca_name as catname","temp.p1te_id=".$listall[0]['p1te_id']." AND p1ca_id=".$listall[0]['p1ca_id'],$debug);/*service_getnames*/

	 $debug = array('file'=>'services.php', 'line'=>'service_temnames');
	 $smart_tem=$services_obj->get_countlists("tbl_templates","p1te_id as id,p1te_name as name","p1te_id=".$listall[0]['p1te_spid'],$debug);/*service_temnames*/

	 $paymentoneusr=base64_encode($decode);	
	 $sku_status=($listall[0]['p1ms_iflag'] == ITEM_STATUS_ACTIVE) ? "" : "&sku=100";
	
	 $sql = $services_obj->get_countlists('tbl_authentications','p1au_id as pay_id,p1au_name as pay_name,p1au_parent as parent,p1au_code as pay_code','1');

	 for($i=0;$i<count($sql);$i++)
	 {
	     $payment_types[$sql[$i]['pay_id']]=array("id"=>$sql[$i]['pay_id'],"name"=>$sql[$i]['pay_name'],"parent_id"=>$sql[$i]['parent'],"code"=>$sql[$i]['pay_code']);
	 }

	 $auth_id=$listall[0]['p1au_id'];
	 $pay_type=$listall[0]['p1_payment_id'];
	 if($auth_id==1)
	 {
	     $pay_types=explode(",",$pay_type);
	     $pay_id=$auth_id;
	     $home_id=$pay_types[0];
	 }
	 else
	{
	     $pay_id=$auth_id;
	     $home_id=($auth_id==3) ? "" :$pay_type;
	 }

	 $debug = array('file'=>'services.php', 'line'=>'products_display');
	 // Retrieve all the items from the CMS 
	 $cms_items=$this->skuProductList($serId,"Products display : CMS Response:",$debug);/*products_display*/	
	 $script_url="paybutton.js";

	 // P1 button script
	 $varscript='<!DOCTYPE>
	<!-- Begin PaymentOne Button Code -->
	<div class="pay_button">
	<script type="text/javascript" src="'.$glb_p1button_url.'/api/'.trim($script_url).'?button_id='.$paymentoneusr.'&ai_client_tran=1001'.trim($sku_status).'"></script>
	<noscript>Your browser doesn&#39t support or has disabled JavaScript</noscript>
	</div>
	<!-- End PaymentOne Button Code -->';
	
	$limg = $listall[0]['p1ms_logo']; 
	list($width, $height, $type, $attr) = getimagesize("../data/images/thumb/".$limg);

	$service_name = $listall[0]['p1ms_name'];
	
	// P1 non button URL
	$script_button_url="p1redirect.php";
	$non_btn_URL=''.$glb_p1button_url.'/api/'.trim($script_button_url).'?button_id='.$paymentoneusr.'&ai_client_tran=1001'.trim($sku_status).'&headerfooter=on';

	// P1 button script open with iframe	
	$sku_status=($listall[0]['p1ms_iflag'] == ITEM_STATUS_ACTIVE) ? "" : "var sku=100;";
	if($sku_status!="")
	{
		$sku_script="&sku=100";
	}	

	$varscript_iframe='<!DOCTYPE>
	<!-- Begin PaymentOne Button Code -->
	<div class="pay_button">
	<script type="text/javascript">
		window.onload = IncludeJsFiles'.$serId.'();
		function OpenP1Iframe'.$serId.'() { open_colorbox_iframe("'.$serId.'"); }
		function IncludeJsFiles'.$serId.'() {
			var script  = document.createElement("script");
			script.src  = "'.$glb_p1button_url.'/api/nonbutton.js?button_id='.$paymentoneusr.'&ai_client_tran=1001'.$sku_script.'";
			script.type = "text/javascript";
			document.getElementsByTagName("head")[0].appendChild(script); }
	</script>
	<noscript>Your browser doesn&#39t support or has disabled JavaScript</noscript>
	</div>
	<!-- End PaymentOne Button Code -->';	

	//Assign the values to smarty
	$smarty->assign('service_id', $serId);
	$smarty->assign('home_type', $home_id);
	$smarty->assign('pay_type', $pay_id);
	$smarty->assign('payment_types', $payment_types);
	$smarty->assign('cms_items', $cms_items);
	$smarty->assign('smart_temp',$smart_tem[0]);
	$smarty->assign('view_button_name', $view_button[0]['p1pimg_name']);
	$smarty->assign('view_button_image', $view_button[0]['p1pimg_image']);
	$smarty->assign('payment_types', $payment_types);	
	$smarty->assign('atext',trim($authtext[0]));
	$smarty->assign('imgwidth', $width);
	$smarty->assign('paymentoneusr', $paymentoneusr);	
	$smarty->assign('viewlist', $listall);
	$smarty->assign('serviceName', $service_name);
	$smarty->assign('result_names', $result_names);
	$smarty->assign('server_path', $glb_p1button_url);
	$smarty->assign('varscript', $varscript);
	$smarty->assign('non_btn_URL', $non_btn_URL);
	$smarty->assign('varscript_iframe', $varscript_iframe);
	$smarty->assign('content', $glb_adm_tpl_path.'viewservice.tpl');
    }

    /* 
	Add service 
    */

    function addService()
    {	
        global $services_obj,$smarty,$validation_obj,$glb_adm_tpl_path,$common_obj,$glb_cms_product_url,$pay_types;
	$merchant_id=$_SESSION['merchant_id'];
	
	$page_action=trim($_POST['page_action']);
	$debug = array('file'=>'services.php', 'line'=>'get_categories');
	$categories = $services_obj->get_drop_downlist('tbl_categories','p1ca_id as cat_id,p1ca_name as cat_name','(p1me_id IS NULL or  p1me_id="'.$merchant_id.'") and p1ca_stflag=1 order by p1ca_name',$debug);/*get_categories*/
		
	$sql = $services_obj->get_countlists('tbl_authentications','p1au_id as pay_id,p1au_name as pay_name,p1au_parent as parent,p1au_code as pay_code','1');

	for($i=0;$i<count($sql);$i++)
	{
	    $payment_types[$sql[$i]['pay_id']]=array("id"=>$sql[$i]['pay_id'],"name"=>$sql[$i]['pay_name'],"parent_id"=>$sql[$i]['parent'],"code"=>$sql[$i]['pay_code']);
	}
	if($page_action == 'add')
	{
	    $service_name=trim($_POST['serviceName']);
	    $category= trim($_REQUEST['category']);
	    $helptext= trim($_REQUEST['helpText']);
	    $image_id=trim($_REQUEST['image_id']);
	    if(count($_POST['payment_type'])==3)
	    {
		$authtype=1;
		$auto_num=(isset($_POST['auto_num'])) ?1 :0 ;
		$paytype=trim($_POST['home']).",3";
		if(trim($_POST['home'])==5 || trim($_POST['home'])==6)
		{
		    $ivr=1;
		}
	    }
	    else
	    {
		$auto_num=0;
		$authtype=$_POST['payment_type'][0];
		$paytype=($_POST['payment_type'][0]==3) ? 3 : $_POST['home'];
		if(trim($_POST['home'])==5 || trim($_POST['home'])==6)
		{
		    $ivr=1;
		}
	    }

	    // Validation start for add service
	    $errresult= array();
	    $service_name_val = $validation_obj->ChkInput($service_name,'service name');
	    if($service_name_val!="")
	    {
		$errresult[]=$service_name_val;
	    }
	    $srce_name = $validation_obj->escape_string($service_name);
	    $debug = array('file'=>'services.php', 'line'=>'check_service_exists'); //addslashes($srce_name)
	    $serv_exist =$services_obj->service_chk(htmlentities($srce_name, ENT_QUOTES),$merchant_id,$page_action,$serId,$debug);/*check_service_exists*/
	    if(count($serv_exist))
	    {
		$errresult[]="<strong>".$srce_name."</strong> service already exists.";
	    }
	    if($_SESSION['image_nameTMPadm']=="")
	    {
		$errresult[]="Upload service logo.";
	    }
	    else{
		$mylogo=$_SESSION['image_nameTMPadm'];
	    }
	    if(!isset($_POST['payment_type']))
	    {
		$errresult[]="Please choose the payment type.";
	    }
	    if($ivr==1)
	    {
		$debug = array('file'=>'services.php', 'line'=>'fetch_ivr');
		$ivr_where="p1me_id=".$merchant_id;
		$ivr_val = $services_obj->get_countlists("tbl_merchants","p1me_tollfree_num as toll",$ivr_where,$debug);/*fetch_ivr*/
		if($ivr_val[0]['toll']=="" || $ivr_val[0]['toll']==null)
		{
			$errresult[]="Please update IVR Toll Free number in paymentone profile.";
		}
	    }
	    $admin_cat_status=CATEGORY_STATUS_INACTIVE;
	    if($category =="others")
	    {					
		$cat=trim($_REQUEST['txt_otr_cat']);
		if($cat == "")
		{
		     $errresult[]="Enter category name."; 
		}
		else
		{
		    $cat_name_err = $validation_obj->ValidAlphaNumSpl(trim($cat),'Category Name');	
		    if($cat_name_err=="")
		    {
			$cat_ins = $validation_obj->escape_string($cat);
			$debug = array('file'=>'services.php', 'line'=>'check_category_exists');
			$cat_exist =$services_obj->get_drop_downlists('tbl_categories','p1ca_id,p1me_id',"p1ca_name='".addslashes($cat_ins)."' AND (p1me_id IS NULL OR p1me_id=".$merchant_id.") AND  p1ca_stflag=1");/*check_category_exists*/
			// Validate for category name already exists.
			if(count($cat_exist))
			{
				if($cat_exist[0]['p1me_id']==CAT_ADD_ADMIN)
				{
					$errresult[]="<strong>$cat</strong> category already added by admin.";
				}
				else
				{
					$errresult[]="<strong>$cat</strong> category already exists.";
				}
			$admin_cat_status=CATEGORY_STATUS_ACTIVE;
			}
		    }
		    else
		    {
		        $errresult[]=$cat_name_err;
		    }
		}		
	    }
	    else
	    {
		if($category =="")
		{
		     $errresult[]="Select service category.";
		}
	    }
	// validation end
	$total_errors=count($errresult);
	if($total_errors==NO_ERROR)
	{
	    if($category =="others")
	    {
		$cat = trim($_REQUEST['txt_otr_cat']);	
		$cat_ins = $validation_obj->escape_string($cat);
		$insertField = "p1me_id,p1ca_name,p1ca_stflag,p1ca_added,p1ca_updated";
		$insertValues = "$merchant_id,'".addslashes($cat_ins)."',".CATEGORY_STATUS_ACTIVE.",now(),now()";
		if($admin_cat_status !=CATEGORY_STATUS_ACTIVE)
		{
		    $debug = array('file'=>'services.php', 'line'=>'add_category');
		    $authList = $services_obj->insertVal("tbl_categories",$insertField,$insertValues,$debug);/*add_category*/
		    $debug = array('file'=>'services.php', 'line'=>'get_category_id');
		    $authList = $services_obj->get_drop_downlist('tbl_categories','max(p1ca_id) as lastid','1',$debug);/*get_category_id*/
		    $addcat= $authList[0]['lastid'];	
		}
	    }
	    else
	    {
		$addcat= $category;
	    }	
	    $help_text =preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "",htmlspecialchars_decode($helptext));
	    $help_text =htmlspecialchars($help_text);
	    $help_text = $validation_obj->escape_string($help_text);
	    // to fetch smartphone default template id
	    $debug = array('file'=>'services.php', 'line'=>'fetch_smartid');
	    $template_where="p1ad_id = 1 and p1me_id IS NULL and p1te_stflag = 1 and p1te_device_flag =2 order by p1te_id asc LIMIT 0,1";
	    $smartphone_tid = $services_obj->get_countlists("tbl_templates","p1te_id",$template_where,$debug);/*fetch_smartid*/
	    $insertField = "p1me_id,p1ms_name,p1ms_logo,p1ms_comments,p1au_id,p1_payment_id,p1ca_id,p1te_id,p1te_spid,p1ad_id,p1pimg_id,p1ms_iflag,p1ms_number_auto,p1ms_stflag,p1ms_added,p1ms_updated";//htmlentities($srce_name, ENT_QUOTES)
	    $insertValues = "$merchant_id,'".addslashes($srce_name)."','".addslashes($mylogo)."','".addslashes($help_text)."',".$authtype.",'".$paytype."',".$addcat.",1,".$smartphone_tid[0]['p1te_id'].",1,'".$image_id."',1,".$auto_num.",0,now(),now()";
	    $debug = array('file'=>'services.php', 'line'=>'Insert_service');
	    $add_service = $services_obj->insertVal("tbl_merchant_services",$insertField,$insertValues,$debug);/*Insert_service*/

	     if($add_service)
	     {
	        $last_id = $services_obj->get_drop_downlist('tbl_merchant_services','max(`p1ms_id`) as lastid',"1",$debug);
	        $last_inserted=$last_id[0]['lastid'];
	        $_SESSION['dummyserid']=$last_inserted;
	        unset($_SESSION['new_default_image']);
	        unset($_SESSION['image_nameTMPadm']);
	        $_SESSION['success']="created";
	        header("Location:services.php?action=products&sid=".$last_inserted);
	        exit;
             }
	     else
             {
		header("Location:erroraccess.php?page_err=yes");
		exit;
	     }
	}
	else
	{
		$authtype=(count($_POST['payment_type'])==3)? 1: $_POST['payment_type'][0];
		// Assign the post values
		$smarty->assign('serviceName', $service_name);
		$smarty->assign('auto_num', $auto_num);
		$smarty->assign('cat', $category);
		$smarty->assign('errpay', $authtype);
		$smarty->assign('home_type', $_POST['home']);
		$smarty->assign('otherscat', $cat);
		$smarty->assign('helpTxt', htmlspecialchars_decode($helptext));
	}
	}
	else{
	     unset($_SESSION['image_nameTMPadm']);
	}
	
	// To show file upload operation start
	$ajaxFileUploader = new AjaxFileuploader($uploadDirectory="data/images/original/");
	$uploadfiles = $ajaxFileUploader->displayFileUploader('id1',$mylogo,52,"add");	
	$smarty->assign('uploadfiles', $uploadfiles);
	$smarty->assign('categories', $categories);
	$smarty->assign('payment_types', $payment_types);
	$smarty->assign('errmsg', $errresult);	
	$smarty->assign('succmsg', $succmsg);
	$smarty->assign('sideheading', 'Add New Service');		
	$smarty->assign('pagetitle', 'PaymentOne: Add service');
	$smarty->assign('content', $glb_adm_tpl_path.'addservice.tpl');
     }

    /* 
	Edit- service
    */
    function editService()
    {	
	unset($_SESSION['new_default_image']);
	global $services_obj,$glb_adm_tpl_path,$smarty,$glb_cms_product_url,$common_obj,$validation_obj;
	$merchant_id=$_SESSION['merchant_id'];
	$page_action = trim($_REQUEST['page_action']);
	$page_action_val = trim($_REQUEST['actionVal']);
	$serId = trim($_REQUEST['serid']);
	$debug = array('file'=>'services.php', 'line'=>'get_servicedetails');
	$listall= $services_obj->get_AllList('tbl_merchant_services',"p1ms_id='$serId' AND p1me_id=$merchant_id",$debug);/*get_servicedetails*/
	
	$debug = array('file'=>'services.php', 'line'=>'get_categories');
	$categories = $services_obj->get_drop_downlist('tbl_categories','p1ca_id as cat_id,p1ca_name as cat_name','(p1me_id IS NULL or p1me_id="'.$merchant_id.'") and p1ca_stflag=1 order by p1ca_name',$debug);/*get_categories*/
	
       
	/*
	We have to redirect the page to prevent the hack by URL
	*/
	if($listall[0]['p1ms_id']=="")
	{
	    header("Location:erroraccess.php?page_err=yes");
	    exit;
	}

	if($listall[0]['p1ms_stflag']==3)
	{
		header("Location:services.php?action=listing");
	     	exit;
	}
	/*
	Declaring the variables 
	*/
	$service_name= $listall[0]['p1ms_name'];
	$num_auto=$listall[0]['p1ms_number_auto'];
	$auth_id=$listall[0]['p1au_id'];
	$pay_type=$listall[0]['p1_payment_id'];
	$button_image=$listall[0]['p1pimg_id'];
	$cat_id=$listall[0]['p1ca_id'];
	if($auth_id==1)
	{
	    $pay_types=explode(",",$pay_type);
	    $pay_id=$auth_id;
	    $home_id=$pay_types[0];
	}
	else{
	    $pay_id=$auth_id;
	    $home_id=($auth_id==3) ? "" :$pay_type;
	}

	$mylogo= $listall[0]['p1ms_logo'];
	if($page_action_val != 'serviceEdit')
	{
	    $mylogo= $listall[0]['p1ms_logo'];
	    $_SESSION['image_nameTMPadm']=$mylogo;
	}
	else{
	     $mylogo=$_SESSION['image_nameTMPadm'];
	}
	$helptext= $listall[0]['p1ms_comments'];
	$debug = array('file'=>'services.php', 'line'=>'get_names');
	$result_names=$services_obj->get_countlists("tbl_categories cat,tbl_payone_images img","cat.p1ca_name as catname,img.p1pimg_image as image,img.p1pimg_default as img_defaut, img.p1pimg_id as image_id","p1pimg_id=".$button_image." AND p1ca_id=".$listall[0]['p1ca_id'],$debug);/*get_names*/ 
$_SESSION['new_default_image']['id'] = $image_id = ($_SESSION['new_default_image']['id'] == '') ? $result_names[0]['image_id'] : $_SESSION['new_default_image']['id'];
$image_default = ($_SESSION['new_default_image']['default'] == '') ? $result_names[0]['img_defaut'] : $_SESSION['new_default_image']['default'];
$button_image = ($_SESSION['new_default_image']['image'] == '') ? $result_names[0]['image'] : $_SESSION['new_default_image']['image'];

	$sql = $services_obj->get_countlists('tbl_authentications','p1au_id as pay_id,p1au_name as pay_name,p1au_parent as parent,p1au_code as pay_code','1');

	for($i=0;$i<count($sql);$i++)
	{
	     $payment_types[$sql[$i]['pay_id']]=array("id"=>$sql[$i]['pay_id'],"name"=>$sql[$i]['pay_name'],"parent_id"=>$sql[$i]['parent'],"code"=>$sql[$i]['pay_code']);
	}

	// Assign values to smarty
	
	if($page_action == 'edit')
	{
	    $service_name=trim($_POST['serviceName']);
	    $helptext= trim($_REQUEST['helpText']);
	    $image_id=trim($_REQUEST['image_id']);
            $num_auto=trim($_REQUEST['auto_num']);
	    $category=trim($_REQUEST['category']);
	    $oldcategory=trim($_REQUEST['catValhid']);
		
	    // Validation start for add service
	    $errresult= array();
	    $service_name_val = $validation_obj->ChkInput($service_name,'service name');
	    if($service_name_val!="")
	    {
		$errresult[]=$service_name_val;
	    }
	    $srce_name = $validation_obj->escape_string($service_name);
	    $debug = array('file'=>'services.php', 'line'=>'check_service_exists');
	    $serv_exist =$services_obj->service_chk(addslashes($srce_name),$merchant_id,$page_action,$serId,$debug);/*check_service_exists*/
	    if(count($serv_exist))
	    {
		$errresult[]="<strong>".$srce_name."</strong> service already exists.";
	    }
	    if($mylogo=="")
	    {
		$errresult[]="Upload service logo.";
	    }
	    else{
	        $mylogo=$_SESSION['image_nameTMPadm'];
	    }

	    $admin_cat_status=CATEGORY_STATUS_INACTIVE;
	    if($category =="others")
	    {					
		$cat=trim($_REQUEST['txt_otr_cat']);
		if($cat == "")
		{
		     $errresult[]="Enter category name."; 
		}
		else
		{
		    $cat_name_err = $validation_obj->ValidAlphaNumSpl(trim($cat),'Category Name');	
		    if($cat_name_err=="")
		    {
			$cat_ins = $validation_obj->escape_string($cat);
			$debug = array('file'=>'services.php', 'line'=>'check_category_exists');
			$cat_exist =$services_obj->get_drop_downlists('tbl_categories','p1ca_id,p1me_id',"p1ca_name='".addslashes($cat_ins)."' AND (p1me_id IS NULL OR p1me_id=".$merchant_id.") AND  p1ca_stflag=1");/*check_category_exists*/
			// Validate for category name already exists.
			if(count($cat_exist))
			{
			     if($cat_exist[0]['p1me_id']==CAT_ADD_ADMIN)
			     {
				  $errresult[]="<strong>$cat</strong> category already added by admin.";
			     }
			else{
				$errresult[]="<strong>$cat</strong> category already exists.";
			}
			$admin_cat_status=CATEGORY_STATUS_ACTIVE;
			}
		    }
		    else
		    {
		        $errresult[]=$cat_name_err;
		    }
		}		
	    }
	    else{
		if($category =="")
		{
		     $errresult[]="Select service category.";
		}
	    }	
	    // validation ends	
	    if(count($errresult)==NO_ERROR)
	    {		
	        $auto=($num_auto=="yes")?1:0;
		if($category =="others")
		{
			$cat = trim($_REQUEST['txt_otr_cat']);	
			$cat_ins = $validation_obj->escape_string($cat);
			$insertField = "p1me_id,p1ca_name,p1ca_stflag,p1ca_added,p1ca_updated";
			$insertValues = "$merchant_id,'".addslashes($cat_ins)."',".CATEGORY_STATUS_ACTIVE.",now(),now()";
			if($admin_cat_status !=CATEGORY_STATUS_ACTIVE)
			{
			$debug = array('file'=>'services.php', 'line'=>'add_category');
			$authList = $services_obj->insertVal("tbl_categories",$insertField,$insertValues,$debug);/*add_category*/
			$debug = array('file'=>'services.php', 'line'=>'get_category_id');
			$authList = $services_obj->get_drop_downlist('tbl_categories','max(p1ca_id) as lastid','1',$debug);/*get_category_id*/
			$addcat= $authList[0]['lastid'];	
			}
		}
		else
		{
			$addcat= $category; 
		}	
		
		$help_text =preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", htmlspecialchars_decode($_REQUEST['helpText']));
		$help_text =htmlspecialchars($help_text);
		$help_text = $validation_obj->escape_string($help_text);
		$im="p1ms_logo='".addslashes($mylogo)."',";
		$updatefields="p1ms_name = '".addslashes($srce_name)."',$im p1ms_comments='".addslashes($help_text)."',p1ms_number_auto=".$auto.",p1ms_updated=now(),p1pimg_id=".$image_id.",p1ca_id=".$addcat;
		$wrcon = "p1me_id=".$merchant_id." and p1ms_id = ".$serId;
		$debug = array('file'=>'services.php', 'line'=>'update_service');
		$updateTmp = $services_obj->get_itm_update("tbl_merchant_services",$updatefields,$wrcon,$debug);/*update_service*/
		
		if($oldcategory!=$addcat)
		{		
		//update category for all product		
		$debug = array('file'=>'services.php', 'line'=>'service_products_display');
		$itemList=$this->updateCmsProduct($serId,"Product  Display: CMS Response: ",$addcat,0,$debug);/*service_products_display*/
		}
		unset($_SESSION['new_default_image']);	
		$_SESSION['success']="updated";
		header("Location:services.php?action=edit&serid=".$serId);
		exit;		
	    }
	    else{
		$cat_id=$category;

		//Assign the post values
		$smarty->assign('serviceName', $service_name);
		$smarty->assign('cat', $cat_id);
		$smarty->assign('otherscat', $cat);
		$smarty->assign('helpTxt', htmlspecialchars_decode($helptext));
                $smarty->assign('num_auto', $num_auto);
	    }			
	}
	// To show file upload operation start
	$ajaxFileUploader = new AjaxFileuploader($uploadDirectory="data/images/original/");
	$uploadfiles = $ajaxFileUploader->displayFileUploader('id1',$mylogo,52,$page_action);
	$smarty->assign('uploadfiles', $uploadfiles);
	// To show file upload operation end
	// Assign values to smarty 
	$smarty->assign('categories', $categories);
	$smarty->assign('cat_id', $cat_id);
	$smarty->assign('num_auto', $num_auto);
	$smarty->assign('home_type', $home_id);
	$smarty->assign('pay_type', $pay_id);
	$smarty->assign('payment_types', $payment_types);
	$smarty->assign('result_names', $result_names);
	$smarty->assign('caction', 'serviceEdit');
	$smarty->assign('helpTxt', htmlspecialchars_decode($helptext));
	$smarty->assign('serviceName', $service_name);
	$smarty->assign('errmsg', $errresult);
	$smarty->assign('success', $_SESSION['success']);
	$smarty->assign('image_id', $image_id);
	$smarty->assign('image_default', $image_default);
	$smarty->assign('button_image', $button_image);
	unset($_SESSION['success']);
	$smarty->assign('viewlist', $listall);
	$smarty->assign('payment_id', $auth_id);
	$smarty->assign('pagetitle', 'PaymentOne: Edit service');
	$smarty->assign('edit_image_id', $edit_image_id);
	$smarty->assign('content', $glb_adm_tpl_path.'editservice.tpl');
		
    }

    /* 
	Activate the service 
    */

     function activateService()
     {
	 global $services_obj,$glb_adm_tpl_path,$smarty,$common_obj;
	 $merchant_id=$_SESSION['merchant_id'];
	 $page_action = trim($_REQUEST['action']);
	 $page_action_val = trim($_REQUEST['actionVal']);
	 $serId = trim($_GET['serid']);
	 $debug = array('file'=>'services.php', 'line'=>'get_service_details');
	 $getsnames=$services_obj->get_drop_downlist('tbl_merchant_services','p1ms_name as sname',"p1ms_id=$serId",$debug);/*get_service_details*/
	 if($getsnames[0]['sname']=="")
	 {
	     header("Location:erroraccess.php?page_err=yes");
	     exit;
	 }
	 // To update temp items start
	 $updatefields="p1ms_stflag =".SERVICE_STATUS_ACTIVE;
	 $wrcon = "p1ms_id=".$serId;
	 $debug = array('file'=>'services.php', 'line'=>'update_service');
	 $updateTmp = $services_obj->get_itm_update("tbl_merchant_services",$updatefields,$wrcon,$debug);/*update_service*/
	 $errormsg = "<div class='success'><strong>".$getsnames[0]['sname']."</strong> has been successfully activated.</div>";
	 $debug = array('file'=>'services.php', 'line'=>'activate_service_paging');
	 $this->servicePaging($debug);/*activate_service_paging*/
	 // paging starts
	 $smarty->assign('errmessage', $errormsg);
	 $smarty->assign('content', $glb_adm_tpl_path.'services.tpl' );
    }

    /* 
	Suspend the service 
    */

    function suspendService()
    {
        global $services_obj,$glb_adm_tpl_path,$smarty,$common_obj;
	$merchant_id=$_SESSION['merchant_id'];
	$page_action = trim($_REQUEST['action']);
	$page_action_val = trim($_REQUEST['actionVal']);
	$serId = trim($_GET['serid']);
	$debug = array('file'=>'services.php', 'line'=>'get_service_details');
	$getsnames=$services_obj->get_drop_downlist('tbl_merchant_services','p1ms_name as sname',"p1ms_id=$serId",$debug);/*get_service_details*/
	if($getsnames[0]['sname']=="")
	{
	     header("Location:erroraccess.php?page_err=yes");
	     exit;
	}
	// To update temp items start
	$updatefields="p1ms_stflag =".SERVICE_STATUS_SUSPEND;
	$wrcon = "p1ms_id=".$serId;	
	$debug = array('file'=>'services.php', 'line'=>'update_service');
	$updateTmp = $services_obj->get_itm_update("tbl_merchant_services",$updatefields,$wrcon,$debug);/*update_service*/	
	$errormsg = "<div class='success'><strong>".$getsnames[0]['sname']."</strong> has been successfully suspended.</div>";
	$debug = array('file'=>'services.php', 'line'=>'suspend_service_paging');
	$this->servicePaging($debug);/*suspend_service_paging*/
	// paging ends
	$smarty->assign('errmessage', $errormsg);
	$smarty->assign('content', $glb_adm_tpl_path.'services.tpl');
    }

    function updateService()
    {
        global $smarty,$glb_adm_tpl_path,$services_obj;
	$sid=trim($_POST['service_id']);
	$item_status=trim($_POST['item_display']);
	$status=($item_status!="")?$item_status :0 ;
	$debug = array('file'=>'services.php', 'line'=>'update_service');
	$updatefields="p1ms_iflag=".$status.",p1ms_updated=now(),p1ms_stflag=1";
	$update_service = $services_obj->get_itm_update("tbl_merchant_services",$updatefields,"p1ms_id=".$sid,$debug);/*update_service*/
	if($update_service)
	{
	     $_SESSION['success']="service_updated";
	     header("Location:merchant.php?page=templates&sid=".$sid."&assign=template");
	     exit;
	}
	else{
	    header("Location:erroraccess.php?page_err=yes");
	    exit;
	}
     }

     /* 
	Delete the service 
     */
     function deleteService()
     {
         global $services_obj,$glb_adm_tpl_path,$smarty,$common_obj,$glb_cms_product_url;
	 $serId = trim($_GET['serid']);
	 $debug = array('file'=>'services.php', 'line'=>'get_service_details');
	 $getsnames=$services_obj->get_drop_downlist('tbl_merchant_services','p1ms_name as sname',"p1ms_id=$serId",$debug);/*get_service_details*/
	 if($getsnames[0]['sname']=="")
	 {
	     header("Location:erroraccess.php?page_err=yes");
	     exit;
	 }
	 $debug = array('file'=>'services.php', 'line'=>'check_transaction');
	 // To update temp items start (Delete)
	 $getsnamesVal=$services_obj->get_drop_downlist('tbl_transactions','count(*) as totcnt',"p1ms_id=$serId",$debug);/*check_transaction*/
	 $gettot =$getsnamesVal[0]['totcnt'];		
	 if(!$gettot) // Check whether the service contains transactions.
	 {	
	      $debug = array('file'=>'services.php', 'line'=>'update_deleteservice');
	      $updateTmp = $services_obj->get_itm_update("tbl_merchant_services","p1ms_stflag =".SERVICE_STATUS_DELETE,"p1ms_id=".$serId);/*update_deleteservice*/
		
	     // to delete the products in CMS and p1 database - when service is deleted.
	     $debug = array('file'=>'services.php', 'line'=>'delete_service_products_display');
	     $itemList=$this->cmsProductList($serId,"Delete Service -Product  Display: CMS Response: ",$debug);/*delete_service_products_display*/
	     if(count($itemList) > 0)
	     {
		for($i=0;$i<count($itemList);$i++)
		{
		
		$str =  $common_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],'/'.$itemList[$i]['productid']);		
		
		$find = array('<state>active</state>');
	    	$replace = array('<state>inactive</state>');
	    	$str_sku = str_replace($find, $replace, $str);
		if(stristr($str_sku, '<purchase_type>onetime</purchase_type>') == TRUE) 
	    	{
    	    		$str_sku = str_replace('</end_date>','</end_date><frequency></frequency>',$str_sku);
            	}
            	$updateresult= $common_obj->CmsCurlPut($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$str_sku,'/'.$itemList[$i]['productid'],$debug);
		
		// delete records from p1 database - items and items attribute table
		$mid=$_SESSION['merchant_id'];
		$wherecon="p1cms_product_id ='".$itemList[$i]['productid']."' and p1me_id='$mid' ";
		$dele_itm=$services_obj->service_delete('tbl_merchants_items',$wherecon);
		}
	     }
	// To update temp items end			
	$errormsg = "<div class='success'><strong>".$getsnames[0]['sname']."</strong> has been successfully deleted.</div>";
	 }	
	 else
	 {
	     $errormsg = "<div class='error'><strong>".$getsnames[0]['sname']."</strong> contains transactions, you cannot delete this service.</div>";
	 }
	 // paging starts
	 $debug = array('file'=>'services.php', 'line'=>'delete_service_paging');
	 $this->servicePaging($debug);/*delete_service_paging*/
	 // paging ends
	 $smarty->assign('errmessage', $errormsg);
	 $smarty->assign('content', $glb_adm_tpl_path.'services.tpl');
     }

    /*
	To display the items from CMS
    */
    function cmsProductList($serviceid,$msg,$findline="")
    {
        $arguments = array($serviceid,$msg);
	$this->debug_obj->WriteDebug($class="serviceModule", $function="cmsProductList", $findline['file'], $this->debug_obj->FindFunctionCalledline('cmsProductList', $findline['file'], $findline['line']), $arguments);

	global $glb_cms_product_url;
	$cmsval_obj= new Common();
	$query_string = "?service=$serviceid&start=0&max=50";
	$debug = array('file'=>'services.php', 'line'=>'cms_products_lists');
	$items_xml =  $cmsval_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_products_lists*/
	/*
	If blocks - To validate the cms response -Log the errors in cms log file
	*/	
	if(!(stristr($items_xml, '<?xml')))
	{
		$items_xml=($items_xml=="") ? "No Response" : "";
		$cmsval_obj->WriteCMSLog($msg.$items_xml, $glb_adm_path."services.php");
		header( 'Location:'.DOMAIN_NAME.'admin/erroraccess.php?page_err=yes' );
		exit;
	     	$itemListTmp=array();
	     	return $itemListTmp;
	}
	else{
		$result_xml = simplexml_load_string($items_xml);
		$arrayValue = array();
		foreach($result_xml->elements->product as $key=> $value)
		{
			$productId = (string)$value->attributes()->product_id ;
			unset($value->attributes()->product_id);
			$value = (array) $value ;
			$value["productid"] = $productId ;
			$itemListTmp[] = $value ;
		}
		return $itemListTmp;
	}
     }


     /*
	To display the view product details
     */

     function viewProductList($serviceid,$msg,$findline="")
     {	
	$arguments = array($serviceid,$msg);	
	$this->debug_obj->WriteDebug($class="serviceModule", $function="viewProductList", $findline['file'], $this->debug_obj->FindFunctionCalledline('viewProductList', $findline['file'], $findline['line']), $arguments);
	
	global $glb_cms_product_url,$services_obj,$validation_obj;	
	$merchant_id=$_SESSION['merchant_id'];
	//$sessId=session_id();	
	if(isset($_REQUEST['co_code']) && $_REQUEST['co_code']!="")
	{	
		$price_country_code=$_REQUEST['co_code'];
	} 
	else 
	{
		$price_country_code="US";
	}	
	$cmsval_obj= new Common();
	$query_string = "?service=$serviceid&price_country_code=$price_country_code&start=0&max=50";
	$debug = array('file'=>'services.php', 'line'=>'viewProductList');
	$items_xml =  $cmsval_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*viewProductList*/	
	/*
	If blocks - To validate the cms response -Log the errors in cms log file
	*/
	if(!(stristr($items_xml, '<?xml')))
	{
		$itemListTmp=array();
		$result_items=array();
		return $itemListTmp;
	}
	else
	{
	     $i=0;
	     $country= array();
	     $cms_product_count=0;
	     $result_xml = simplexml_load_string($items_xml);		
	     foreach($result_xml->elements->product as $key=> $value)
	     {
	         $productId = (string)$value->attributes()->product_id;
	         unset($value->attributes()->product_id);
	         $value = (array) $value ;
	         $value["product_id"] = (string)$productId;
		 $pay_types=$value["purchase_type"];
		 $cdate=$value["effective_date"];
		 $sku=$value["sku"];
		 $pname="";
		 $des=$value["default_description"];
		 $plan=$value["plan_id"];
		 $tariff=$value["tariff"];
		 $min_tariff=$value["min_tariff"];
		 $default_country_code=$value["default_country_code"];
		 $pname= $validation_obj->escape_string($pname);
		 $des= $validation_obj->escape_string($des);
		 $plan= $validation_obj->escape_string($plan);
		 $sku= $validation_obj->escape_string($sku);

		//Check the P1 Database for sync
		 $debug = array('file'=>'services.php', 'line'=>'get_product_sync');		
                 $chk_product= $services_obj->get_drop_downlists('tbl_merchants_items','p1cms_product_id','p1cms_product_id='.$productId,$debug);/*get_product_sync*/
		
		if(count($chk_product)==0)
		{
			if($pay_types=="subscription")
			{
			$ptype=2;
			$frequency=3;	
			$insertField="p1ms_id,p1me_id,p1mi_code,p1pt_id,p1pf_id,p1mi_stflag,p1mi_added,p1cms_product_id";
		 	$insertValues="'".$serviceid."','".$merchant_id."','".addslashes($sku)."','".$ptype."','".$frequency."','1','".$cdate."','".$productId."'";
			}
			else
			{
			$ptype=1;
			$frequency='';
			$insertField="p1ms_id,p1me_id,p1mi_code,p1pt_id,p1mi_stflag,p1mi_added,p1cms_product_id";
		 	$insertValues="'".$serviceid."','".$merchant_id."','".addslashes($sku)."','".$ptype."','1','".$cdate."','".$productId."'";
			}
		   	$debug = array('file'=>'service.php', 'line'=>'insert_item_sync');
		    	$p1items = $services_obj->insertVal("tbl_merchants_items",$insertField,$insertValues,$debug);/*insert_item_sync*/
		}
		// End Sync CMS and Local DB		

		 $xml_array=array();
		 foreach($value as $k=>$v)
                 {
			$xml_array1=array();
			if(is_array($v))
			{			
				if($k=="price")
				{					
					foreach($v as $k1=>$v1)
					{ 
						$price_id=(string)$v1->attributes()->price_id;
						unset($v1->attributes()->price_id);
						$v1=(array)$v1;
						$v1["price_id"]=$price_id;
						$xml_array1[$v1["product_offering_code"]][]=$v1;
						//$xml_array1[$k1]["price_id"]=$price_id;
					}
					$xml_array["price"]=$xml_array1;
					$cms_product_count++;	
				}
				else 
				{
					$xml_array[$k] = $v;
				}
			}
			else
			{				
				if(is_object($v))
				{
					if($k=="price")
					{
						$priceId = (string)$v->attributes()->price_id;
						unset($v->attributes()->price_id);
						$v=(array)$v;
						$v["price_id"] = (string)$priceId;
						$xml_array1[$v["product_offering_code"]][]=$v;
						$xml_array["price"]=$xml_array1;
					}

					if($k=="description")
					{
						$desId = (string)$v->attributes()->description_id;
						unset($v->attributes()->description_id);
						$v=(array)$v;
						$v["des_id"] = (string)$desId;
						//$xml_array1[$v["product_offering_code"]][]=$v;
						$xml_array["description"]=$v;//$xml_array1;
					}		
				}
				else
				{					
					$xml_array[$k] = $v;
				}
			}
 
                 }
		
		$itemListTmp[] = $xml_array;

		//Check the P1 order for sync
		/*if($itemListTmp[$i]['available_country_code']!='' && is_array($itemListTmp[$i]['available_country_code']))
		{ 
			$country=$itemListTmp[$i]['available_country_code'];
		}
		elseif($itemListTmp[$i]['available_country_code']!='')
		{
			$country[]=$itemListTmp[$i]['available_country_code'];
		}*/

		$wherecon="p1cms_product_id=".$productId;
                $max_product= $services_obj->get_countlists('tbl_merchants_items','p1mi_id as proid',$wherecon);  $product_merchant_id=$max_product[0]['proid'];
                array_push($country,$default_country_code);

		$wherecon="mi.p1ms_id=".$serviceid;			
            	$max_order = $services_obj->get_countlists('tbl_merchants_items mi inner join tbl_merchants_items_order mo on mi.p1mi_id=mo.p1mi_id','max(mo.p1mio_order) as orders',$wherecon);		
	    	$order=$max_order[0]['orders'] + 1;

			$country_array = $services_obj->get_countlists("tbl_country","p1co_id","p1co_code = '$default_country_code'");
                	$country_id=$country_array[0]["p1co_id"];

			$debug = array('file'=>'services.php', 'line'=>'check_product_order_exist');		
                 	$product_count=$services_obj->get_drop_downlists('tbl_merchants_items_order','p1mi_id','p1mi_id='.$product_merchant_id.' AND p1co_id='.$country_id,$debug);/*check_product_order_exist*/
			
			if(count($product_count)==0)
			{
				$insertField = "p1mi_id,p1mio_order,p1co_id";
				$insertValues = "'".$product_merchant_id."',".$order.",'".$country_id."'"; 
				$debug = array('file'=>'service.php', 'line'=>'insert_item_order');
				$orderitems = $services_obj->insertVal("tbl_merchants_items_order",$insertField,$insertValues,$debug);/*insert_item_order*/
			}	
             /*   foreach($country as $code)
                {				
                	$country_array = $services_obj->get_countlists("tbl_country","p1co_id","p1co_code = '$code'");
                	$country_id=$country_array[0]["p1co_id"];

			$debug = array('file'=>'services.php', 'line'=>'check_product_order_exist');		
                 	$product_count=$services_obj->get_drop_downlists('tbl_merchants_items_order','p1mi_id','p1mi_id='.$product_merchant_id.' AND p1co_id='.$country_id,$debug);/*check_product_order_exist*/
		/*	
			if(count($product_count)==0)
			{
				$insertField = "p1mi_id,p1mio_order,p1co_id";
				$insertValues = "'".$product_merchant_id."',".$order.",'".$country_id."'"; 
				$debug = array('file'=>'service.php', 'line'=>'insert_item_order');
				$orderitems = $services_obj->insertVal("tbl_merchants_items_order",$insertField,$insertValues,$debug);/*insert_item_order*/
		/*	}	
                }*/
		//End p1 order sync
		$i++; 
	     }		
		$result_items=$itemListTmp;
	    	//return $result_items;
	}

	//$items=$services_obj->get_countlists("tbl_merchants_items","p1cms_product_id as cms_pid,p1mi_code as sku,p1mi_disp_order as order_id","p1ms_id=".$serviceid." order by p1mi_disp_order ASC,p1mi_id");
	
	$items=$services_obj->get_countlists("tbl_merchants_items mi inner join tbl_merchants_items_order mo on mi.p1mi_id=mo.p1mi_id inner join tbl_country c on mo.p1co_id=c.p1co_id","mi.p1cms_product_id as cms_pid,mi.p1mi_code as sku,mo.p1mio_order as order_id","mi.p1ms_id=".$serviceid." and c.p1co_code='".$price_country_code."' order by mo.p1mio_order ASC,mi.p1mi_id");


	//compare CMS with local products 
	if($cms_product_count==count($items))
	{
		foreach($items as $vals)
		{
			foreach ($result_items as $k => $v)
			{				
				if($result_items[$k]['sku']==$vals['sku'])
				{                            
					$product_list[]=$v;
				} 
			}
		}
	}
	else
	{
		$product_list=$result_items;
	}

	return($product_list);
    }



     /*
	To display the items from CMS using SKU
     */

     function skuProductList($serviceid,$msg,$findline="")
     {
	$arguments = array($serviceid,$msg);
	$this->debug_obj->WriteDebug($class="serviceModule", $function="skuProductList", $findline['file'], $this->debug_obj->FindFunctionCalledline('skuProductList', $findline['file'], $findline['line']), $arguments);
	
	global $glb_cms_product_url,$services_obj,$validation_obj;
	$merchant_id=$_SESSION['merchant_id'];
	$sessId=session_id();	
	$cmsval_obj= new Common();
	$query_string = "?service=$serviceid&start=0&max=50";
	$debug = array('file'=>'services.php', 'line'=>'cms_products_lists');
	$items_xml =  $cmsval_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_products_lists*/

	$items=$services_obj->get_countlists("tbl_merchants_items","p1cms_product_id as cms_pid,p1mi_code as sku,p1mi_disp_order as order_id","p1ms_id=".$serviceid." order by p1mi_disp_order ASC,p1mi_id");
	/*
	If blocks - To validate the cms response -Log the errors in cms log file
	*/
	if(!(stristr($items_xml, '<?xml')))
	{
		$items_xml=($items_xml=="") ? "No Response" : "";
		$cmsval_obj->WriteCMSLog($msg.$items_xml, $glb_adm_path."services.php");
		header( 'Location:'.DOMAIN_NAME.'admin/erroraccess.php?page_err=yes' );
		exit;
		$itemListTmp=array();
		$result_items=array();
		return $itemListTmp;
	}
	else{
	     $result_xml = simplexml_load_string($items_xml);
	     $arrayValue = array();
	     	
	     foreach($result_xml->elements->product as $key=> $value)
	     {
		
	         $productId = (string)$value->attributes()->product_id ;
	         unset($value->attributes()->product_id);
	         $value = (array) $value ;
	         $value["product_id"] = $productId ;
	         $itemListTmp[$value['sku']][] = $value;
		 $countitem[]=$value;

		 $sku=$value["sku"];
		 $pname=$value["name"];
                 $mbloxid=$value["mblox_id"];
                 $short=$value["short_code"];
                 $plan=$value["plan_id"];
                 $freq=$value["frequency"];
                 $sms=$value["sms_description"];
                 $pay_types=$value["purchase_type"];
		 $cdate=$value["creation_date"];
		 $tariff=$value["tariff"];
		 $min_tariff=$value["min_tariff"];
		 $des=$value["description"];

		 $pname= $validation_obj->escape_string($pname);
		 $des= $validation_obj->escape_string($des);
		 $plan= $validation_obj->escape_string($plan);
		 $sku= $validation_obj->escape_string($sku);

		 //Check the P1 Database for sync
		 $debug = array('file'=>'services.php', 'line'=>'get_product');		
                 $chk_product= $services_obj->get_drop_downlists('tbl_merchants_items','p1cms_product_id','p1cms_product_id='.$productId,$debug);
		
		if(count($chk_product)==0)
		{
			if($pay_types=="subscription")
			{
			$ptype=2;
			$frequency=3;	
			$insertField="p1ms_id,p1me_id,p1mi_code,p1pt_id,p1pf_id,p1mi_stflag,p1mi_added,p1cms_product_id";
		 	$insertValues="'".$serviceid."','".$merchant_id."','".addslashes($sku)."','".$ptype."','".$frequency."','1','".$cdate."','".$productId."'";
			}
			else
			{
			$ptype=1;
			$frequency='';
			$insertField="p1ms_id,p1me_id,p1mi_code,p1pt_id,p1mi_stflag,p1mi_added,p1cms_product_id";
		 	$insertValues="'".$serviceid."','".$merchant_id."','".addslashes($sku)."','".$ptype."','1','".$cdate."','".$productId."'";
			}
			 
		   	$debug = array('file'=>'service.php', 'line'=>'insert_item');
		    	$p1items = $services_obj->insertVal("tbl_merchants_items",$insertField,$insertValues,$debug);/*insert_item*/
		}
		// End Sync CMS and Local DB
	     }
	   
		if(count($countitem)==count($items))
		{
			foreach($items as $vals)
			{
				foreach ($itemListTmp as $k => $v)
				{
					if($k==$vals['sku'])
					{
						$result_items[$k]=$v;		
					}
				}
			}

		}
		else
		{
			$result_items=$itemListTmp;
		}
	
	    return $result_items;
	}
    }

     /*
	To update item in CMS
     */

     function updateCmsProduct($serviceid,$msg,$category,$start,$findline="")
     {
	$arguments = array($serviceid,$msg);
	$this->debug_obj->WriteDebug($class="serviceModule", $function="updateCmsProduct", $findline['file'], $this->debug_obj->FindFunctionCalledline('updateCmsProduct', $findline['file'], $findline['line']), $arguments);

	global $services_obj,$glb_cms_product_url,$glb_adm_path;
	$cmsval_obj= new Common();
	$query_string = "?service=$serviceid&start=$start&max=50";
	$debug = array('file'=>'services.php', 'line'=>'cms_products_lists');
	$items_xml =  $cmsval_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_products_lists*/
	/*
	If blocks - To validate the cms response -Log the errors in cms log file
	*/
	if(!(stristr($items_xml, '<?xml')))
	{
		$items_xml=($items_xml=="") ? "No Response" : "";
		$cmsval_obj->WriteCMSLog($msg.$items_xml, $glb_adm_path."services.php");
		header( 'Location:'.DOMAIN_NAME.'admin/erroraccess.php?page_err=yes' );
		exit;
		$itemListTmp=array();
		return $itemListTmp;
	}
	else{
	     $result_xml = simplexml_load_string($items_xml);
	     $arrayValue = array();
	     $total_cms_products = (string)$result_xml->attributes()->total;
	     foreach($result_xml->elements->product as $key=> $value)
	     {
		$productId = (string)$value->attributes()->product_id ;
		unset($value->attributes()->product_id);
		$accountid=(string)$value->account_owner_id;
		$defaultcountry=(string)$value->default_country_code;
		$sku=(string)$value->sku;
		$minprice=(string)$value->minimum_price_amount;
		$sms=(string)$value->sms_default_description;
		$default_description=(string)$value->default_description;
		$purchase_type=(string)$value->purchase_type;
		$retail_price=(string)$value->retail_price_amount;
		$fixed_price=(string)$value->fixed_price_amount;
		$minimum_price=(string)$value->minimum_price_amount;
		$plan_id=(string)$value->plan_id;
		$service_id=(string)$value->service_id;
		$category_id=(string)$value->category_id;
		$effective_date=(string)$value->effective_date;
		$end_date=(string)$value->end_date;
		$frequency=(string)$value->frequency;
		$state=(string)$value->state;
	     	$detail = array();
		
		if($purchase_type=="onetime")$purchase_type="one time";

		foreach($value->description as $val)
		{
			$description_id=(string)$val->attributes()->description_id;
			unset($val->attributes()->description_id);
			$array_val=(array)$val;
			array_unshift($array_val,$description_id);			
			$des_array[]=$array_val;
		}

		foreach($value->price as $val)
		{
			$price_id=(string)$val->attributes()->price_id;			
			unset($val->attributes()->price_id);
			$val_array=(array)$val;
			array_unshift($val_array,$price_id);			
			
			$price_array[]=$val_array;				
		}		
	
		foreach($value->product_offering_code as $val)
		{
			$pro_type[]=(string)$val;
		}	
	
		foreach($value->available_country_code as $val)
		{
			$country[]=(string)$val;
		}	

		$arr_xml=array("account_owner_id"=>$accountid,"default_country_code"=>$defaultcountry,"sku"=>$sku,"default_description"=>$default_description,"sms_default_description"=>$sms,"purchase_type"=>$purchase_type,"retail_price_amount"=>$retail_price,"fixed_price_amount"=>$fixed_price,"minimum_price_amount"=>$minimum_price,"plan_id"=>$plan_id,"service_id"=>$service_id,"category_id"=>$category,"effective_date"=>$effective_date,"end_date"=>$end_date,"frequency"=>$frequency,"state"=>$state,"product_offering_code"=>$pro_type,"available_country_code"=>$country,"description"=>$des_array,"price"=>$price_array);

		if($plan_id=="")
		{
			unset($arr_xml["plan_id"]);
		}
	
		if($country=="" || count($country)==0)
		{
			unset($arr_xml["available_country_code"]);
		}

		if($sms=="")
		{
			unset($arr_xml["sms_default_description"]);
			//$arr_xml["sms_default_description"]="sds";		
		}
	
		if($arr_xml["retail_price_amount"]=="")
		{
			unset($arr_xml["retail_price_amount"]);
		}
	
		if($arr_xml["fixed_price_amount"]=="")
		{
			unset($arr_xml["fixed_price_amount"]);
		}	
		
		if($arr_xml["minimum_price_amount"]=="")
		{
			unset($arr_xml["minimum_price_amount"]);
		}
		$var_xml=$services_obj->createProductXml($arr_xml,$productId);

		$result= $cmsval_obj->CmsCurlPut($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$var_xml,'/'.$productId,$debug);
		unset($des_array);
		unset($pro_type);
		unset($country);
		unset($price_array);
		$result_text= strip_tags($result);

		if(!(stristr($result, '<?xml')))
		{
			$items_xml=($result=="") ? "No Response" : "";
			$cmsval_obj->WriteCMSLog($msg.$items_xml, $glb_adm_path."service.php");
			header( 'Location:'.DOMAIN_NAME.'admin/erroraccess.php?page_err=yes' );
			exit;
		}

	     }
	
 		$start+=50;
		
 		if($start < $total_cms_products)
 		{
			$debug = array('file'=>'services.php', 'line'=>'service_products_display');
			$itemList=$this->updateCmsProduct($serviceid,"Product  Display: CMS Response: ",$category,$start,$debug);/*service_products_display*/
 		}

	}
    }
	
     /*
	Paging for activate,suspend,delete service.
    */

    function servicePaging($findline="")
    {
        global $services_obj,$common_obj,$smarty;
	$this->debug_obj->WriteDebug($class="serviceModule", $function="servicePaging", $findline['file'], $this->debug_obj->FindFunctionCalledline('servicePaging', $findline['file'], $findline['line']), "");
	$merchant_id=$_SESSION['merchant_id'];
	// paging
	$limit = 5; // set the paging limit value
	$page="";		
	if(isset($_REQUEST['page_val']) && ($_REQUEST['page_val']!=""))
	{
	     $page=$_REQUEST['page_val'];
	     $start = ($page - 1) * $limit;
	}
	else
	{
	     $start = 0;
	}
	$varname="page_val";
	$selectCat = $_REQUEST['selectCat'];
	if($selectCat =="")
	     $selectCat = $_REQUEST['hid_selectCat'];
	$statusVal = $_REQUEST['st'];
	$debug = array('file'=>'services.php', 'line'=>'getcategories');
	$catList = $services_obj->get_drop_downlist('tbl_categories','p1ca_id as catId,p1ca_name as catName',"(p1me_id IS NULL or p1me_id=$merchant_id) and p1ca_stflag=1 order by catName",$debug);/*getcategories*/
	$tmpf="";
	$cat="viewservice";
	if($selectCat == 'All' or $selectCat == "")
	{ 
	     $appQry = ""; $qrystr1 = ""; 
	}
	else
	{ 
	    $appQry = " and tbl_ser.p1ca_id = $selectCat"; $qrystr1 = "&selectCat=$selectCat";
	}	
	// initialise the service count values default 0
	$serviceAllCount=0;
	$serviceActiveCount=0;
	$servicePendingCount=0;
	// To calculate the count of all status (start)
	$cond="tbl_ser.p1me_id=".$merchant_id." ".$appQry." GROUP BY tbl_ser.p1ms_stflag";
	$debug = array('file'=>'services.php', 'line'=>'get_count');
	$result = $services_obj->get_countlists('tbl_merchant_services tbl_ser INNER JOIN tbl_categories tbl_cat ON tbl_ser.p1ca_id=tbl_cat.p1ca_id','COUNT(*) AS total,tbl_ser.p1ms_stflag',$cond,$debug);/*get_count*/

	for($i=0;$i<count($result);$i++)
	{
	     if($result[$i]['p1ms_stflag']==1)
	     {
		  $serviceActiveCount=$result[$i]['total'];
	     }
	     if($result[$i]['p1ms_stflag']==2)
	     {
	          $servicePendingCount=$result[$i]['total'];
	     }
	}
	$serviceAllCount=$serviceActiveCount+$servicePendingCount;
	// To calculate the count of all status (end)
	if($statusVal == "all" or $statusVal == "")
	{ 
	     $appQry.=""; $qrystr = ""; 
	}
	else
	{
	     $appQry.=" and tbl_ser.p1ms_stflag = $statusVal"; $qrystr ="&st=$statusVal";
	}	
	
	$targetpage ='services.php?action=listing'."$qrystr"."$qrystr1";	
	$wherecon = "tbl_ser.p1me_id=".$merchant_id." ".$appQry." and tbl_ser.p1ms_stflag != 0 and tbl_ser.p1ms_stflag !=3"; //tbl_merchant_services tbl_ser INNER JOIN tbl_categories tbl_cat ON tbl_ser.p1ca_id=tbl_cat.p1ca_id
	$total_records=0;
	$debug = array('file'=>'services.php', 'line'=>'get_total');
	$service_viewpage = $services_obj->get_countlists('tbl_merchant_services tbl_ser INNER JOIN tbl_categories tbl_cat ON tbl_ser.p1ca_id=tbl_cat.p1ca_id','count(tbl_ser.p1ms_id) AS totalvalue',$wherecon,$debug);/*get_total*/
	$total_records= $service_viewpage[0]['totalvalue'];
	$debug = array('file'=>'services.php', 'line'=>'get_services_result');
	$service_viewpage_perpage = $services_obj->get_countlists('tbl_merchant_services tbl_ser INNER JOIN tbl_categories tbl_cat ON tbl_ser.p1ca_id=tbl_cat.p1ca_id','tbl_ser.p1ms_name as servicename,tbl_ser.p1ms_id as serviceId,tbl_ser.p1ms_stflag as stflag,tbl_cat.p1ca_name as cat_name',$wherecon.' ORDER BY tbl_ser.p1ms_updated DESC LIMIT '.$start.','.$limit,$debug);/*get_services_result*/
	// to stay in same page for pagination having single record with transaction in delete functionality
	
	if(count($service_viewpage_perpage)==1)
	{
	     $debug = array('file'=>'services.php', 'line'=>'stay_samepage');
	     $fetch_serid=$service_viewpage_perpage[0]['serviceId'];
	     $getsnamesVal=$services_obj->get_drop_downlist('tbl_transactions','count(*) as totcnt',"p1ms_id=$fetch_serid and p1tr_stflag=1",$debug);/*stay_samepage*/
	     $gettot =$getsnamesVal[0]['totcnt'];
	     if($gettot)
	     {
	          $page_hid=$page;
	     }	
	     else{
	          $page_hid=($page > 1 ? $page-1 : "");
	     }
	}
	else{
		$page_hid=$page;
	}
	$pagination=$common_obj->Pagination($total_records,$limit,$targetpage,$page,$start,$varname);
	$smarty->assign('hidpage',$page_hid);
	$smarty->assign('pagination',$pagination);
	$smarty->assign('serviceAllCount', $serviceAllCount);
	$smarty->assign('serviceActiveCount', $serviceActiveCount);
	$smarty->assign('servicePendingCount', $servicePendingCount);
	$smarty->assign('selCat', $selectCat);
	$smarty->assign('catList', $catList);
	$smarty->assign('catfilter', $qrystr1);
	$smarty->assign('pageContents', $service_viewpage_perpage);
     }
}
new serviceModule();
$smarty->assign('page_name', 'admin_services');
$smarty->assign('currentpage', 'services');
$smarty->assign('header', $glb_adm_tpl_path.'header.tpl' );
$smarty->assign('sidebar', $glb_adm_tpl_path.'sidebar.tpl' );
$smarty->assign('footer', $glb_adm_tpl_path.'footer.tpl' );
$smarty->display($glb_adm_tpl_path.'index.tpl');
?>