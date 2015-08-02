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
//require JPATH_BASE.'/administrator/includes/toolbar.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/christianconnect.php';

/**
 * View class for a list of Christianconnect.
 */
class ChristianconnectViewBulletinBoards extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
		echo $this->addToolbar();
        parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
	 
                // add required stylesheets from admin template
                $document    = & JFactory::getDocument();
                $document->addStyleSheet('administrator/templates/system/css/system.css');
                //now we add the necessary stylesheets from the administrator template
                //in this case i make reference to the bluestork default administrator template in joomla 1.6
                $document->addCustomTag(
                        '<link href="administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />'."\n\n".
                        '<!--[if IE 7]>'."\n".
                        '<link href="administrator/templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" />'."\n".
                        '<![endif]-->'."\n".
                        '<!--[if gte IE 8]>'."\n\n".
                        '<link href="administrator/templates/bluestork/css/ie8.css" rel="stylesheet" type="text/css" />'."\n".
                        '<![endif]-->'."\n".
                        '<link rel="stylesheet" href="administrator/templates/bluestork/css/rounded.css" type="text/css" />'."\n"
                        );
                //load the JToolBar library and create a toolbar
                jimport('joomla.html.toolbar');
                $bar =new JToolBar( 'toolbar' );
                //and make whatever calls you require
                $bar->appendButton( 'Standard', 'delete', 'Trash', 'bulletinboards.trash', false );
                $bar->appendButton( 'Separator' );
                $bar->appendButton( 'Standard', 'edit', 'Edit', 'bulletinboard.edit', false );
                $bar->appendButton( 'Standard', 'new', 'Add', 'bulletinboard.add', false );
                $bar->appendButton( 'Separator' );
                $bar->appendButton( 'Standard', 'publish', 'Publish', 'bulletinboards.publish', false );
                $bar->appendButton( 'Standard', 'unpublish', 'UnPublish', 'bulletinboards.unpublish', false );
               
                //generate the html and return
                return $bar->render();
			}
        
}
