<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the UpdHelloWorld Component
 */
class  christianconnectViewsubscription extends JView
{
	protected $state;
	protected $form;
	protected $churchdetail;
	
        // Overwriting JView display method
        function display($tpl = null) 
        {
       			 if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
        		// Get some data from the models
                $this->state          = $this->get('State');
                $this->churchdetail = $this->get('churchdetail');
                $this->form     = $this->get('Form');
             
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                // Display the view
                parent::display($tpl);
                $this->setDocument();
        }
        
		/**
         * Method to set up the document properties
         *
         * @return void
         */
        protected function setDocument() 
        {
                $document = JFactory::getDocument();
                $document->addScript(JURI::root() . "administrator/components/com_christianconnect"
                                                  . "/models/forms/js/validate.js");
                
        }
}