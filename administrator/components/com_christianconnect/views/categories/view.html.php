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
class christianconnectViewcategories extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		$this->addToolbar();
        
        parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/christianconnect.php';

		 JToolBarHelper::title(JText::_('COM_CHRISTIANCONNECT_TITLE_CATEGORY'));
              JToolBarHelper::custom('categories.trash','delete.png','delete_f2.png','JTOOLBAR_TRASH',true);
              JToolBarHelper::divider();
              JToolBarHelper::unpublish('categories.unpublish', 'COM_CHRISTIANCONNECT_TOOLBAR_UNPUBLISHED', true);
			  JToolBarHelper::custom('categories.publish', 'unblock.png', 'unblock_f2.png', 'COM_CHRISTIANCONNECT_TOOLBAR_PUBLISHED', true);
              JToolBarHelper::divider();
              JToolBarHelper::editList('category.edit');
              JToolBarHelper::addNew('category.add');
            //  JToolBarHelper::cancel('categories.cancel', 'JTOOLBAR_CLOSE');
			}
        
}
