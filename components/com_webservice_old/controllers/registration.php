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
class WebserviceControllerRegistration extends WebserviceController
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
	public function getModel($name = 'registration', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	/*
	 * function for doing registration(Normal, FB, Twitter)......
	 * @params 
	 * email, firstname,lastname, password, isSocialAuth
	 */
   public function Register(){
	  	$data = json_decode(JRequest::getVar('data'),true);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->Register($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_REGISTRATION_SUCCESS'),  JText::_('MSG_REGISTRATION_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_REGISTRATION_FAIELD'),  JText::_('MSG_REGISTRATION_FAIELD'),$return);
					exit;
	  		}
	  	}
  }
  /*
   * Function for login...
   * @params
   * email, password, Issocial
   */
   public function Login(){
	  	$data = json_decode(JRequest::getVar('data'),true);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->Login($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_LOGIN_SUCCESS'),  JText::_('MSG_LOGIN_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_LOGIN_FAIELD'),  JText::_('MSG_LOGIN_FAIELD'),$return);
					exit;
	  		}
	  	}
   }
 /*
  * function for forget password..
  * @params
  * emailId
  */ 
   public function ForgetPassword(){
	  	$data = json_decode(JRequest::getVar('data'),true);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->ForgetPassword($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_FORGET_EMAIL_SUCCESS'),  JText::_('MSG_FORGET_EMAIL_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_FORGET_EMAIL_FAIELD'),  JText::_('MSG_FORGET_EMAIL_FAIELD'),$return);
					exit;
	  		}
	  	}
   }
  /*
   * function for forget password confirm..
   * @params
   * email, veriicationcode, newpassword, newpassword1
   */
   public function ForgetPasswordConfirm(){
	  	$data = json_decode(JRequest::getVar('data'),true);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->ForgetPasswordConfirm($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_PASSWORD_CHANGE_SUCCESS'),  JText::_('MSG_PASSWORD_CHANGE_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_PASSWORD_CHANGE_FAIELD'),  JText::_('MSG_PASSWORD_CHANGE_FAIELD'),$return);
					exit;
	  		}
	  	}
   }
}