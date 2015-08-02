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
class WebserviceControllerPushnotification extends WebserviceController
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
	public function getModel($name = 'pushnotification', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	
	 /*function to send the notification to all the friend to share there location
	  *@param sessionId
	 * @return 
	 */
	function requestFriendLocation()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
			//check for session
		 	$this->checkSession($data['sessionId']);
		 	//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']); 
		 	$model=$this->getModel();
			$model->setState('pushnotification.sessionId',$data);
			$model->setState('pushnotification.userId',$userId);
		    $return=$model->requestFriendLocation($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_REQUESTFRIENDLOCATION_SUCCESS'),'');
				exit;
		    	
		    }
		 }
	 }
	 
 	/* function  update location of friend who wants to share location with user
	  *@param sessionId
	 * @return 
	 */
	function respondFriendLocation()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
			// validate session
		 	$this->checkSession($data['sessionId']);
		 	//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']); 
		 	$model=$this->getModel();
			$model->setState('pushnotification.sessionId',$data);
			$model->setState('pushnotification.friendId',$userId);
			$model->setState('pushnotification.locationData',$data);
		    $return=$model->respondFriendLocation(); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_REQUESTFRIENDLOCATION_SUCCESS'),'');
				exit;
		    	
		    }
		 }
	 }
	
	/* function  update location of friend who wants to share location with user
	  *@param sessionId
	 * @return 
	 */
	function getFriendLocation()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
			// validate session
		 	$this->checkSession($data['sessionId']);
		 	//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']); 
		 	$model=$this->getModel();
			$model->setState('pushnotification.sessionId',$data);
			$model->setState('pushnotification.userId',$userId);
			
		    $return=$model->getFriendLocation(); 
		    if(count($return)!=0)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETFRIENDLOCATION_SUCCESS'),$return);
				exit;
		    }else{
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETFRIENDLOCATION_SUCCESS'),$return);
				exit;
		    }
		 }
	 }

 
/* function  update location of friend who wants to share location with user
	  *@param sessionId
	 * @return 
	 */
	function updateDeviceInfo()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
			// validate session
		 	$this->checkSession($data['sessionId']);
		 	//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']); 
		 	$model=$this->getModel();
			$model->setState('pushnotification.deviceData',$data);
			$model->setState('pushnotification.userId',$userId);
			
		    $return=$model->updateDeviceInfo(); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_UPDATEDEVICEINFO_SUCCESS'),'');
				exit;
		    	
		    }else{
		    echo $this->responseData(JText::_('ERRCODE_UPDATEDEVICEINFO_FAIL'),  JText::_('MSG_UPDATEDEVICEINFO_FAIL'),'');
				exit;
		    	
		    }
		    
		 }
	 }
	
}
