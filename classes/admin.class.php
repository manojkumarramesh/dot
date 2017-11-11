<?php
ob_start();
//-------------------------------------------------------------------------------------------------------------------
// File name   : admin.class.php
// Description : Handles Admin related tasks
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 11-03-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
class Admin extends ClassGeneral 
{
	var $db_connect;
	var $tbl_name;
	var $debug_obj;
	const CMS_INACTIVE_STATUS=0;
	const CMS_SUSPEND_STATUS=2;
	const CMS_ACTIVE_STATUS=1;
	function __construct()
	{	
	
		global $glb_obj_genral,$debug_obj,$admin_obj;
		$this->db_connect = $glb_obj_genral->db_connect;
		$this->debug_obj = $debug_obj;
		$this->admin_obj = $admin_obj;
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: GetAdminSelect( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose: Select the Administrator information
	// arguments: $tablename,$fieldname,$whereCon,$findline
	// ---------------------------------------------------------------------------------------------------------------
	function GetAdminSelect($tablename="",$fieldname="",$whereCon="", $findline="")
	{
		$arguments = array($tablename,$fieldname,$whereCon);
		//Debugging	GetAdminSelect
		$this->debug_obj->WriteDebug($class="admin.class", $function="GetAdminSelect", $findline['file'], $this->debug_obj->FindFunctionCalledline('GetAdminSelect', $findline['file'], $findline['line']), $arguments);
		$get_admin_select = $this->db_connect->querySelect("CALL uspGet_admin(\"$tablename\", \"$fieldname\", \"$whereCon\")");
		$this->db_connect->closedb();
		return $get_admin_select;	
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: GetAdminInsert ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose:		 Admin inserts information
	// arguments:		$tablename,$fieldname,
	// ---------------------------------------------------------------------------------------------------------------
	function GetAdminInsert($tablename="",$fieldname="",$fieldval="",$findline="")
	{	
		$arguments = array($tablename,$fieldname,$fieldval);
		//Debugging	GetAdminInsert
		$this->debug_obj->WriteDebug($class="admin.class", $function="GetAdminInsert", $file=$_SERVER['PHP_SELF'], $this->debug_obj->FindFunctionCalledline('GetAdminInsert', $findline['file'], $findline['line']), $arguments);			
		$get_merchant_insert = $this->db_connect->query("CALL uspInsert_admin(\"$tablename\", \"$fieldname\", \"$fieldval\")");
		$this->db_connect->closedb();
		if($get_merchant_insert)
			return 1;
            	else
              		return 0;	
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: GetAdminUpdate ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose:		Update the  Admin update information
	// arguments:		$tablename,$values
	// ---------------------------------------------------------------------------------------------------------------
	function GetAdminUpdate($tablename="",$fieldval="", $wherecon="",$findline="")
	{	
		$arguments = array($tablename,$fieldval,$wherecon);
		//Debugging	GetAdminUpdate
		$this->debug_obj->WriteDebug($class="admin.class", $function="GetAdminUpdate", $file=$_SERVER['PHP_SELF'], $this->debug_obj->FindFunctionCalledline('GetAdminUpdate', $findline['file'], $findline['line']), $arguments);			
		$get_merchant_update = $this->db_connect->query("CALL uspUpdate_admin(\"$tablename\", \"$fieldval\", \"$wherecon\")");
		$this->db_connect->closedb();
		
		if($get_merchant_update)
			return 1;
            	else
              		return 0;	
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: CheckP1Admin ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose: Check the administrator in the P1 Database and insert the admin details if not exists in the P1
	// arguments: $xml_array,$findline
	// ---------------------------------------------------------------------------------------------------------------
	function CheckP1Admin($xml_array, $findline="")
	{
		global $glb_tbl_admin,$glb_admin_column,$glb_tbl_admin_profile,$glb_admin_profile_column,$glb_tbl_admin_log,$glb_admin_log_column;	
		//Debugging	CheckP1Admin
		$arguments = array($xml_array);
		$this->debug_obj->WriteDebug($class="admin.class", $function="CheckP1Admin", $findline['file'], $this->debug_obj->FindFunctionCalledline('CheckP1Admin', $findline['file'], $findline['line']), $arguments);
		//Check the user existence in the P1 Database
		$debug = array('file'=>'admin.class.php', 'line'=>'select_p1_admin');
		$adm_info =self::GetAdminSelect("$glb_tbl_admin","$glb_admin_column[ad_id],$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag]","$glb_admin_column[ad_email]='".$xml_array['email']."' and $glb_admin_column[cms_account_id]=".$xml_array['account_id'],$debug);/*select_p1_admin*/
		$adm_cnt=count($adm_info);
		//Check If admin user exists in the P1 Database
		if($adm_cnt>0)
		{
			$admin_id = $adm_info[0]["$glb_admin_column[ad_id]"];
			$admin_email = $adm_info[0]["$glb_admin_column[ad_email]"];//Username
			$admin_password = $adm_info[0]["$glb_admin_column[ad_pass]"];
			$admin_status = $adm_info[0]["$glb_admin_column[ad_stflag]"];
			$curr_session_id = session_id();
			$curr_ip_address = $_SERVER['REMOTE_ADDR'];
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			//If CMS account details are not available in PaymentOne database, then insert the records into the local database
			//Insert the values in the tbl_admin 
			$insert_fields="$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag],$glb_admin_column[ad_added],$glb_admin_column[cms_account_id]";
			$debug = array('file'=>'admin.class.php', 'line'=>'insert_p1_admin');
			$insert_values =" '".$xml_array['email']."','".md5($xml_array['password'])."','1',now(),'".$xml_array['account_id']."'";
			$insert_cms=self::GetAdminInsert("$glb_tbl_admin",$insert_fields,$insert_values,$debug);/*insert_p1_admin*/
			if($insert_cms)
			{
				//Get the last insert id starts
				$debug = array('file'=>'admin.class.php', 'line'=>'max_id');
				$last_ins_id = self::GetAdminSelect("$glb_tbl_admin","max($glb_admin_column[ad_id]) as lastid",'1',$debug);/*max_id*/
				$lst_id= $last_ins_id[0]['lastid'];
				//Insert the values in tbl_admin_profile
				if($lst_id!="")
				{
					$ins_profile_flds= "$glb_admin_profile_column[ad_id],$glb_admin_profile_column[ap_fullname],$glb_admin_profile_column[ap_nickname],$glb_admin_profile_column[ap_display_name]";
					$ins_pro_values= " '".$lst_id."','".$xml_array['nick_name']."','".$xml_array['nick_name']."','".$xml_array['nick_name']."'";
					$debug = array('file'=>'admin.class.php', 'line'=>'ins_p1_admin_profile');
					$ins_cms_pro=self::GetAdminInsert("$glb_tbl_admin_profile",$ins_profile_flds,$ins_pro_values,$debug);/*ins_p1_admin_profile*/
					if($ins_cms_pro)
					{
						$debug = array('file'=>'admin.class.php', 'line'=>'select_p1_admin_1');
						$adm_info=self::GetAdminSelect("$glb_tbl_admin","$glb_admin_column[ad_id],$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag]","$glb_admin_column[cms_account_id]=''",$debug);/*select_p1_admin_1*/
						if($adm_info)
						{	
							$admin_id = $adm_info[0]["$glb_admin_column[ad_id]"];
							$admin_email = $adm_info[0]["$glb_admin_column[ad_email]"];//Username
							$admin_password = $adm_info[0]["$glb_admin_column[ad_pass]"];
							$admin_status = $adm_info[0]["$glb_admin_column[ad_stflag]"];
							$curr_session_id = session_id();
							$curr_ip_address = $_SERVER['REMOTE_ADDR'];
							$user_agent = $_SERVER['HTTP_USER_AGENT'];
						}
						else
						{
							return "DB SELECT ERROR";
						}
					}
					else
					{
						return "DB INSERT ERROR";
					}
				}
				else
				{
					return "DB SELECT ERROR";
				}
			}
			else
			{
				return "DB INSERT ERROR";
			}
			
		}
		//Insert the values into the Admin log
		$insert_fields = " $glb_admin_log_column[ad_id],$glb_admin_log_column[al_session_id],$glb_admin_log_column[al_user_agent],$glb_admin_log_column[al_logged_in_on],$glb_admin_log_column[al_session_starts_at],$glb_admin_log_column[al_ip_addr],$glb_admin_log_column[al_stflag],$glb_admin_log_column[al_session_status]";
		$debug = array('file'=>'admin.class.php', 'line'=>'insert_log');
		$insert_values =" '".$admin_id."','".$curr_session_id."','".$user_agent."',now(),now(),'".$curr_ip_address."','1','1'";
		$insert_log = self::GetAdminInsert("$glb_tbl_admin_log",$insert_fields,$insert_values,$debug);/*insert_log*/
		if($insert_log)
		{
			//Update the Last Login time into the Admin table
			$debug = array('file'=>'admin.class.php', 'line'=>'update_admin_1');
            		$update_fields="$glb_admin_column[ad_lastlogin] = now()";
            		$update_time = self::GetAdminUpdate("$glb_tbl_admin",$update_fields,"$glb_admin_column[ad_id]='$admin_id'",$debug);/*update_admin_1*/
			if($update_time)
			{
				$debug = array('file'=>'admin.class.php', 'line'=>'select_p1_admin_profile_1');
				$admin_profile_info=self::GetAdminSelect("$glb_tbl_admin_profile","$glb_admin_profile_column[ap_id],$glb_admin_profile_column[ad_id],$glb_admin_profile_column[ap_fullname],$glb_admin_profile_column[ap_nickname],$glb_admin_profile_column[ap_display_name]","$glb_admin_profile_column[ad_id]='$admin_id'",$debug);/*select_p1_admin_profile_1*/
				if($admin_profile_info)
				{
					$adm_pro_id = $admin_profile_info[0]["$glb_admin_profile_column[ap_id]"];
					$adm_pro_full_name = $admin_profile_info[0]["$glb_admin_profile_column[ap_fullname]"];
					$adm_pro_nickname = $admin_profile_info[0]["$glb_admin_profile_column[ap_nickname]"];
					$adm_pro_display_name = $admin_profile_info[0]["$glb_admin_profile_column[ap_display_name]"];
				}
				else
				{
					return "DB INSERT ERROR";
				}
			}
			else
			{
				return "DB UPDATE ERROR";
			}
		}
		else
		{
			return "DB INSERT ERROR";
		}
		$p1_admin=array_merge($adm_info[0],$admin_profile_info[0]);
		return $p1_admin;
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: InsertP1Admin ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose: Insert the admin details when forgetting the password of Admin
	// arguments: $cms_account_id,$cms_ad_email,$cms_nickname,$cms_status,$findline
	// ---------------------------------------------------------------------------------------------------------------
	function InsertP1Admin($cms_account_id,$cms_ad_email,$new_pwd,$cms_nickname,$cms_status,$findline="")
	{
		global $glb_tbl_admin,$glb_admin_column,$glb_tbl_admin_profile,$glb_admin_profile_column;
		$arguments = array($cms_account_id,$cms_ad_email,$cms_nickname,$cms_status);
		//Debugging InsertP1Admin
		$this->debug_obj->WriteDebug($class="admin.class", $function="InsertP1Admin", $findline['file'], $this->debug_obj->FindFunctionCalledline('InsertP1Admin', $findline['file'], $findline['line']), $arguments);
		$debug = array('file'=>'admin.class.php', 'line'=>'select_admin');
		//Get the information from P1 Database
		$pwd_info = self::GetAdminSelect("$glb_tbl_admin","$glb_admin_column[ad_id],$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag]","$glb_admin_column[ad_email]='$cms_ad_email' and $glb_admin_column[cms_account_id]='$cms_account_id'",$debug);/*select_admin*/
		$row_count = count($pwd_info);
		if($row_count==0)
		{
			if($cms_status=="active")
			{
				$cms_status = self::CMS_ACTIVE_STATUS;
			}
			elseif($cms_status=="suspend")
			{
				$cms_status = self::CMS_SUSPEND_STATUS;
			}
			else if($cms_status="inactive")
			{
				$cms_status = self::CMS_INACTIVE_STATUS;
			}
			if($cms_nickname=="")//Check the nickname from CMS
			{
				$cms_nickname ="";
			}
			//Insert fields in the tbl_admin
			$insert_fields="$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag],$glb_admin_column[ad_added],$glb_admin_column[cms_account_id]";
			$insert_values =" '".$cms_ad_email."','".md5($new_pwd)."','".$cms_status."',now(),'".$cms_account_id."'";
			//Insert values in the tbl_admin
			$insert_values =" '".$cms_ad_email."','".md5($new_pwd)."','".$cms_status."',now(),'".$cms_account_id."'";
			$debug = array('file'=>'admin.class.php', 'line'=>'insert_admin');
			$insert_cms=self::GetAdminInsert("$glb_tbl_admin",$insert_fields,$insert_values,$debug);/*insert_admin*/
			if($insert_cms)
			{
				//Get the last insert id
				$debug = array('file'=>'admin.class.php', 'line'=>'select_max_id');
				$last_ins_id = self::GetAdminSelect("$glb_tbl_admin","max($glb_admin_column[ad_id]) as lastid",'1',$debug);/*select_max_id*/
				$lst_id= $last_ins_id[0]['lastid'];
				if($lst_id!="")
				{	
					//Insert the values in tbl_admin_profile
					$ins_profile_flds= "$glb_admin_profile_column[ad_id],$glb_admin_profile_column[ap_fullname],$glb_admin_profile_column[ap_nickname],$glb_admin_profile_column[ap_display_name]";
					$ins_pro_values= "'".$lst_id."','".$cms_nickname."','".$cms_nickname."','".$cms_nickname."'";
					$debug = array('file'=>'admin.class.php', 'line'=>'ins_admin_profile');
					$ins_cms_pro=self::GetAdminInsert("$glb_tbl_admin_profile",$ins_profile_flds,$ins_pro_values,$debug);/*ins_admin_profile*/
					if(!$ins_cms_pro)
					{
						return "DB INSERT ERROR";
					}				
				}
				else
				{
					return "DB SELECT ERROR";
				}
			}
			else
			{
				return "DB INSERT ERROR";	
			}
			return "DB SUCCESS";
		}
		return "DB SUCCESS";
	}
	// ---------------------------------------------------------------------------------------------------------------
	// function: UpdateP1Admin ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose: Update the admin details when forgetting the password of Admin
	// arguments: $cms_account_id,$cms_ad_email,$findline
	// ---------------------------------------------------------------------------------------------------------------
	function UpdateP1Admin($cms_account_id,$cms_ad_email,$new_pwd,$findline="")
	{
		global $glb_tbl_admin,$glb_admin_column,$glb_tbl_admin_profile,$glb_admin_profile_column;
		$arguments= array($cms_account_id,$cms_ad_email);
		//Debugging UpdateP1Admin
		$this->debug_obj->WriteDebug($class="admin.class", $function="UpdateP1Admin", $findline['file'], $this->debug_obj->FindFunctionCalledline('UpdateP1Admin', $findline['file'], $findline['line']), $arguments);
		//Select from P1 Database
		$debug = array('file'=>'admin.class.php', 'line'=>'select_p1_admin_2');
		$admin_info = self::GetAdminSelect("$glb_tbl_admin","$glb_admin_column[ad_id],$glb_admin_column[ad_email],$glb_admin_column[ad_pass],$glb_admin_column[ad_stflag]","$glb_admin_column[ad_email]='$cms_ad_email' and $glb_admin_column[cms_account_id]='$cms_account_id'",$debug);/*select_p1_admin_2*/
		if($admin_info)
		{
			$admin_id = $admin_info[0]["$glb_admin_column[ad_id]"];
			$admin_email = $admin_info[0]["$glb_admin_column[ad_email]"];//Username
			$admin_status = $admin_info[0]["$glb_admin_column[ad_stflag]"];
			$update_fields="$glb_admin_column[ad_pass] = '".md5($new_pwd)."',$glb_admin_column[ad_updated] = now()";
		}
		else
		{
			return "DB SELECT ERROR";
		}
		$debug = array('file'=>'admin.class.php', 'line'=>'update_p1_admin_1');
		$get_admin_update = self::GetAdminUpdate("$glb_tbl_admin",$update_fields,"$glb_admin_column[ad_email]='$admin_email'",$debug);/*update_p1_admin_1*/
		if($get_admin_update)
		{
			//Retrive the Admin profile information
			$debug = array('file'=>'admin.class.php', 'line'=>'select_p1_admin_profile_2');
			$admin_profile_info=self::GetAdminSelect("$glb_tbl_admin_profile","$glb_admin_profile_column[ap_id],$glb_admin_profile_column[ad_id],$glb_admin_profile_column[ap_fullname],$glb_admin_profile_column[ap_nickname],$glb_admin_profile_column[ap_display_name]","$glb_admin_profile_column[ad_id]='$admin_id'",$debug);/*select_p1_admin_profile_2*/
			if($admin_profile_info)
			{
				$adm_pro_id = $admin_profile_info[0]["$glb_admin_profile_column[ap_id]"];
				$adm_pro_full_name = $admin_profile_info[0]["$glb_admin_profile_column[ap_fullname]"];
				$adm_pro_nickname = $admin_profile_info[0]["$glb_admin_profile_column[ap_nickname]"];
				$adm_pro_display_name = $admin_profile_info[0]["$glb_admin_profile_column[ap_display_name]"];
			}
			else
			{
				return "DB SELECT ERROR";
			}
		}
		else
		{
			return "DB UPDATE ERROR";
		}
		$p1_admin=array_merge($admin_info[0],$admin_profile_info[0]);
		return $p1_admin;
	}
}
?>
