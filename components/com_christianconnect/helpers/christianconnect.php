<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */

defined('_JEXEC') or die;

class ChristianconnectHelper
{
	/**
	 * gets a list of options for church category.
	 *
	 * @return	option select box html
	 */
	public static function getCategoryOptions()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id As value, a.title As text');
		$query->from('#__church_category AS a');
		$query->order('a.title');
		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
        
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		array_unshift($options, JHtml::_('select.option', '', JText::_('ALL')));
		return $options;
		
	}
	
	/**
	 * gets a list of options for  country.
	 *
	 * @return	option select box html
	 */
	public static function getCountryOptions()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.ccode As value, a.country As text');
		$query->from('#__countries AS a');
		$query->order('a.country');
		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
        
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		array_unshift($options, JHtml::_('select.option', '0', JText::_('Select Country')));
		return $options;
		
	}

}

