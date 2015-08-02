<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Banner HTML class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       2.5
 */
abstract class JHtmlChristianconnect
{
	/**
	 * Display a batch widget for the client selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function emergencies()
	{
		// Create the batch selector to change the client on a selection list.
		$lines = array(
			'<label id="emergency_lbl" for="emergency" class="hasTip" title="'.JText::_('COM_EMERGENCIES_DROPDOWN_LABEL').'::'.JText::_('COM_EMERGENCIES_DROPDOWN_LABEL_DESC').'">',
			JText::_('COM_EMERGENCIES_DROPDOWN_LABEL'),
			'</label>',
			'<select name="emergency" class="inputbox" id="emergency">',
			'<option value="">'.JText::_('COM_EMERGENCIES_EMERGENCY_NOCHANGE').'</option>',
			'<option value="0">'.JText::_('COM_EMERGENCIES_EMERGENCY_SELECT').'</option>',
			JHtml::_('select.options', self::Emergencylist(), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}
	
/**
	 * Display a batch widget for the client selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function countries()
	{
		// Create the batch selector to change the client on a selection list.
		$lines = array(
			'<label id="country_lbl" for="country" class="hasTip" title="'.JText::_('COM_EMERGENCIES_COUNTRY_DROPDOWN_LABEL').'::'.JText::_('COM_EMERGENCIES_DROPDOWN_LABEL_DESC').'">',
			JText::_('COM_EMERGENCIES_COUNTRY_DROPDOWN_LABEL'),
			'</label>',
			'<select name="country" class="inputbox" id="country">',
			'<option value="">'.JText::_('COM_EMERGENCIES_EMERGENCY_NOCHANGE').'</option>',
			'<option value="0">'.JText::_('COM_EMERGENCIES_EMERGENCY_SELECT').'</option>',
			JHtml::_('select.options', self::Countrylist(), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public static function Emergencylist()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id As value, title As text');
		$query->from('#__emergency AS a');
		$query->order('a.title');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}
	
/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public static function Countrylist()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id As value, title As text');
		$query->from('#__country AS a');
		$query->order('a.title');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}

}
