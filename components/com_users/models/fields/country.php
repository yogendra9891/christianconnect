<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
JFormHelper::loadFieldClass('list');
 
/**
 * countries Form Field class for the users component
 */
class JFormFieldCountry extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Country';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{   
		
		$options = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('ccode As value, country As text');
		$query->from('#__countries AS a');
		$query->order('a.country');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		//making the select dropdown.......
		array_unshift($options, JHtml::_('select.option', '0', JText::_('Select Country')));

		return $options;
	 }

}