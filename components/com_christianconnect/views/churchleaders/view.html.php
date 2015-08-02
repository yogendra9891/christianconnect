<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class ChristianconnectViewChurchLeaders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
				if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
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
	
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$user		= JFactory::getUser();
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
                $bar->appendButton( 'Standard', 'new', 'Add Leaders', 'churchleaders.addLeaders', false );
                $bar->appendButton( 'Separator' );
                //$bar->appendButton( 'Standard', 'delete', 'Delete Leaders', 'churchleaders.delete', false );
                //generate the html and return
                return $bar->render();
			}
	
	
}
