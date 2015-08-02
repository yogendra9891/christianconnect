<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';
require_once('components/com_users/helpers/users.php');
/**
 * Profile controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerProfile extends UsersController
{
	/*
	 * uplaod a profile image by ajax..
	 */
	public function uploadprofileimage()
	{		
		$fieldname = JRequest::getVar('fname', '', 'GET', 'string');
		$file = JRequest::getVar( $fieldname, '', 'files', 'array' );
		$response = UsersHelper::ajaxUpload($file);
		echo json_encode($response); 
		return;
	}
		/*
	 * Function for the autocomplete for the religion.......
	 * 
	 */
	public function religion()
	{   
		$ch = JRequest::getVar('ch'); 
		$model = $this->getModel('Profile', 'UsersModel');
		$result = $model->getReligion($ch);
		
		$finalresult = array(); $j=0;
		 	
		 	if(!empty($result)) {
			 	foreach($result as $i =>$final) {	 
			 		   $finalresult[$j] = $final->name."<p style='display:none;'>".$final->id.'</p>';
			 		   $j++;
			 	}
		 	} 	
		 	$result1['test'] = $finalresult;
            echo json_encode($result1);   
		
	}
/*
 * Method for finding the localcurch on a ajax call from edit profile page(for Autosuggestion).. 
 */	
	public function localchurch()
	{
		$church = JRequest::getVar('church'); 
		$model = $this->getModel('Profile', 'UsersModel');
		$churchresult = $model->getLocalChurch($church);
		
		$finalresult = array(); $j=0;
		 	
		 	if(!empty($churchresult)) {
			 	foreach($churchresult as $i =>$final) {	 
			 		   $finalresult[$j] = $final->cname."<p style='display:none;'>".$final->id.'</p>';
			 		   $j++;
			 	}
		 	} 	
		 	$result1['test'] = $finalresult;
            echo json_encode($result1);   
		
	}
}
