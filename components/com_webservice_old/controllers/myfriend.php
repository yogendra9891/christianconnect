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
class WebserviceControllerMyFriend extends WebserviceController
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
	public function getModel($name = 'myfriend', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/*function to get my friend
	 * @param sessionid,pageindex and page size
	 * @return church object if successful
	 */
	function getMyFriends()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		
		 if(!empty($data))
		 {
		 	 //check for session
		 	$this->checkSession($data['sessionId']);
		 	//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']); 
			$model=$this->getModel();
			$model->setState('myfriend.frienddata',$data);
			$model->setState('myfriend.userId',$userId);
		    $return=$model->getMyFriends($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETMYFRIEND_SUCCESS'),$return);
				exit;
		    	
		    }
		 }
	 }

	/*function to find churches
	 * @param lat, long, churchtype
	 * @return church object if successful
	 */
	function searchChurch()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
			//check for session
		 	$this->checkSession($data['sessionId']);
		 	$model=$this->getModel();
			$model->setState('findchurch.churchsearch',$data);
		    $return=$model->searchChurch($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_SEARCHCHURCH_SUCCESS'),$return);
				exit;
		    	
		    }
		 }
	 }
	
	
}
