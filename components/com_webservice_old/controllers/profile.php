<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceControllerProfile extends WebserviceController
{
	
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'profile', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/*
	 * function for updating the user profile..
	 */
	public function UpdateProfile(){
	  	$data = json_decode(JRequest::getVar('data'),true); 
	  	$data_json = JRequest::get('formdata'); //for getting the file(image) name..
		 //check for session
		$this->checkSession($data['sessionId']); 
	  	$filedata = array(); 
	  	if(!empty($_FILES['file']['name']))
	  	{
	  		$_FILES['file']['name'] = $data_json['name'];
			$filedata = $_FILES['file'];
	  		
	  	} 
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->UpdateProfile($data, $filedata); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_PROFILE_UPDATE_SUCCESS'),  JText::_('MSG_PROFILE_UPDATE_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_PROFILE_UPDATE_FAIELD'),  JText::_('MSG_PROFILE_UPDATE_FAIELD'),$return);
					exit;
	  		}
	  	}
	}
	/*
	 * function for doing logout a user..
	 */
	public function Logout(){
	  	$data = json_decode(JRequest::getVar('data'),true); 
		 //check for session
		$this->checkSession($data['sessionId']);
	  	if(!empty($data))
	  	{   
			$model = $this->getModel();
			$return = $model->Logout($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_LOGOUT_SUCCESS'),  JText::_('MSG_LOGOUT_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_LOGOUT_FAIELD'),  JText::_('MSG_LOGOUT_FAIELD'),$return);
					exit;
	  		}
	  	}
	}
	/*function to get my profile
	 * @param sessionid
	 
	 * @return profile object if successful
	 */
	function getMyProfile()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('profile.userId',$userId);
			$return=$model->getMyProfile(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETMYPROFILE_SUCCESS'),$return);
					exit;
			    }
		}
	 }

	/*function to getUserProfile
	 * @param sessionid
	 
	 * @return profile object if successful
	 */
	function getUserProfile()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$data['userId'];
			$model=$this->getModel();
			$model->setState('profile.userId',$userId);
			$return=$model->getMyProfile(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETUSERPROFILE_SUCCESS'),$return);
					exit;
			    	
			    }
		}
	 }
	
	
	
}
