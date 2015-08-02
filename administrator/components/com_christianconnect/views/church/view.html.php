<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class christianconnectViewChurch extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}
		
		$this->addToolbar();
		parent::display($tpl);
	    // Set the document
        $this->setDocument();
        
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
			JToolBarHelper::title(JText::_('COM_CHRISTIANCONNECT_TITLE_CHURCHS'));
			JToolBarHelper::apply('church.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('church.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('church.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('church.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('church.cancel', 'JTOOLBAR_CLOSE');
		}

	}
	
 		/**
         * Method to set up the document properties
         *
         * @return void
         */
        protected function setDocument() 
        {
                $document = JFactory::getDocument();
                
                $document->addScript(JURI::root() . "/administrator/components/com_christianconnect"
                                                  . "/models/forms/js/validate.js");
               
        }
}
