<?php
/**
 * @version     1.0.0
 * @package     com_users
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */
define('IMG_UPLOAD_DIR_PATH', 'components/com_users/images/profileimage/original/');
define('IMG_THUMB_UPLOAD_DIR_PATH', 'components/com_users/images/profileimage/thumbs/');
abstract class UsersHelper
{

    /*
     * function for uplaoding a profile picture.....
     */

	public static function ImageUpload($file){ 
		$response = array();
		$response['error'] = false;
		$response['msg'] = '';
		$response['src'] = '';
		// Make the file name safe.
		jimport('joomla.filesystem.file');
		$user = JFactory::getUser();
		$filename = $file['name'] = time().self::clean(JFile::makeSafe(strtolower($file['name'])));

		// Move the uploaded file into a permanent location.
		if (isset( $file['name'] )) {
			// Make sure that the full file path is safe.
			$filepath = JPath::clean( IMG_UPLOAD_DIR_PATH.strtolower( $file['name'] ) );
			// Move the uploaded file.
			if(JFile::upload( $file['tmp_name'], $filepath )){
			 // if orignal file uploaded then create thumb..
			 	UsersHelper::createThumbnail($filename);
			}else{
				$response['error'] = true;
				$response['msg'] = JText::_('COM_USERIMAGE_UPLOAD_FAILED');
			}
		}
		return $file['name'];
	}

	/*
	 * creating a thumb of the profile image... 
	 * reading the file from orignal folder and create its thumbs..
	 */
	public static function createThumbnail($filename) {   
        $path_to_thumbs_directory = IMG_THUMB_UPLOAD_DIR_PATH; 
    	$path_to_image_directory =  JURI::root().IMG_UPLOAD_DIR_PATH; 
    	$final_width_of_image = 100;  
   
        if(preg_match('/[.](jpg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](jpeg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](gif)$/', $filename)) {  
            $im = imagecreatefromgif($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](png)$/', $filename)) {  
            $im = imagecreatefrompng($path_to_image_directory . $filename);  
        }  
        $ox = imagesx($im);  
        $oy = imagesy($im);  
        $ratio = $ox / $oy;
		$nx = $ny = min($final_width_of_image, max($ox, $oy));
		if ($ratio < 1) {
 		   $nx = $ny * $ratio;
		} else {
           $ny = $nx / $ratio;
		}
		
        $nm = imagecreatetruecolor($nx, $ny);  
        imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);  
        if(!file_exists($path_to_thumbs_directory)) {  
          if(!mkdir($path_to_thumbs_directory)) {  
               die("There was a problem. Please try again!");  
          }  
           }
        imagejpeg($nm, $path_to_thumbs_directory . $filename);   
		return true;
	}  
	
	/**
	 * Method to remove space from the string
	 * @param unknown_type $string
	 */
	public function clean($string){
		return JString::str_ireplace(' ', '', $string);
	}
	/*
	 * Uplaod image for church...
	 */
	public static function ChurchImageUpload($imagefile)
	{  
		$response = array();
		$response['error'] = false;
		$response['msg'] = '';
		$response['src'] = '';
		// Make the file name safe.
		jimport('joomla.filesystem.file');
		$user = JFactory::getUser();
		$app = &JFactory::getApplication();
		$config=JFactory::getConfig();
		$filename = $imagefile['name'] = time().self::clean(JFile::makeSafe(strtolower($imagefile['name'])));

		// Move the uploaded file into a permanent location.
		if (isset( $imagefile['name'] )) {
			// Make sure that the full file path is safe.
			$filepath = JPath::clean( JPATH_ROOT.$config->getValue('config.church_imagepath_original').strtolower( $imagefile['name'] ) ); 
			// Move the uploaded file.
			if(JFile::upload( $imagefile['tmp_name'], $filepath )){
			 // if orignal file uploaded then create thumb..
			 	UsersHelper::createChurchThumbnail($filename);
			}else{
				$response['error'] = true;
				$response['msg'] = JText::_('OM_USERIMAGE_UPLOAD_FAILED');
			}
		}
		return $imagefile['name'];	
	}
	/*
	 * creating a thumb of the church image... 
	 * reading the file from original folder and create its thumbs..
	 */
	public static function createChurchThumbnail($filename) {   
		$app = &JFactory::getApplication();
		$config=JFactory::getConfig();
		
        $path_to_thumbs_directory = JPATH_SITE .$config->getValue('config.church_imagepath_thumbs');; 
    	$path_to_image_directory =  JPATH_ROOT.$config->getValue('config.church_imagepath_original'); 
    	$final_width_of_image = 150;  
   
        if(preg_match('/[.](jpg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](jpeg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](gif)$/', $filename)) {  
            $im = imagecreatefromgif($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](png)$/', $filename)) {  
            $im = imagecreatefrompng($path_to_image_directory . $filename);  
        }  
        $ox = imagesx($im);  
        $oy = imagesy($im);  
        $ratio = $ox / $oy;
		$nx = $ny = min($final_width_of_image, max($ox, $oy));
		if ($ratio < 1) {
 		   $nx = $ny * $ratio;
		} else {
           $ny = $nx / $ratio;
		}
        $nm = imagecreatetruecolor($nx, $ny);  
        imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);  
        if(!file_exists($path_to_thumbs_directory)) {  
          if(!mkdir($path_to_thumbs_directory)) {  
               die("There was a problem. Please try again!");  
          }  
           }
        imagejpeg($nm, $path_to_thumbs_directory . $filename);   
		return true;
	}  
	
	
}
