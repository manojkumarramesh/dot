<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : debug.class.php
// Description : debug writing in debug.txt file
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 13-01-2011
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
class Debug{

private $label = array('file_1'=>'Triggered file::','file_2'=>'Function resides in::','line'=>'Triggered line::','function_name'=>'Function name::','args'=>'Arguments::', 'class'=>'Class name::','function'=>'Function','api_request'=>'Api Request', 'api_response'=>'Api Response');
private $function_trigged_file;


function WriteDebug($class="",$function="", $file="", $calledline="",$arguments="", $api_request="", $api_response="")
{
if($_SESSION['debug_session']== "start")
{
        $str = ""; $tmp=""; $temp="";
        if(isset($file))
        {
                $tmp .= $this->label["file_1"].basename($file).";\n";
        }
        if(isset($calledline))
        {
                $tmp .= $this->label["line"].$calledline.";\n";
        }
        if(isset($class) && $class!="")
        {
                $tmp .= $this->label["file_2"].$class.".php;\n";
        }
        else
        {
                $tmp .= $this->label["file_2"].$this->function_trigged_file.";\n";	
        }
        if(isset($class))
        {
                $tmp .= $this->label["class"].$class.";\n";
        }	
        if(isset($function))
        {
                $tmp .= $this->label["function_name"].$function.";\n";
        }
        
        if(isset($arguments) && is_array($arguments))
        {
                $tmp .= $this->label['function']." ".$this->label['args']."(";
                foreach ($arguments as $key=>$value)
                {
                        if(!is_array($value))
                        {	
                                if(ctype_alpha($key))
                                        $tmp .= "$key:$value,";
                                else
                                        $tmp .= "$value,";
                        }
                        else if(is_array($value))
                        {
                                $tmp .="$key=>(";	
                                foreach ($value as $_key=>$_value)
                                {
                                        
                                        if(!is_array($_value))
                                        {
                                                if(ctype_alpha($_key))
                                                        $tmp .= "$_key:$_value,";
                                                else	
                                                        $tmp .= "$_value,";
                                        }
                                        elseif(is_array($_value))
                                        {
                                                $tmp .="$_key=>(";
                                                foreach ($_value as $_kkey=>$_vvalue)
                                                {
                                                        if(!is_array($_vvalue))
                                                        {

                                                                if(ctype_alpha($_kkey))
                                                                        $tmp .= "$_kkey:$_vvalue,";
                                                                else	
                                                                        $tmp .= "$_vvalue,";
                                                        }
                                                        elseif(is_array($_vvalue))
                                                        {
                                                                $tmp .="$_kkey=>(";
                                                                foreach ($_vvalue as $_gkey=>$_gvalue)
                                                                {
                                                                        if(ctype_alpha($_gkey))
                                                                                $tmp .= "$_gkey:$_gvalue,";
                                                                        else	
                                                                                $tmp .= "$_gvalue,";
                                                                }
                                                                $tmp = rtrim($tmp,",");	
                                                                $tmp .=")";
                                                        }
                                                        $tmp = rtrim($tmp,",");	
                                                        $tmp .="),";
                                                }
                                        }
                                }
                                $tmp = rtrim($tmp,",");
                                $tmp .=")";
                        }
                }
                $tmp = rtrim($tmp,",");
                $tmp .= ");\n";
        }
        if(is_array($api_request))
        {
                $tmp .= $this->label['api_request']."(";
                foreach($api_request as $key=>$value)
                {
                        if(ctype_alpha($key))
                                $tmp .= "$key:$value,";
                        else
                                $tmp .= "$value,";
                }
                $tmp = rtrim($tmp,","); 
                $tmp .= ");\n";

        }
        if(is_array($api_response))
        {
                $tmp .= $this->label['api_response']."(";
                foreach($api_response as $key=>$value)
                {
                        if(ctype_alpha($key))
                                $tmp .= "$key:$value,";
                        else
                                $tmp .= "$value,";
                }
                $tmp = rtrim($tmp,","); 
                $tmp .= ");\n";
        }		
        $debug_string .= "$tmp\n----------------------------------------\n";
        //$_SESSION['debug'] .= $debug_string;
        $this->	WriteDebugFile($debug_string);
}
}
private function WriteDebugFile($string="")
{
        if($_SESSION['debug_session']== "start")
        {
                $debug_file =FULL_PATH."data/debug/debug.txt";
                $debug_file_handle = fopen($debug_file,'a');
                fwrite($debug_file_handle, $string);
                fclose($debug_file_handle);
        }
}


function FindFunctionCalledline($function="", $file="", $line="")
{
        $path = array('class_1'=>'includes/classes/','class_2'=>'../includes/classes/','admin'=>'../admin/','root'=>'');
        $requesturi = $_SERVER['REQUEST_URI'];
        $admin=0;
        if($_SESSION['debug_session']== "start")
        {
                $this->function_trigged_file = 	$file;
                $function_line="";
                $function_line ="<unknown>";
                if(isset($file))
                {
        
                        $file = basename($file);
                        if(stristr($requesturi, 'admin') && stristr($file, 'class'))
                        {
                                $file = $path['class_2'].$file;
                                $admin=1;
                        }
                        else if((!stristr($requesturi, 'admin')) && stristr($file, 'class'))
                        {
                                $file = $path['class_1'].$file;
                        }
                        
                        if(stristr($requesturi, 'admin'))
                        {
                                $admin=1;
                        }
                        if($admin)
                        {
                                $this->function_trigged_file = 'admin/'.$this->function_trigged_file;
                        }
                        else
                        {
                                $this->function_trigged_file = $this->function_trigged_file;
                        }
                        
        
                        $file = realpath($file);
                                
                }	
                if(isset($function))
                {
                        
                        $lines=file($file); 
                        foreach( $lines as $key=>$value)
                        {
                                $value = trim($value);
                                if($line!="")
                                {	
                                        if(preg_match("/$function\((.*)\);\/\*$line\*\//i", $value))
                                        {
                                                $function_line=($key+1);
                
                                        }
                                }
                                else if($line=="")
                                {
                                        if(preg_match("/$function\((.*)\);/i", $value))
                                        {
                                                $function_line=($key+1);
                
                                        }
                                }	
                        }
                
                }
        
                return $function_line;
        }
}

}
?>