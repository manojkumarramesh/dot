<?php
ob_start();
//-------------------------------------------------------------------------------------------------------------------
// File name   : Validator.class.php
// Description : Handles validation information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 23-02-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
class Validator 
{
	//--------------------------------------------------------------------------------------------------------
	// function: ValidLogin ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: Validate login credentials
 	// Parameters: $name
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Admin Login, Merchant Login to be used
 	//-------------------------------------------------------------------------------------------------------
	function ValidLogin($name)
	{
		if($name =="")
		{
			$errmsg="Enter valid Email/Password.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidEmail ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: Validate email address to check the consistency of email format (Merchant' with CMS)
 	// Parameters: $email
 	// Returns/Assigns: empty string on success (or) error message on error
 	// Modules Used: Admin/CSR/Merchant Forgot Password,CSR Login
	//-------------------------------------------------------------------------------------------------------	
	function ValidEmail($email,$flag="e-mail address")
	{
		if($email=="")
		{
			$errmsg="Enter ".$flag.".";
		}
		elseif(!preg_match('|^([a-zA-Z0-9\-\_\.]+)(\.[a-zA-Z0-9\-\_\.]+)?(\@)([a-zA-Z0-9\_]+)(\.[a-zA-Z0-9\_]+)?(\.)([a-zA-Z]{2,4})$|', $email))
		{
			$errmsg="Enter valid ".$flag.".";
		}
		else if(strlen($email)>200)
		{
			$errmsg = ucfirst($flag)." limit is 200 characters.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
		
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidCsrEmail ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: Validate email address to check the consistency of email format for CSR
 	// Parameters: $email
 	// Returns/Assigns:	empty string on success (or) error message on error
 	// Modules Used: CSR
	//-------------------------------------------------------------------------------------------------------	
	function ValidCsrEmail($email)
	{
		
		if($email=="")
		{
			$errmsg="Enter e-mail address.";
		}
		elseif(!preg_match('|^([a-zA-Z0-9\-\_\.]+)(\.[a-zA-Z0-9\-\_\.]+)?(\@)([a-zA-Z0-9\_]+)(\.[a-zA-Z0-9\_]+)?(\.)([a-zA-Z]{2,4})$|', $email))
		{
			$errmsg = "Enter valid e-mail address.";
		}
		else if(strlen($email)>200)
		{
			$errmsg="Email address does not exceed 200 characters.";
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
		
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ChkInput ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
	// Purpose: To check the length of Input parameters 
	// Parameters: $name,$fld
	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Services, CSR ,Merchant, Forgot Password
	//-------------------------------------------------------------------------------------------------------	 
	function ChkInput($name,$fld)
	{
		if($name=="")
		{
			$errmsg="Enter $fld.";
		}
		else if($fld=="city name" || $fld=='Category Name')
		{
			if(strlen($name)>60)
			{
				$errmsg = ucfirst($fld)." limit is maximum 60 characters.";
			}
		}
		else if($fld=='service name')
		{
	            if(strlen($name)>50)
		    {
		        $errmsg = ucfirst($fld)." limit is maximum 50 characters.";
	            }
		     else if(!preg_match("/^[A-Za-z0-9\-\‘\’\ \'\#\:\”\“\,\&\*\<\>\`\"\.\_\!\@\#\$\%\^\(\)\/ ]+$/",$name))
		    {
			$errmsg = "Enter valid service name";
		    }
		}
		else if($fld=='own question')
		{
			if(strlen($name)>200)
			{
				$errmsg = ucfirst($fld)." limit is maximum 200 characters.";
			}
		}
		else if($fld=='answer')
		{
			if(strlen($name)>20)
			{
				$errmsg = ucfirst($fld)." limit is maximum 20 characters.";
			}
			
		}
		else
		{	
			$errmsg ="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidPwd ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check the length,empty input or not and spaces of password parameters 
 	// Parameters: $pwd, $frm
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR,Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidPwd($pwd,$frm)
	{
		if($pwd=="")
		{
			if($frm!="profile")
			{
				$errmsg="Enter password.";
			}
			else
			{
				$errmsg="Enter new password.";
			}
		}
		else if(!preg_match("/^[A-Za-z0-9\~\!\@\#\$\%\^\&\*\(\)\+\=\{\}\[\]\"\|\-\_\.\,\:\;\\\'\‘\?\<\>\’\/\` ]+$/", $pwd))
		{
			$errmsg = "Enter valid Password.";
		}
		else if($frm=='regn' || $frm=='profile')
		{
		 	if(strlen($pwd)>60 || strlen($pwd)<6)
			{
				$errmsg="Enter 6 to 60 characters for password.";
			}
			else if($pwd=="XXXXXX" || $pwd=="xxxxxx")
			{
				$errmsg = "Enter valid Password.";
			}
			else
			{
				$errmsg="";		
			}
		
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: chkAddress ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
	// Purpose: To check the length of Input parameters 
	// Parameters: $value,$fldname
	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR,Merchant
	//-------------------------------------------------------------------------------------------------------	 
	function chkAddress($value,$fldname,$co_code="")
	{
		if($value=="") 
		{
			$errmsg = "Enter ".$fldname." message.";
		}
		else if($value!="")
		{
			if($co_code=="US" || $co_code=="")
			{
				if(!preg_match("/^[A-Za-z0-9\!\@\#\$\%\^\(\)\-\_\.\,\:\;\'\‘\’\/\` ]+$/", $value)) 
				{
					$errmsg = "Enter valid ".$fldname." message.";
				}
			}
		}
        	else if($fldname=="Trans.Type - Authorization" || $fldname=="Trans.Type - Billing" || $fldname=="Trans.Type - Opt In" || $fldname=="Trans.Type - IVS 3.2" || $fldname == "Trans.Type - mBlox")
        	{
			if(strlen($value)>400)
			{
				$errmsg ="Maximum ".$fldname." message length is 400 characters.";
			}
        	}
		elseif(strlen($value)>60)
		{
			$errmsg ="Maximum ".$fldname." length is 60 characters.";
		}	
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: chkUserDetails ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
	// Purpose: To check the length of Input parameters 
	// Parameters: $value,$fldname
	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR
	//-------------------------------------------------------------------------------------------------------	 
	function chkUserDetails($value,$fldname)
	{
		if($value=="") 
			{
			  $errmsg = "Enter $fldname.";
			}
		elseif(strlen($fldname)>60)
			{
				$errmsg ="Maximum ".$fldname." length is 60 characters.";
			}

	
		elseif(!preg_match("/^[A-Za-z0-9\#\-\_\.\'\‘\’\,\/ ]+$/", $value))
                	{
                          $errmsg = "Enter valid $fldname.";
                	}
		else
			{
			$errmsg = "";
			}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: CheckPassword ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check consistency between new and confirm password field 
 	// Parameters: $pwd, $cpwd, $frm
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR, Merchant
 	//-------------------------------------------------------------------------------------------------------
	function CheckPassword($pwd, $cpwd,$frm="")
	{
		if($cpwd=="")
		{
			if($frm=="profile")
			{
				$errmsg = "Enter verify new password.";
			}
			else
			{
				$errmsg = "Enter confirm password.";
			}	
		
		}
		else if($pwd!=$cpwd)
		{
			if($frm=="profile")
			{
				$errmsg = "Your new password and verify new password should be same.";
			}
			else
			{
				$errmsg = "Your password and confirm password should be same.";
			}	
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidAlpha ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check length and valid characters of input parameter
 	// Parameters: $name, $fldname
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR, Service Categories,Merchant, Services
 	//-------------------------------------------------------------------------------------------------------
	function ValidAlpha($name,$fldname)
	{
		if($name=="")
		{
			$errmsg = "Enter $fldname.";	
		}
		else if(!preg_match("/^[A-Za-z]+$/", $name))
		{
			$errmsg = "Enter valid $fldname.";	
		}
		else if(strlen($name)>60)
		{
			$errmsg = "".ucfirst($fldname)." limit is maximum 60 characters.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidMobile ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check whether mobile number comply with the standard format and length
 	// Parameters: $number
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR,Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidMobile($number)
	{		
		preg_match_all('/[0-9]/', $number, $matches);
		$count = count($matches[0]);
		if($number=="")
		{
			$errmsg = "Enter phone number.";
		}
		else if(!preg_match("/^[0-9\(\)\+\- ]+$/", $number))
		{
			$errmsg = "Only numbers(0-9), plus, minus, paranthesis and spaces are allowed for phone number.";
		}
		else if(strlen($number)> 20 || strlen($number)<10)
		{
			$errmsg = "Phone number limit is 10 to 20 digits.";
		}
		else if($count<10)
		{
			$errmsg = "There must be minimum 10 numbers in phone number.";
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidPosition ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check lenght of input parameter and having valid aplhanumeric or not  
 	// Parameters: $position
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidPosition($position,$page="")
	{	
		if(!preg_match("/^[A-Za-z0-9\'\. ]+$/", $position) && $page!="merchant")
		{
			$errmsg = "Job title should contain alphabets or numbers.";
		}
		else if(strlen($position)>60)
		{
			$errmsg = "Maximum Job title limit is 60 characters.";
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidWebsite ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check website parameter comply with standard format or not  
 	// Parameters: $website
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Merchant
 	//-------------------------------------------------------------------------------------------------------
    function ValidWebsite($website, $flag="Website URL")
	{	
		if($website == "")
		{
			$errmsg="Enter ".$flag.".";
		}
		if(preg_match('|^(http(s)?://)?(www\.)?([a-z0-9-]+\.)?([a-z0-9-]+)?(\.)([a-z]{2,4})(\/)?(.*)?$|i', $website))
		{
				$errmsg="";
		}
		elseif(preg_match('|^(ftp)://([a-z0-9\-\_]+)?(@)?([a-z0-9]+\.)?([a-z0-9]+)?(\.)?([a-z]{2,4})(\/)?$|i', $website))
		{
				$errmsg="";
		}
		elseif(preg_match('|^(ftp)://([a-z0-9_]+)?(@)?(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$|i', $website))
		{
				$errmsg="";
		}
		elseif(preg_match('|^(http(s)?://)?(www\.)?([a-z0-9\_]+):(\d{2,4})/([a-z0-9\_]+)(\/)?(.*)?$|i', $website))
		{
				$errmsg="";
		}
		elseif(preg_match("|^(http(s)?://)?(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/([a-z0-9\_]+)(\/)?(.*)?$|i",$website))
		{
			$errmsg = "";
		}
		else
		{
				$errmsg = "Enter a valid ".$flag.".";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidAddress ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check the length of address   
 	// Parameters: $address, $fldname, $flag
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR
 	//-------------------------------------------------------------------------------------------------------
	function ValidAddress($address, $fldname, $flag)
	{
		if($address=="" && $flag ==1)
		{
			$errmsg ="Enter $fldname.";
		}
		else if($fldname!="company name")
		{
			if(strlen($address)>100)
			{
				$errmsg ="Maximum ".$fldname." length is 100 characters.";
			}
		}
		else if($fldname=="company name")
		{
			if(strlen($address)>50)
			{
				$errmsg ="Maximum ".$fldname." length is 50 characters.";
			}
			
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidCity ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check the length of address and having valid characters    
 	// Parameters: $city
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR
 	//-------------------------------------------------------------------------------------------------------
	function ValidCity($city)
	{

		if($city=="")
		{
			$errmsg = "Enter city name.";	
		}
		else if(!preg_match("#^[-A-Za-z' ]*$#",$city))
		{
			$errmsg = "Enter valid city.";	
		}
		else if(strlen($name)>100)
		{
			$errmsg = "Maximum city name limit is maximum 100 characters.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function:  ValidState ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose:		To check the length of city and having valid characters    
 	// Parameters: 		$state
 	// Returns/Assigns:	empty string on success (or) error message on error
	// Modules Used: CSR, Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidState($state)
	{

		if($state=="")
		{
			$errmsg = "Enter state/province.";	
		}
		else if(!preg_match("#^[-A-Za-z' ]*$#",$state))
		{
			$errmsg = "Enter valid state/province.";	
		}
		else if(strlen($name)>100)
		{
			$errmsg = "Maximum limit is maximum 100 characters.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidZipcode ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To check the length of zipcode and having valid characters 
 	// Parameters: $zipcode
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR, Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidZipcode($zipcode,$flag="zip code")
	{	
		if($zipcode=="")
		{
			$errmsg ="Enter ".$flag.".";
		}
		else if(!preg_match("#^[0-9]+$#",$zipcode))
		{
			$errmsg = ucfirst($flag)." should be number.";
		}
		else if(!preg_match("#^[0-9]{5}$#",$zipcode))
		{
			$errmsg = ucfirst($flag)." should be in 5 digits.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;			
	}
	//--------------------------------------------------------------------------------------------------------
	// function:  ValidTrms ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose:		To check whether the terms and conditions checkbox is checked ot not
 	// Parameters: 		$value
 	// Returns/Assigns:	empty string on success (or) error message on error
	// Modules Used: Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidTrms($value)
	{
		if($value !="1")
		{	
			$errmsg="Accept terms and conditions.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function:  ValidTollNo ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose:		To ensure number parameter is numeric, doesn't blank and should not exceed 10 characters
 	// Parameters: 		$number
 	// Returns/Assigns:	empty string on success (or) error message on error
	// Modules Used: Merchant
 	//-------------------------------------------------------------------------------------------------------
	function ValidTollNo($number,$flag="toll free number")
	{
		if($number == "")
		{
			$errmsg = "Enter ".$flag.".";
		}
		else if(!preg_match("/^[A-Za-z0-9\.\(\)\-\*\+\ ]+$/", $number))
		{
			$errmsg = "Enter valid Toll free number.";
		}
		else if(strlen($number)>50)
		{
			$errmsg = "Toll free number maximum limit is 50.";
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function:  ValidHomePhoneNo ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To ensure number parameter should not blank and comply with american home phone no standards    
 	// Parameters: $number
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR
 	//-------------------------------------------------------------------------------------------------------
	function ValidHomePhoneNo($number)
	{
		if($number=="")
		{
			$errmsg = "Enter home phone number";
		}
		elseif(!preg_match("#^[0-9-\(\)\+ ]+$#",$number))
		{
			$errmsg = "Phone number allows only - + () and space";	
		}
		elseif(!preg_match("#^\+?\(?([0-9]{3})\)?[- ]?([0-9]{3})[- ]?([0-9]{4})$#",$number))
		{
			$errmsg = "The phone number should be 10 digits.";	
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: Dob ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To ensure date of birth parameter should be in ddmmyy format
 	// Parameters: $dob
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: CSR
 	//-------------------------------------------------------------------------------------------------------
	function Dob($dob)
	{
		if($dob=="")
		{
			$errmsg = "DoB should not be blank";
		}
		elseif(!preg_match("#^(\d{2})-(\d{2})-(\d{4})$#",$dob))
		{
			$errmsg = "Enter valid DoB.";	
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: escape ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To escape the string
 	// Parameters: $str
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Admin Login
 	//---------------------------------------------------------------------------------------------------------
	function escape($str)
   	{
		$value = addcslashes($str,"!@#$%^&()- _.'/");
		return $value;
        }
	//--------------------------------------------------------------------------------------------------------
	// function: ValidANStrict ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To ensure the Alphanumeric with strict special characters (According to PayConnect document)
 	// Parameters: $value,$fldname
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Merchant
 	//---------------------------------------------------------------------------------------------------------
	function ValidANStrict($value,$fldname,$page="")
	{
		if($value=="")
		{
			$errmsg = "Enter $fldname.";	
		}
		else if((preg_match('/<script\b[^>]*>(.*?)<\/script>/is', $value)) && ($page=="merchant"))
		{
		  	$errmsg = "Enter valid $fldname.";
		}
		else if(!preg_match("/^[A-Za-z0-9\-\'\#\.\,\_\‘\’ ]+$/", $value) && ($page!="merchant"))
		{
			$errmsg = "Enter valid $fldname.";	
		}
		else if($fldname == "city name" || $fldname== "support city name")
		{
			if(strlen($name)>30)
			{
				$errmsg = "".ucfirst($fldname)." limit is maximum 30 characters.";
			}
			else
			{
				$errmsg="";
			}
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidAlphaNumSpl ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To ensure the Alphanumeric with restricted special characters (According to PayConnect document)
 	// Parameters: $value,$fldname
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Category,CSR,Merchant,Service
 	//---------------------------------------------------------------------------------------------------------
	function ValidAlphaNumSpl($value,$fldname,$page="")
	{	
		if($value=="")
		{
		  	$errmsg="Enter $fldname.";
		}
		else if((preg_match('/<script\b[^>]*>(.*?)<\/script>/is', $value)) && ($page=="merchant"))
		{
		  	$errmsg = "Enter valid $fldname.";
		}
		else if((!preg_match("/^[A-Za-z0-9\-\'\#\.\_\!\@\$\%\^\(\)\‘\’\/ ]+$/", $value)) && ($page!="merchant"))
		{			
			$errmsg = "Enter valid $fldname.";
		}
		else if($fldname=='SMS company name' && strlen($value)>16)
		{
			$errmsg = "Maximum ".$fldname." length is 16 characters.";
		}
		else if(($fldname=='first name' || $fldname=='last name' ||  $fldname=='corp name' ||  $fldname=='service/product name' ||  $fldname=='Category Name') && (strlen($value)>60))
		{
			$errmsg = "Maximum ".$fldname." length is 60 characters.";
		}
		else if($fldname=="company name" && strlen($value)>50 )
		{
			$errmsg ="Maximum ".$fldname." length is 50 characters.";
		}
		else if($fldname=="API Username" && strlen($value)>20 )
		{
			$errmsg ="Maximum ".$fldname." length is 20 characters.";
		}
		else if($fldname=="API Password" && strlen($value)>128 )
		{
			$errmsg ="Maximum ".$fldname." length is 128 characters.";
		}
		else if($fldname=="secret phrase" && strlen($value)>32 )
		{
			$errmsg ="Maximum ".$fldname." length is 32 characters.";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	//--------------------------------------------------------------------------------------------------------
	// function: ValidNumeric ( -- parameters -- )
	//---------------------------------------------------------------------------------------------------------
 	// Purpose: To ensure the Numeric with restricted special characters
 	// Parameters: $value,$fldname
 	// Returns/Assigns: empty string on success (or) error message on error
	// Modules Used: Merchant
 	//----------------------------------------------------------------------------------------------------------
	function ValidNumeric($number,$fldname)
    {
        if($number=="")
        {
            $errmsg = "Enter $fldname.";
        }
        else if(!is_numeric($number))
        {
            $errmsg = ucfirst($fldname). " should contain only numbers.";
        }
        else if($fldname=="client id" && strlen($number)>4)
        {
            $errmsg = ucfirst($fldname)." limit is maximum 4 digits.";
        }
        else if($fldname == "Response Code" || $fldname=="Response Appl.Code (From)" || $fldname=="Response Appl.Code (To)")
        {
            if(strlen($number)<4)
            {
                $errmsg = ucfirst($fldname)." limit is 4 digits.";
            }
        }
        else if($fldname=="IVR toll free number" && strlen($number)!=10)
        {
            $errmsg = ucfirst($fldname)." limit is 10 digits.";
        }
        else
        {
            $errmsg="";
        }
        return $errmsg;
    }
	// ---------------------------------------------------------------------------------------------------------------
    // function: escape_string ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: escape_string to add slashes for special characters in input.
    // Parameters: $str
    // Returns:escaped string
	// Modules Used: Category, Merchant,Service, PayOne Button
    // ---------------------------------------------------------------------------------------------------------------
    function escape_string($str)
    {
        $search = array("\\","\0","\n","\r","\x1a","'",'"');
        $replace = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        return str_replace($search,$replace,$str);
    }
	// ---------------------------------------------------------------------------------------------------------------
    // function: ValidPhoneNumber( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: ValidPhoneNumber to check the phone number
    // Parameters: $number
	// Modules Used: CSR
    // ---------------------------------------------------------------------------------------------------------------
	function ValidPhoneNumber($number)
	{		
		
		if($number=="")
		{
			$errmsg = "Enter phone number.";
		}
		elseif(preg_match("#^[0]{1,}$#",$number))
		{
			$errmsg = "Phone number should not have all 0's";
		}	
		else if(!preg_match("#^[\+0-9\(\)\- ]+$#",$number))
		{
			$errmsg = "Only numbers (0-9), plus, minus, paranthesis and spaces are allowed in phone numbers.";
		}
		else if(!preg_match("#^(.){10,20}$#",$number))
		{
			$errmsg = "Phone number limit is 10 to 20 digits";
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	// ---------------------------------------------------------------------------------------------------------------
    // function: ValidPwdCsr( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: ValidPwdCsr to check the CSR password
    // Parameters: $pwd,$frm
	// Modules Used: CSR
    // ---------------------------------------------------------------------------------------------------------------
	function ValidPwdCsr($pwd,$frm)
	{
		if($pwd=="")
		{
			if($frm!="profile")
			{
				$errmsg="Enter password.";
			}
			else
			{
				$errmsg="Enter new password.";
			}
		}
		else if($frm=='regn' || $frm=='profile')
		{
			if(!preg_match("/^[A-Za-z0-9\-\'\#\.\,\_]+$/", $pwd))
			{
				$errmsg = "Please enter valid password.";
			}
		 	else if(strlen($pwd)>60 || strlen($pwd)<6)
			{
				$errmsg="Enter 6 to 60 characters for password.";
			}
		}
		else
		{
			$errmsg = "";
		}
		return $errmsg;
	}
	// ---------------------------------------------------------------------------------------------------------------
    // function: ValidStates( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: ValidStates to check the state valid or not
    // Parameters: $state, $flag
	// Modules Used: CSR,Merchant
    // ---------------------------------------------------------------------------------------------------------------
	function ValidStates($state, $flag="state")
	{
		if($state=="Select")
		{
			$errmsg= "Select ".$flag.".";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	// ---------------------------------------------------------------------------------------------------------------
    // function: ValidCountrys( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: ValidCountrys to check the country valid or not
    // Parameters: $country, $flag
	// Modules Used: CSR,Merchant
    // ---------------------------------------------------------------------------------------------------------------
	function ValidCountrys($country, $flag="country")
	{
		if($country=="Select")
		{
			$errmsg= "Select ".$flag.".";
		}
		else
		{
			$errmsg="";
		}
		return $errmsg;
	}
	// ---------------------------------------------------------------------------------------------------------------
    // function: StripSplChars( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: StripSplChars to strip the special character in a given string
    // Parameters: $string
	// Modules Used: Merchant
    // ---------------------------------------------------------------------------------------------------------------
	function StripSplChars($string)
	{
		$string = preg_replace("/[^A-Za-z0-9]+/","",$string);
		return $string;
	}
    // ---------------------------------------------------------------------------------------------------------------
    // function: TextAreaContent( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: TextAreaContent to remove the new lines from the given string
    // Parameters: $string
    // Modules Used: API Response Messages
    // ---------------------------------------------------------------------------------------------------------------
    function TextAreaContent($string)
    {
        $string = preg_replace("/\n/"," ",$string);
        return $string;
    }
}
?>