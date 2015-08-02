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
 * View class for a list of Christianconnect.
 */
class ChristianconnectViewChurchs extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		 // Get data from the model
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        //set toolbar
 		$this->addToolbar();
        // Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
 protected function addToolBar() 
        {
              JToolBarHelper::title(JText::_('COM_CHRISTIANCONNECT_TITLE_CHURCHS'));
              JToolBarHelper::custom('churchs.trash','delete.png','delete_f2.png','JTOOLBAR_TRASH',true);
              JToolBarHelper::divider();
              JToolBarHelper::unpublish('churchs.unpublish', 'COM_CHRISTIANCONNECT_TOOLBAR_UNPUBLISHED', true);
			  JToolBarHelper::custom('churchs.publish', 'unblock.png', 'unblock_f2.png', 'COM_CHRISTIANCONNECT_TOOLBAR_PUBLISHED', true);
              JToolBarHelper::divider();
              JToolBarHelper::editList('church.edit');
              JToolBarHelper::addNew('church.add');
             // JToolBarHelper::cancel('churchs.cancel', 'JTOOLBAR_CLOSE');
             JToolBarHelper::divider();
			 JToolBarHelper::preferences('com_christianconnect');
        }
	
}
