<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
 
/**
 * HelloWorld Model
 */
class ChristianconnectModelFindchurch extends JModelList
{
	// flag to check for records in other category bydefault
		protected $flag=0;
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
	}
 		
/**
         * Returns a reference to the a Table object, always creating it.
         *
         * @param       type    The table type to instantiate
         * @param       string  A prefix for the table class name. Optional.
         * @param       array   Configuration array for model. Optional.
         * @return      JTable  A database object
         * @since       2.5
         */
        public function getTable($type = 'findchurch', $prefix = 'ChristianconnectTable', $config = array()) 
        {
                return JTable::getInstance($type, $prefix, $config);
        }
       
		/**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
        	   //getting lat lng of search query  	
        	   $location=$this->getLatlng();
        	  
        	   	//$countrycode=$this->getState('search.country');
        	   	$countrycode=$this->getUserCountryCode();  
        	   	$category=$this->getState('search.category');
        	  // Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->clear(true);
                // Select some fields
               	$query->select("a.id,a.cname,b.title as category,a.lat,a.lng,a.address1,a.address2,a.city,a.state,c.country as country,a.postcode,a.profileimage1,a.profileimage2, a.logo, IF( b.map_pin IS NULL , 'map-default.png', b.map_pin) as pin,".
					 " ( 6371 * acos( cos( radians('$location->lat') ) * cos( radians(a.lat ) ) * cos( radians( a.lng ) - radians('$location->lng') ) + sin( radians('$location->lat') ) * sin( radians( a.lat ) ) ) ) AS distance ");
				 // From the tablename
                $query->from('#__church AS a ');
                $query->join('INNER','#__church_category AS b ON b.id=a.category');
                $query->join('INNER','#__countries AS c ON c.ccode=a.country');
        		$query->where('a.country='.$db->quote($countrycode));
                if($category!='' && $this->flag==0){
                	$query->where('a.category='.$db->quote($category));
                }
                $query->order('distance ASC');
                
                return $query;
        }
        
		/**
         * Method to build an SQL query to load the category of chruch.
         *
         * @return legend object
         */
        public function getLegend()
        {
              // Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query = $query->clear();
                // Select some fields
                $query->select('a.*');
                // From the tablename
                $query->from('#__church_category AS a ');
                //where clause
                $query->where('a.published= 1');
                
                $legends= $this->_getList($query);
                return $legends;
        }
        
        
		/**
         * Method to get latitude and longitude of the country
         * @param country name 
         * @return location object having lat&lng
         */
        public function getLatlng()
        {
        	//$countrycode=$this->getState('search.country');
        	//$countrycode=JRequest::getVar('country');
        	$countrycode=$this->getUserCountryCode();  
			$country=$this->getCountryName($countrycode);  
			//set location object. if no country selected set default lat lng of US
			$location=new stdClass();
			$location->lat=(float) 40;
			$location->lng=(float) -100;
			if($country!="0"){
			$request_url = "http://maps.googleapis.com/maps/api/geocode/json?&sensor=false&address={?}"; 
        	
		    $country = urlencode(trim($country));
	        $request_url = str_replace('{?}', $country, $request_url);  
		  	$json = file_get_contents($request_url, true);  //getting the file content 
			$decode = json_decode($json, false); 
			$status = $decode->status;
			
			if($status=="OK")
			{
				// Get parameters from URL
			 $location->lat = (float) $decode->results[0]->geometry->location->lat;
			 $location->lng = (float) $decode->results[0]->geometry->location->lng;
			}
			}
		//	echo "<pre>";var_dump($decode); 
			
			return $location;
    
        }

        /*function to get country name
         * @params country code
         * @return country name
         */
        protected function getCountryName($countrycode)
        {
        		
        	   // Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query = $query->clear();
                // Select some fields
                $query->select('a.country');
                // From the tablename
                $query->from('#__countries AS a ');
                //where clause
                $query->where('a.ccode='.$db->quote($countrycode));
                $db->setQuery($query);
                $countryname= $db->loadResult();
                return $countryname;
        	
        }
        
		/*function to get country code
         * @params userid
         * return country code
         */
        protected function getUserCountryCode()
        {
        		//get userid
        		$userid=JFactory::getUser()->id;
        	   // Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query = $query->clear();
                // Select some fields
                $query->select('a.country');
                // From the tablename
                $query->from('#__christianusers AS a ');
                //where clause
                $query->where('a.userid='.$userid);
                $db->setQuery($query);
                $countrycode= $db->loadResult();
                return $countrycode;
        	
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
        	    // List state information.
                parent::populateState($ordering='distance', $direction='asc'); 
				// Load the filter state.
              //  $country = $this->getUserStateFromRequest($this->context.'search.country', 'country','','string'); 
               // $this->setState('search.country', $country);
 
                $category = $this->getUserStateFromRequest($this->context.'.search.category', 'category', '', 'string');
                $this->setState('search.category', $category);
        }
        
 		 /*Function to get category name
         * @params category id from the user state
         * return category name
         */
       public function getCategoryName()
        {
        	    //variable to store categroy title
        	    $categoryname='';
        	    //geting categroy id from user request state
        		$category = $this->getUserStateFromRequest($this->context.'.search.category', 'category', '', 'string');
				
        		if($category!=''){
        		// Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query = $query->clear();
                // Select some fields
                $query->select('a.title');
                // From the tablename
                $query->from('#__church_category AS a ');
                //where clause
                $query->where('a.id='.$db->quote($category));
                $db->setQuery($query);
                $categoryname= $db->loadResult();
				}
                return $categoryname;
        	
        }

/** override the parent function to get records from other category if they are less than 5
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();
		
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		if(count($items)>0 && count($items)<5)
		{
			$this->flag=1;
			// Load the list items.
			$query = $this->getListQuery();
			//If there are less than 5 exact denomination matches then the remaining churches (up to 5 max) will display
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}
		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
        
}
?>
