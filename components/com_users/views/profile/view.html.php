<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHTML::_('behavior.modal');
/**
 * Profile view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersViewProfile extends JViewLegacy
{
	protected $data;
	protected $form;
	protected $params;
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param	string	$tpl	The template file to include
	 * @since	1.6
	 */
	public function display($tpl = null)
	{  
		// Get the view data.
		$this->data		= $this->get('Data');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');
		
		// getting the extra information data for editing the profile from christianusers table.......(fname,lname, email etc....)
		$this->christiandata		= $this->get('christianData');
		// getting the accessibilty for the fields.... from userprofileaccess table...
		$this->accessibilty		= $this->get('christianAccessibilty');
		// getting the type of the userlogin(FB/TW) used in the profile edit image ,..
		$this->getUsertype = $this->getUsertype();
		//getting the church data from userchurch table..
		$this->getUserchurch = $this->get('UserChurch');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Check if a user was found.
		if (!$this->data->id) {
			JError::raiseError(404, JText::_('JERROR_USERS_PROFILE_NOT_FOUND'));
			return false;
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->prepareDocument();

		parent::display($tpl);
	}
/*
 * function for new church.....
 * 
 */
	public function newchurchsuccess($tpl= null)
	{   
		$app	= JFactory::getApplication();
		$model1 = $this->getModel('Profile');
		parent::display($tpl);
	}
/*
 * function for new church.....
 * 
 */
	public function newchurch($tpl= null)
	{   
		$app	= JFactory::getApplication();
		$model1 = $this->getModel('Profile');
		$this->churchform	= $model1->getChurchForm(); 
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 *
	 * @since	1.6
	 */
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$user		= JFactory::getUser();
		$login		= $user->get('guest') ? true : false;
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $user->name));
		} else {
			$this->params->def('page_heading', JText::_('COM_USERS_PROFILE'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
	/*
	 * 
	 * getting the dropdown of accessibilty......
	 * @param access: for the dynamic accessibility..
	 */
	public function dropdown($access, $accessname)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id , a.name ');
		$query->from('#__accessibility AS a');
    	// Get the options.
		$db->setQuery($query);
        $options = $db->loadObjectList();
		// making html of dropdown..
		$html = '';
        $html .= '<select id="access_select" class="access_select" name="access['.$accessname.']" required="required">';
        for ($i=0, $n=count( $options ); $i < $n; $i++) 
        {
             $row = &$options[$i];
             $selected	= (($row->id == $access)) ? ' selected="selected"' : '';
             $html .= '<option value="'. $row->id.'"'.$selected.'>'. $row->name.'</option>';
        }
       $html .= '</select>';
       echo $html; 
	
	}
/*
 * Function for getting the user type ..(FB/TW)
 */
	public function getUsertype()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$userid = JFactory::getUser()->id;
		$query->select('a.* ');
		$query->from('#__christianuserstype AS a');
		$query->where('userid= '.$userid);
    	// Get the options.
		$db->setQuery($query);
 		$result = $db->loadObjectList();
 		return $result;
	}
/*
 * 
 * finding the Religion name according to the id..
 */	
	public function religionname($religionid)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$userid = JFactory::getUser()->id;
		$query->select('a.name ');
		$query->from('#__userreligion AS a');
		$query->where('id= '.$religionid);
    	// Get the options.
		$db->setQuery($query);
 		$result = $db->loadResult();
 		return $result;
	}
/*
 * 
 * finding the country name according to the ccode..
 */	
	public function countryname($ccode)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.country');
		$query->from('#__countries AS a');
		$query->where('a.ccode = '.'"'.$ccode.'"'); 
		// Get the options.
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	
}

