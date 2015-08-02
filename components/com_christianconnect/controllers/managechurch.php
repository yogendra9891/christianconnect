<?php
 
// No direct access.
defined('_JEXEC') or die;
 
// import Joomla controller library
jimport('joomla.application.component.controller');
// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');
 
class ChristianconnectControllerManageChurch extends JControllerForm
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

	
	/* Method to save church profile edited by user
	 * @params mixed value
	 * @return true
	 */  
	
	function saveChurchProfile()
	{
	   //check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
		$model=$this->getModel('ManageChurch');
		$return=$model->saveChurchProfile();
		$recordid=$model->getState('church.id');
		if($return)
		{
			$this->setRedirect(
				JRoute::_(
					'index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$recordid
					, false
				)
			);
		}
	
	}
	
	/* Method to save church profile edited by user
	 * @params mixed value
	 * @return true
	 */  
	
	function cancel()
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
		$model=$this->getModel('ManageChurch');
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$recordid=$data['id'];
		$this->setRedirect(
				JRoute::_(
					'index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$recordid
					, false
				)
			);
	}
		
		
	
//End class
 }
?>
