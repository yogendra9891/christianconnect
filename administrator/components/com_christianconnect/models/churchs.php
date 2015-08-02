<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Christianconnect records.
 */
class ChristianconnectModelchurchs extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
					            'cname', 'a.cname',
					           'country', 'a.country',
            					'state', 'a.state',
            );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);
		
		/*$state = $app->getUserStateFromRequest($this->context.'.filter.churchstate', 'filter_state', '', 'string');
		$this->setState('filter.churchstate', $state);*/
        
		$statefltr = $app->getUserStateFromRequest($this->context.'.filter.churchstate', 'filter_churchstate', '', 'string');
		$this->setState('filter.churchstate', $statefltr); 
        
        
        
		// Load the parameters.
		$params = JComponentHelper::getParams('com_christianconnect');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id,a.cname,a.state as churchstate,b.country,a.published'
			)
		);
		$query->from('`#__church` AS a');
		//Join to get country name from country table
		$query->join('INNER','#__countries AS b ON b.ccode=a.country');
         $query->where('a.lat = ""');
     
	    // Filter by published state
	    $published = $this->getState('filter.state'); 
	    if (is_numeric($published)) {
	        $query->where('a.published = '.(int) $published);
	    } else if ($published === '') {
	    	$query->where('(a.published IN (0, 1))');
	    }
    		// Filter by search in title
		$search = $this->getState('filter.search');
	   if (!empty($search)) {
					if (stripos($search, 'id:') === 0) {
						$query->where('a.id = '.(int) substr($search, 3));
					} else {
						$search = $db->Quote('%'.$db->escape($search, true).'%');
						$query->where('(a.cname LIKE '.$search.'OR b.country LIKE '.$search.')');
					}
		}
		
        //Filter by state in country
				 $statefltr = $this->getState('filter.churchstate'); 
				if ($statefltr!=='0' && $statefltr!=='') {
					$query->where('a.state = '.$db->quote($statefltr));
				}
		// filter for state published/unpublished
				 $state = $this->getState('filter.state'); 
				if ($state!=='0' && $state!=='') {
					$query->where('a.published = '.$db->quote($state));
				} 
				
		// Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }
        
		return $query;
	}
	
/**
	 * Method to (un)publish a deals
	 * @access	public
	 * @param Id
	 * @return	boolean	True on success
	 * 
	 */
	function updatelatlong($cids,$value)
	{
		$db=JFactory::getDBO();
		$tempquery ='';
		foreach($cids as $cid){
			$tempquery	= $db->getQuery(true);
			$tempquery->select('*');
			$tempquery->from('christian_church ');
			
				// Build the WHERE clause for the primary keys.
		$tempquery->where('id = ' . $cid);
 
		$db->setQuery($tempquery);
		$tempresults=$db->loadObjectList($tempquery);
		foreach($tempresults as $tempresult){
		//get church location from church adrress
        $address = $tempresult->cname." ".$tempresult->street." ".$tempresult->suburb." ".$tempresult->state." ".$tempresult->code." ".$tempresult->country." ".$tempresult->postal_address;
	    $locationdata = $this->getLatLon($address);
	    if($locationdata!=false);
	    {
	    $temdata['lat']= $locationdata->lat;
	    $temdata['lng']= $locationdata->lng;
	    }
	    
	    // Update the publishing state for rows with the given primary keys.
		$updatequery = $db->getQuery(true);
		$updatequery->update('#__church');
		$updatequery->set('lat = ' .$temdata['lat']);
		$updatequery->set('lng = ' .$temdata['lng']);
		$updatequery->where('id = ' .$cid);
		
		$db->setQuery($updatequery);
		$db->execute();
		}
		}
	}
	
	/**
	 * Method to get longitude and latitude.
	 *
	 * @param	array		address data.
	 * @return	mixed		longitude and latitude.
	 */
		
	 function getLatLon($address) {
		//$address=mysql_real_escape_string("PO Box 8041,Urlich,Hamilton,Waikato- thames valley,NZ,3245");
		//$address="PO Box 44,Waihi Beach,Waikato - Thames Valley,3642,NZ"; 
		
		$request_url = "http://maps.googleapis.com/maps/api/geocode/json?&sensor=false&address={?}"; 
        $address = trim($address);
        $address = urlencode($address);  
	        $request_url = str_replace('{?}', $address, $request_url);    
		  	$json = file_get_contents($request_url, true);  //getting the file content 
		  	$decode = json_decode(utf8_encode($json), false); 
			$status = $decode->status;
         if($status=='OK'){
	        return $decode->results[0]->geometry->location;
	     }else{
	     return false;
	     }
    }
}