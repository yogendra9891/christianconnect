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
class WebserviceControllerReligion extends WebserviceController
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
	public function getModel($name = 'religion', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	/*
	 * finding all the religions....
	 * 
	 */
	public function getReligions(){
	  	$data = json_decode(JRequest::getVar('data'),true); 
		 //check for session
		$this->checkSession($data['sessionId']);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->getReligions($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_RELIGION_SUCCESS'),  JText::_('MSG_RELIGION_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_RELIGION_FAIELD'),  JText::_('MSG_RELIGION_FAIELD'),$return);
					exit;
	  		}
	  	}
	}

	/*
	 * finding all the church category..
	 * 
	 */
	public function getchurchCategory(){
	  	$data = json_decode(JRequest::getVar('data'),true); 
		 //check for session
		$this->checkSession($data['sessionId']);
	  	if(!empty($data))
	  	{
			$model = $this->getModel();
			$return = $model->getchurchCategory($data); 
	  		if($return){
			    	echo $this->responseData(JText::_('ERRCODE_CHURCHCATEGORY_SUCCESS'),  JText::_('MSG_CHURCHCATEGORY_SUCCESS'),$return);
					exit;
	  		}else{
			    	echo $this->responseData(JText::_('ERRCODE_CHURCHCATEGORY_FAIELD'),  JText::_('MSG_CHURCHCATEGORY_FAIELD'),$return);
					exit;
	  		}
	  	}
	}
	
}