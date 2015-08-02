<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class ChristianconnectViewMychurch extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
				$app =& JFactory::getApplication();
				if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
				
				// Get data from the model
				$this->state		= $this->get('State');
				$this->items		= $this->get('Items');
				$this->pagination	= $this->get('Pagination');
 
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
               
                // Display the template
                parent::display($tpl);
	}
	

	
	
}
