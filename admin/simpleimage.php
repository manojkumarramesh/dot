<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/

class SimpleImage {

    var $image;
    var $image_type;
    var $new_image;
    var $newwidth;
    var $newheight;

    function load($filename) {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type, $compression=100, $permissions=null) {
        if( $image_type == '.jpg' or $image_type == '.jpeg'  ) {
            imagejpeg($this->image,$filename,$compression);
            imagedestroy($this->image);
            if($this->image != $this->new_image) {
                imagedestroy($this->new_image);
            }
        } elseif( $image_type == '.gif' || $image_type == '.png') {

	   // imagegif($this->image,$filename);
 imagepng($this->image,$filename);
            imagedestroy($this->image);
            if($this->image != $this->new_image) {
                imagedestroy($this->new_image);
            }
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }

   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }

   function getWidth() {
      return imagesx($this->image);
   }

   function getHeight() {
      return imagesy($this->image);
   }

   /*function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }*/

    /* resize Image height relative to its width*/
    function resizeToHeight($newwidth) {
        $size=array();
        $newheight = round( ( $this->getHeight() /  $this->getWidth() ) * $newwidth);
        $size['width']=$newwidth;
        $size['height']=$newheight;
        if($newheight>"70") {
            $newwidth = ($newwidth-5);
            return $this->resizeToHeight($newwidth);
        }
        return $size;
    }
   /* resize the Image height relative to its width*/

   /*function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }*/

    /* resize Image width relative to its height*/
    function resizeToWidth($newheight) {
        $size=array();
        $newwidth = round(($this->getWidth()/$this->getHeight())*$newheight);
        $size['width']=$newwidth;
        $size['height']=$newheight;	
        if($newwidth>"184") {
            $newheight = ($newheight-5);
            return $this->resizeToWidth($newheight);
        }
        return $size;
    }
    /* resize Image width relative to its height*/

   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }

   function resize() {
    /*
        if($this->getWidth()>"140"){
            $this->newwidth = "100";
            $this->newheight = $this->resizeToHeight($this->newwidth);
        }
        else
        {
            $this->newwidth = $this->getWidth();
            $this->newheight = $this->getHeight();
        }
    */
    $width = $this->getWidth();
    $height = $this->getheight();
    $diff = $width - $height;
    if($diff>=0) // execute if width greater than height
    {
        $this->newwidth = "184";
        $size = $this->resizeToHeight($this->newwidth);
        $this->newwidth =$size['width'] ;
        $this->newheight=$size['height'] ;
    }
    elseif($diff<0) // execute if height greater than width
    {
        $this->newheight = "70";
        $size = $this->resizeToWidth($this->newheight);
        $this->newwidth =$size['width'] ;
        $this->newheight=$size['height'] ;
    }
    else {
        $this->newwidth = $this->getWidth();
        $this->newheight = $this->getheight();
    }
    //$new_image = imagecreatetruecolor($width, $height);
    $this->new_image = imagecreatetruecolor($this->newwidth, $this->newheight);

    	imagealphablending($this->new_image, true);
	$transparent = imagecolorallocatealpha($this->new_image, 0, 0, 0, 127 );
	imagefill( $this->new_image, 0, 0, $transparent );

	//imagecolortransparent($this->new_image, imagecolorallocate($this->new_image, 0, 0, 0));
    	imagecopyresampled($this->new_image, $this->image, 0, 0, 0, 0, $this->newwidth, $this->newheight, $this->getWidth(), $this->getHeight());
	imagealphablending($this->new_image, false);
	imagesavealpha($this->new_image,true);
	
    $this->image = $this->new_image;
   }

}

?>