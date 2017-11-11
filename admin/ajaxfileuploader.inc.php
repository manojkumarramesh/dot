<?php 
@session_start();
class AjaxFileuploader {
	// PHP 4.x users replace "PRIVATE" from the following lines with "var". Also remove all the PUBLIC, PRIVATE and PROTECTED Kaywords from the class
	private $uploadDirectory='';
	private $uploaderIdArray=array();

	/**
	 * Constructor Function
	 * 
	 */
	public function AjaxFileuploader($uploadDirectory) {
		if (trim($uploadDirectory) != '' && is_dir($uploadDirectory)) {
			$this->uploadDirectory=trim($uploadDirectory);
		}
	}

	/**
	 * 
	 * This function return all the files in the upload directory, sorted according to their file types
	 *
	 * @return array
	 */		
	public function getAllUploadedFiles() {
		$returnArray = array();
		$allFiles = $this->scanUploadedDirectory();
		return $returnArray;
	}

	/**
	 * 
	 * This function scans uploaded directory and returns all the files in it
	 *  
	 * @return array
	 */
	private function scanUploadedDirectory() {
		$returnArray = array();
		if ($handle = opendir($this->uploadDirectory)) {
			while (false !== ($file = readdir($handle))) {
				if (is_file($this->uploadDirectory."/".$file)) {
					$returnArray[] = $file;
				}
			}
			closedir($handle);
		}
		else {
			die("<b>ERROR: </b> Could not read directory: ". $this->uploadDirectory);
		}
		return $returnArray;
	}

	/**
	 * This function returns html code for uploading a file
	 * 
	 * @param string $uploaderId
	 * 
	 * @return string
	 */
	public function showFileUploader($uploaderId,$Tslogo,$tabvalue) {
		if (in_array($uploaderId, $this->uploaderIdArray)) {
			die($uploaderId." already used. please choose another id.");
			return '';
		}
		else {
			$this->uploaderIdArray[] = $uploaderId;
			return '
                            <input type="hidden" name="id" value="'.$uploaderId.'" />
                            <span id="uploader'.$uploaderId.'" style="font-family:verdana;font-size:10;" class="btn-file-choose">
                                    <input name="'.$uploaderId.'" type="file" class="file" value="'.$uploaderId.'" onchange=\'return uploadFile(this,"'.$this->uploadDirectory.'")\' tabindex="'.$tabvalue.'" /></span>
                            <span id="loading'.$uploaderId.'"></span>
                            <iframe name="iframe" src="imageupload.php?logoid='.$Tslogo.'" height="100px" width="350" style="display:block" scrolling="no" frameborder="0" tabindex="'.$tabvalue.'" > </iframe>';
		}
	}


	public function displayFileUploader($uploaderId,$Tslogo,$tabvalue,$class) {
		if($class=='add')
		{
			$clas='btn-file-choose';
		}
		else{
			$clas='btn-file-change';
		}
		if (in_array($uploaderId, $this->uploaderIdArray)) {
			die($uploaderId." already used. please choose another id.");
			return '';
		}
		else {
			$this->uploaderIdArray[] = $uploaderId;
			return '
				<iframe class="logo-preview" name="iframe" src="imageupload.php?logoid='.$Tslogo.'"  scrolling="no" frameborder="0"> </iframe>
			    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
                            <input type="hidden" name="id" value="'.$uploaderId.'" />
                            <span id="uploader'.$uploaderId.'" style="font-family:verdana;font-size:10;" class="'.$clas.'">
                                    <input name="'.$uploaderId.'" type="file" class="file" value="'.$uploaderId.'" onchange=\'return uploadFile(this,"'.$this->uploadDirectory.'")\' tabindex="'.$tabvalue.'" /></span>
                            <span id="loading'.$uploaderId.'"></span>';
		}
	}

}

?>