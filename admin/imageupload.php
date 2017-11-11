<html>
<head>
<style type="text/css">
* { margin:0; padding:0; }
.logo-display { display: table-cell; text-align: center; vertical-align: middle; height:81px; width:344px; padding:0; margin:0; }
.logo-display * { vertical-align: middle; }
.error {background:#FBE3E4;color:#8a1f11;height:81px; width:344px; vertical-align:middle; line-height:81px; text-align:center; font-size:12px;font-family:"Helvetica Neue"}
/* IE/Mac \*//*/
* html .logo-display { display: block; line-height: 0.6;}
* html .logo-display span { display: inline-block; height: 100%; width: 1px;}
/**/
</style>
<!--[if lt IE 8]>
<style>
.logo-display span {
    display: inline-block;
    height: 100%;
}
</style>
<![endif]-->
</head>
<body>

<?php

@session_start();
error_reporting(1);
$dirName="../data/images/original/";
include('../includes/functions/simpleimage.php');

//Constant Definition
define("UPLOAD_MAX_FILE_SIZE", 2);
define("UPLOAD_INI_SIZE", 1);
define("UPLOADED_FILE_SIZE", 2097152);
define("UPLOADED_SUCCESSFULLY", 1);

if($_REQUEST['logoid'] != "")
{
	$imgpath="../data/images/thumb/".$_REQUEST['logoid'];
        echo "<div class='logo-display'><span></span><img src='$imgpath'></div>";
}


//if image uploaded
if (isset($_POST['id'])) 
{
    //To check file errors
	if($_FILES["$_POST[id]"]['error']>0)
	{
	        $_SESSION['image_nameTMPadm']="";		
		if($_FILES["$_POST[id]"]['error'] == UPLOAD_MAX_FILE_SIZE || $_FILES["$_POST[id]"]['error'] == UPLOAD_INI_SIZE)
		{
		    echo '<div class="error">Please upload up to 2MB of file size.</div>';
		}	
	}
    //to check uploaded file size
	elseif($_FILES["$_POST[id]"]['size'] < UPLOADED_FILE_SIZE)
	{
		$uploadFile="$dirName/".md5($_FILES[$_POST['id']]['name'].".demo");	
		$ext = strtolower(strrchr($_FILES[$_POST['id']]['name'],'.'));
                  //to check the image formats
		if($ext == '.jpg' or $ext == '.JPEG' or $ext == '.JPG' or $ext == '.jpeg' or $ext == '.gif' or $ext == '.png' or $ext == '.GIF' or $ext == '.PNG')
		{	
			$image_name=time().$ext;
			$_SESSION['image_nameTMPadm']=$image_name;
			$target_path = $dirName.$image_name;
			$result = 0;
                       //image upload function
			if(move_uploaded_file($_FILES[$_POST['id']]['tmp_name'], $target_path)) 
            		{
				$result = UPLOADED_SUCCESSFULLY;
            		}
			$image = new SimpleImage();
			$image->load($target_path);
			$image->resize();	
			$image->save("../data/images/thumb/".$image_name,$ext);
			$imgpath="../data/images/thumb/".$image_name;
			echo "<div class='logo-display'><span></span><img src='$imgpath'></div>";
		} 
		else 
		{
			$_SESSION['image_nameTMPadm']="";
			echo '<div class="error">Select valid file format (JPG, PNG, GIF).</div>';
		}	
	}
	else
	{
		$_SESSION['image_nameTMPadm']="";
		echo '<div class="error">Please upload up to 2MB of file size.</div>';
	}
}

?>
</body>
</html>