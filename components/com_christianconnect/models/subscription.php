  <?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');
// add table as table is lying in com_users not accessable by getTable
require_once('components/com_users/tables/christianusers.php');
 
/**
 * UpdHelloWorld Model
 */
class ChristianconnectModelSubscription extends JModelForm
{
        /**
         * @var object item
         */
        protected $data;
        
	
 
        /**
         * Get the data for a new qualification
         */
        public function getForm($data = array(), $loadData = true)
        {
 
        $app = JFactory::getApplication('site');
 
        // Get the form.
                $form = $this->loadForm('com_christianconnect.subscription', 'subscription', array('control' => 'jform', 'load_data' => true));
                if (empty($form)) {
                        return false;
                }
                return $form;
 
        }
        /**
	 * Method to get the profile form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		// Initialise variables.
		$table = $this->getTable('ChristianUsers','UsersTable');
		$pk=$this->getChristianUsersId();
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$data = JArrayHelper::toObject($properties, 'JObject');
		$data->country=$this->getCountryName($data->country);

		if (property_exists($data, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($data->params);
			$data->params = $registry->toArray();
		}

		return $data;	
	}
	
	/**
	 * Method to get the primary that should be injected in the getdata.
	 *
	 * @return	int	The id for getdata
	 * @param userid of the user logged in
	 */
	
	function getChristianUsersId()
	{
		$userid=JFactory::getUser()->id;
			$db=JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			// Select the required fields from the table.
	        $query->select('a.id ');
	        $query->from('#__christianusers AS a');
	        $query->where('a.userid='.$userid);	
		    $db->setQuery($query);
			$chistianuserid=(int)$db->loadResult();
			return $chistianuserid;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		if (empty($data)) {
			$data = $this->getData();
        }

		return $data;
	}
 
        
	/**
	 * Method to get the churchdetails that will be subscribe by user.
	 *
	 * @return	mixed	The data for the form.
	 * @param	churchid
	 */
	public function getChurchDetail()
	{
		// Initialise variables.
		$table = $this->getTable('church','ChristianconnectTable');
		$pk=(int)JRequest::getVar('churchid');
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$details = JArrayHelper::toObject($properties, 'JObject');
			$details->country=$this->getCountryName($details->country);
			return $details;
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
        
		/*function to save order
         * @params Mixed data from form post
         * @return set orderid in state 
         */
        public function saveOrder()
        {
        		$orderdata=$this->getState('orderdata');
        		$date =& JFactory::getDate();
        		$orderdata['date']=$date->toMysql();
        		$orderdata['id']=0;
        		// Initialise variables.
				$row = $this->getTable('order','ChristianconnectTable');
				// save the church after complete image validation    
				if($row->save($orderdata))
				{
					$this->setState('orderid', $row->id);	
					return true;
				}else{
				   $this->setError($row->getError());
					return false;
				}
        	  
        }
        
	/*function remove order from order table
	  * order is cancelled by subscriber or no successfully paid
	  */
	 function cancelOrder()
	 {
	 	
	 	$oid=$this->getState('oid');
	 	$row = $this->getTable('order','ChristianconnectTable');
		if($row->delete($oid)){
			return true;
		}else{
			return false;
		}
	 	
	 }
	 
	/*function update order status on order table
	  * order is successfully completed
	  */
	 function updateOrderStatus()
	 {
	 	$oid=$this->getState('orderid');
	 	$db = &JFactory::getDBO();
	 	// Create an object for the record we are going to update.
		$object = new stdClass();
 
		// Must be a valid primary key value.
		$object->id = $oid;
		$object->status = '1';
		try {
		    // Update their details in the users table using id as the primary key.
		JFactory::getDbo()->updateObject('#__subscription_order', $object, 'id'); 
		} catch (Exception $e) {
		    // catch the error.
		}
		
	}
	 
 	public function getOrder()
	 {
	 	// Initialise variables.
		$orderid=(int)$this->getState('orderid');	
		$db=JFactory::getDBO();
		//get query 
		$query = $db->getQuery(true);
        $query = $query->clear();
        
        $query->select('a.*,b.cname');
        $query->from('#__subscription_order AS a');
        $query->join('INNER','#__church AS b ON a.churchid=b.id');
        $query->where('a.id='.$orderid);
        //set query
        $db->setQuery($query);
        $order=$db->loadObject();
        if(count($order)!=0)
        {
        	return $order;	
        }else{
         return false;	
        }
        
	 }
	 
	 /* Method to update church Status in Church table
	  * @params churchid
	  * @return true update successful
	  * 
	  */
	 function updateChurchStatus()
	 {
	 	// Initialise variables.
		$churchid=(int)$this->getState('churchid');	
	 	$db = &JFactory::getDBO();
	 	// Create an object for the record we are going to update.
		$object = new stdClass();
 
		// Must be a valid primary key value.
		$object->id = $churchid;
		$object->subscription_status= '1'; 
		try {
		    // Update their details in the users table using id as the primary key.
		   $result = JFactory::getDbo()->updateObject('#__church', $object, 'id'); 
		} catch (Exception $e) {
		    // catch the error.
		}
		
	 }
	 
	 /* Methosd to insert data in user church access table
	  * @param mixed
	  * @return true if successful
	  */
	 
	 function updateUserAccess()
	 {
	 	$userid=JFactory::getUser()->id;
	 	$table = $this->getTable('ChurchAccess','ChristianconnectTable');
	 	$churchid=(int)$this->getState('churchid');	
	 	// Create an object for the record we are going to insert.
		$object = new stdClass();
 		$object->id=0;
		$object->userid= $userid; 
	 	$object->churchid= $churchid;
	 	$table->load($object);
		 	if($table->save($object))
		 	{
		 	 return true;
		 	}else{
		 		return false;
		 	}
	 }

}