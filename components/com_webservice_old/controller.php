<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_Websevice
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Websevice Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_Websevice
 * @since 1.5
 */
class WebserviceController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $safeurlparams=null);
		return $this;
	}
	
	/*function to response data
	 * @params errNo,msgString,result if any other wise set 0
	 * @return json string
	 */
   
  protected function responseData($errNo,$msgString,$result){
        
   		 	$this->response['resultObject']=$result;    
  			$this->response['message']=$msgString;
  			$this->response['statusCode']=$errNo;
        
        return json_encode($this->response); 
    }
    
    /* Method to check the sessionid in session Table
     * @param sessionid
     * @return true if sessionid exit 
     */

    protected function checkSession($sessionId)
    {
      	$model=$this->getModel('login');
      	$model->setState('login.sessionId',$sessionId);
      	$return=$model->checkSession();
     	 if(!$return)
      	 {
      	 	 echo $this->responseData(JText::_('ERRCODE_INVALID_SESSION'),  JText::_('MSG_INVALID_SESSION'),0);
			 exit;
      	 }
    }
    
	/* Method to check the sessionid in session Table
     * @param sessionid
     * @return true if sessionid exit 
     */

    protected function getUserIdBySession($sessionId)
    {
      	$model=$this->getModel('login');
      	$model->setState('login.sessionId',$sessionId);
      	$userId=$model->getUserIdBySession();
      	return $userId;
    }
    
 	
}
