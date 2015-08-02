<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of banner records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class ChristianconnectModelBulletinBoards extends JModelList
{
	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'start_date', 'a.start_date',
				'end_date', 'a.end_date'
				
			);
		}

		parent::__construct($config);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'BulletinBoard', $prefix = 'ChristianconnectTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
         * Method to auto-populate the model state.
         *
         * Note. Calling getState in this method will result in recursion.
         *
         * @since       1.6
         */
        protected function populateState($ordering = null, $direction = null)
        {
                // Initialise variables.
                $app = JFactory::getApplication('site');
 
                // Load the filter state.
                $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
                $this->setState('filter.search', $search);
 
                $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
                $this->setState('filter.state', $state);
 
                // List state information.
                parent::populateState('a.title', 'asc');
        }

	
	
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
                // Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                // Select some fields
                $query->select('a.id,a.title,a.start_date,a.end_date,a.published');
                // Add the list ordering clause.
				$orderCol	= $this->state->get('list.ordering', 'a.id');
				$orderDirn	= $this->state->get('list.direction', 'ASC');
				$query->order($db->escape($orderCol.' '.$orderDirn));
                
                // From the emergency table
                $query->from('#__bulletin_board AS a');
		        // Filter by search in title
				$search = $this->getState('filter.search');
				
				if (!empty($search)) {
					if (stripos($search, 'id:') === 0) {
						$query->where('a.id = '.(int) substr($search, 3));
					} else {
						$search = $db->Quote('%'.$db->escape($search, true).'%');
						$query->where('(a.title LIKE '.$search.')');
					}
				}
		        // Filter by published state
				$published = $this->getState('filter.state');
				if (is_numeric($published)) {
					$query->where('a.published = '.(int) $published);
				}
                return $query;
        }
        

}
