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
class WebserviceControllerMyChurch extends WebserviceController
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
	public function getModel($name = 'mychurch', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/*function to get churches
	 * @param mixed data
	 * @optional param keyword and lat long
	 * @return church object if successful
	 */
	function getMyChurches()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('mychurch.userId',$userId);
			$return=$model->getMyChurches(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETMYCHURCH_SUCCESS'),$return);
					exit;
			    	
			    }
		}
	 }

	
	/*function to delete my church
	 * @param mixed data
	 * @optional param keyword and lat long
	 * @return church object if successful
	 */
	function deleteMyChurch()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('mychurch.userId',$userId);
			$model->setState('mychurch.churchId',$data['churchId']);
			$return=$model->deleteMyChurch(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_DELETEMYCHURCH_SUCCESS'),0);
					exit;
			    	
			    }
		}
	 }
	 
	 /*function to get church detail
	 * @param churchid
	 * @return church object if successful
	 */
	  function getChurchDetails()
	  {
	   	$data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			
			$model=$this->getModel();
			$model->setState('mychurch.churchId',$data['churchId']);
			$return=$model->getChurchDetails(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETCHURCHDETAILS_SUCCESS'),$return);
					exit;
			    	
			    }
		}
	  }
	  
	  
	/*function to get leader detail
	 * @param churchid
	 * @return church leader object if successful
	 */
	  function getChurchLeaders()
	  {
	   	$data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			
			$model=$this->getModel();
			$model->setState('mychurch.churchId',$data['churchId']);
			$return=$model->getChurchLeaders(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETCHURCHLEADERS_SUCCESS'),$return);
					exit;
			    	
			    }
		}
	  }
	  
	/*function to get ChurchFriends
	 * @param churchid,pageindex,pagesize
	 * @return church friend object if successful
	 */
	  function getChurchfriends()
	  {
	   	$data=json_decode(JRequest::getVar('data'),true);
		 
		 if(!empty($data))
		 {
		 	//check for session
		    $this->checkSession($data['sessionId']);
			//getting useid from sessionid
			$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('mychurch.churchfrienddata',$data);
			$model->setState('mychurch.userId',$userId);
			$return=$model->getChurchFriends(); 
			 	if($return)
			    {
			    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_GETCHURCHFRIENDS_SUCCESS'),$return);
					exit;
			    	
			    }
		}
	  }
	
}
