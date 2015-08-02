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
class WebserviceControllerEmailAdmin extends WebserviceController
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
	public function getModel($name = 'emailadmin', $prefix = 'WebserviceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/*function to send email to admin
	 * @param session id
	 * @return 
	 */
	function emailAdmin()
	{
		 $data=json_decode(JRequest::getVar('data'),true);
		 if(!empty($data))
		 {
		 	//check for session
			$this->checkSession($data['sessionId']);
			//getting useid from sessionid
		 	$userId=$this->getUserIdBySession($data['sessionId']);
			$model=$this->getModel();
			$model->setState('emailadmin.userId',$userId);
			$return=$model->emailAdmin($data); 
		    if($return)
		    {
		    	echo $this->responseData(JText::_('ERRCODE_SUCCESS'),  JText::_('MSG_EMAIL_ADMIN_SUCCESS'),'');
				exit;
		    	
		    }
		 }
	 }
	
	
}
