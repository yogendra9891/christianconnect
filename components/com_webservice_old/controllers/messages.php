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
class WebserviceControllerMessages extends WebserviceController
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
	public function getModel($name = 'messages', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/*function to get Messages
	 * @param mixed data
	 * @return message object if successful
	 */
	function getMessages()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 if(!empty($data))
		 {
		 	//check for session
			$this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			
			$model=$this->getModel();
			$model->setState('messages.messagedata',$data);
			$model->setState('messages.userId',$userId);
		    $return=$model->getMessages($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETMESSAGES_SUCCESS'),$return);
				exit;
		    }
		 }
	 }

	 
	/*function to get getUnreadMessageCount
	 * @param session id
	 * @return count if successful
	 */
	function getUnreadMessageCount()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 if(!empty($data))
		 {
		 	//check for session
			$this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			
			$model=$this->getModel();
			$model->setState('messages.userId',$userId);
		    $return = $model->getUnreadMessageCount($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETUNREADMESSAGESCOUNT_SUCCESS'),$return);
				exit;
		    }else{
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETUNREADMESSAGESCOUNT_SUCCESS'),$return);
				exit;
		    }
		 }
	 }
	 /*
	  * function for sending the messages.
	  * @params sessionId, sendTo,body
	  * return success/unsuccess message.
	  */
	 function sendMessage()
	 {
		 $data=json_decode(JRequest::getVar('data'),true);
		 if(!empty($data))
		 {
		 	//check for session
			$this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('messages.userId',$userId);
		    $return=$model->sendMessage($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_SENDMESSAGES_SUCCESS'),true);
				exit;
		    }
		    else{
		    	echo $this->responseData(JText::_('ERRCODE_MESSAGE_FAILURE'),  JText::_('MSG_SENDMESSAGES_FAILURE'),false);
				exit;
		    }
		 }
	 }
	 /*
	  * function for delete a message.
	  * @params sessionId,messageId
	  * return success 
	  */
	 public function deleteMessage(){
		 $data=json_decode(JRequest::getVar('data'),true);
		 if(!empty($data))
		 {
		 	//check for session
			$this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('messages.userId',$userId);
		    $return=$model->deleteMessage($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_DELETEMESSAGES_SUCCESS'),true);
				exit;
		    }
		    else{
		    	echo $this->responseData(JText::_('ERRCODE_DELETE_MESSAGE_FAILURE'),  JText::_('MSG_DELETEMESSAGES_FAILURE'),false);
				exit;
		    }
		 }
	 }
}

