<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
 
/**
 * HelloWorld Model
 */
class ChristianconnectModelMychurch extends JModelList
{
 		/**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
        		//get user church
        		$userchurch=$this->getUserChurch();
        		// Create a new query object.           
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->clear();
                //if no church related to user exist redirect it to profile
                if(trim($userchurch['localchurch'])=='' && trim($userchurch['otherchurch']==''))
                {
	                if($this->get('UserChurch')==false)
					{
					 $app =& JFactory::getApplication();
					 $errormessage = JText::_('UPDATE_PROFILE');
					 $app->redirect('index.php?option=com_users&view=profile', $errormessage,'message');
					}
                }
                //creating some condition
        		if($userchurch['localchurch']!='')
        		{
        			$where[]="a.id IN (".$userchurch['localchurch'].")";
        		}
        		if($userchurch['otherchurch']!='')
        		{
        			$where[]=" a.id IN(".$db->quote($userchurch['otherchurch']).")";
        		}
        		
        		
        		// Select some fields
                $query->select('a.*');
                // From the tablename
                $query->from('#__church AS a ');
                // Where condition
                $query->Where($where,'OR');
                
                return $query;
        }
        
        /*function to getuser church
         * @params user id from user object
         * @return object of userchurch
         */
       public function getUserChurch()
        {
        	$userid=JFactory::getUser()->id;
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
            // Select some fields
            $query->select('a.*');
            // From the tablename
            $query->from('#__userchurch AS a ');
            // Where condition
            $query->Where('a.userid =' .(int)$userid);
            
            $db->setQuery($query);
            return $db->loadAssoc();
            
        }
        
 
        /*function to get friend that attend the church
         * @params church id 
         * @return object of u
         */
       protected function getFriendAttandChurch($churchid)
        {
        	$user_id=JFactory::getUser()->id;
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
        	 // Select some fields
        	 $query->select('a.id,a.email,b.fname,b.lname,b.profileimage');
        	 // From the tablename
        	 $query->from( '#__users AS a LEFT JOIN #__christianusers AS b ON b.userid=a.id');
        	 // Where the condition with the subquery
        	 $query->where('a.id IN (SELECT DISTINCT a.userid FROM #__userchurch AS a WHERE (FIND_IN_SET('.$churchid.',a.localchurch) OR FIND_IN_SET('.$churchid.',a.otherchurch) ) AND a.userid !='.$user_id.')');
			
        	 return $query;
			//return $this->_getList($query);
        }
        
 	
	/**
	 * override function to add friend in church
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		//counter for array
		$i=0;
		$c=0;
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
	
	
    
 		/* function to get church detail
         * @params church id from request
         * @return object of church
         */
       Public function getFullDeatil()
        {
        	$churchid=$this->getState('mychurch.churchid');
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
            // Select some fields
            $query->select('a.*');
            // From the tablename
            $query->from('#__church AS a ');
            // Where condition
            $query->Where('a.id =' .(int)$churchid);
            
            $db->setQuery($query);
            $items=$db->loadObject();
            //getting the other user attand same church
        	$fquery=$this->getFriendAttandChurch($items->id);
        	$leaderquery=$this->getChurchLeader($items->id);
			$items->friends =$this->_getList($fquery);
			$items->leaders =$this->_getList($leaderquery);
			$items->access=$this->isUserOwner($items->id); 
			 return $items;
        }
        
		/* function to get church detail
         * @params church id from request
         * @return object of church
         */
       Public function getNavigationArray()
        {
        	$navArray=array();
        	$items=$this->getItems();
        	if(count($items)!=0){
        	foreach($items as $item){
        		$navArray[]=$item->id;
        	}
        	}
        	return $navArray;
        }
		
        /* Method to get the user is owner of the church
         * @params churchid
         * @params userid
         * return true if user is owner  
         */
        
	 Public function isUserOwner($churchid)
        {
        	$user_id=JFactory::getUser()->id;
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
            // Select some fields
            $query->select('a.id');
            // From the tablename
            $query->from('#__user_church_access AS a ');
            // Where condition
            $query->Where('a.userid ='.(int)$user_id);
            $query->Where('a.churchid ='.(int)$churchid);
            
            $db->setQuery($query);
            $id=$db->loadObject();
            if(count($id)!=0)
            {
            	return true;
            }
            return false;
        }
        
        

        /*function to get friend that attend the church
         * @params church id 
         * @return object of u
         */
       protected function getChurchLeader($churchid)
        {
        	$user_id=JFactory::getUser()->id;
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
        	 // Select some fields
        	 $query->select('a.id,a.email,b.fname,b.lname,b.profileimage');
        	 // From the tablename
        	 $query->from( '#__users AS a LEFT JOIN #__christianusers AS b ON b.userid=a.id');
        	 $query->join( 'INNER',' #__church_leaders AS c ON c.leaderid = b.userid');
        	 // Where the condition with the subquery
        	 $query->where('c.churchid='.$churchid);
			 return $query;
			//return $this->_getList($query);
        }
        
}
?>
