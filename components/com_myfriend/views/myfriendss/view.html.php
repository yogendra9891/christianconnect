<?php
/**
 * My Friends
 *
 * @version 	1.0
 * @author		Yogendra singh
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MyfriendViewMyfriendss extends JViewLegacy
{ 
		// Overwriting JView display method
	function display($tpl = null) 
	{   
		$model =& $this->getModel('Myfriendss', 'MyfriendModel'); 
		$this->items = $model->getMyfriends(); 
		$this->paginations = $model->getPagination(); 
		// Display the view
		parent::display($tpl);
	}
	/*
	 * function for searched friends.......
	 */
	public function friends($tpl = null)
	{
		// Display the view
		parent::display($tpl);	 
	}
/*
 * function for seeing profile searched friend.... 
 */
	public function viewprofile($tpl = null)
	{
		// Display the view
		parent::display($tpl);	 	
	}
/*
 * function for showing the messages....
 */
	public function message()
	{
		// Display the view
		parent::display($tpl);	 	
	}
/*
 * function for getting copuntry name by code.
 */	
	public function findcountry($ccode)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('country');
		$query->from('#__countries');
		$query->where('ccode ='.$db->Quote($ccode));
		// Get the options.
		$db->setQuery($query); 
		$result = $db->loadResult();
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		return $result;
	}
/*
 * function for getting religion name by its id....
 */	
	public function findReligion($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__userreligion');
		$query->where('id ='.$id);
//		$query->where('published ='.(int)'1');
		// Get the options.
		$db->setQuery($query); 
		$result = $db->loadResult();
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		return $result;
		
	}
}