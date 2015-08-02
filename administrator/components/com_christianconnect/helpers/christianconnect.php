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

/**
 * Christianconnect helper.
 */
class ChristianconnectHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		
		JSubMenuHelper::addEntry(
			JText::_('COM_CHRISTIANCONNECT_TITLE_CHURCHS'),
			'index.php?option=com_christianconnect&view=churchs',
			$vName == 'churchs'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_CHRISTIANCONNECT_TITLE_CATEGORY'),
			'index.php?option=com_christianconnect&view=categories',
			$vName == 'categories'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_CHRISTIANCONNECT_TITLE_RELIGIONS'),
			'index.php?option=com_christianconnect&view=religions',
			$vName == 'religions'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_christianconnect';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	 * gets a list of options for church category.
	 *
	 * @return	option array
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

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option', '0', JText::_('Select Category')));

		return $options;
	}
	
/**
	 * function requried to remove archive and trash from state filter drop down
	 * Returns an array of  published state filter options for  category forproject chrirstian connect.
	 *
	 * @param   array  $config  An array of configuration options.
	 *                          This array can contain a list of key/value pairs where values are boolean
	 *                          and keys can be taken from 'published', 'unpublished', '', '', 'all'.
	 *                          These pairs determine which values are displayed.
	 *
	 * @return  string  The HTML code for the select tag
	 *
	 * @since   11.1
	 */
	public static function getStateOptions($config = array())
	{
		// Build the active state filter options.
		$options = array();
		$options[] = JHtml::_('select.option', ' ', JText::_('COM_CHRISTIANCONNECT_FILTER_STATE'));
		if (!array_key_exists('published', $config) || $config['published'])
		{
			$options[] = JHtml::_('select.option', '1', JText::_('COM_CHRISTIANCONNECT_PUBLISHED'));
		}
		if (!array_key_exists('unpublished', $config) || $config['unpublished'])
		{
			$options[] = JHtml::_('select.option', '0', JText::_('COM_CHRISTIANCONNECT_UNPUBLISHED'));
		}
		if (!array_key_exists('all', $config) || $config['all'])
		{
			$options[] = JHtml::_('select.option', '*', JText::_('JALL'));
		}
		return $options;
	}


	/**
	 * gets a list of options for country.
	 *
	 * @return	option array
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
	
			// Merge any additional options in the XML definition.
			//$options = array_merge(parent::getOptions(), $options);
	
			array_unshift($options, JHtml::_('select.option', '0', JText::_('Select Country')));
	
			return $options;
		}
}