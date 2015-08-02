<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
 
/**
 * HelloWorld Model
 */
class ChristianconnectModelChurchLeaders extends JModelList
{
 		/**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
        	$user_id=JFactory::getUser()->id;
        	$churchid=JRequest::getVar('churchid');
        	$db=JFactory::getDBO();
        	$fquery = $db->getQuery(true);
        	$fquery->clear();
        	 // Select some fields
        	 $fquery->select('a.id,a.email,IF( c.id IS NULL , 0, c.id ) as leaderid,b.fname,b.lname,b.profileimage');
        	 // From the tablename
        	 $fquery->from( '#__users AS a LEFT JOIN #__christianusers AS b ON b.userid=a.id');
        	 $fquery->join( 'LEFT',' #__church_leaders AS c ON c.leaderid = b.userid');
        	 // Where the condition with the subquery
        	 $fquery->where('a.id IN (SELECT DISTINCT a.userid FROM #__userchurch AS a WHERE (FIND_IN_SET('.$churchid.',a.localchurch) OR FIND_IN_SET('.$churchid.',a.otherchurch) ) AND a.userid !='.$user_id.')');
			// echo $fquery; die;
        	 return $fquery;
        }
        
	/* function to add leader of church
	 * @params mixed data from list
	 * @params get churchleaderid if same combination is already exist
	 */
	
	function addLeaders()
	{
		// getting selected leader to save
		$cids = JRequest::getVar('cid', array(), '', 'array');
		// Make sure the freind ids are integers
		JArrayHelper::toInteger($cids);
		$churchid=$this->getState('churchleaders.churchid'); 
		$leadertable = $this->getTable('ChurchLeaders','ChristianconnectTable');
		foreach($cids as $cid){
		$churchleaderid=$this->getChurchLeaderId($churchid,$cid); 
		$churchleader=new stdClass();
		$churchleader->id=$churchleaderid;
		$churchleader->churchid=$churchid;
		$churchleader->leaderid=$cid;
		$leadertable->load($churchleaderid);
			if(!$leadertable->save($churchleader))
			{
				return false;
			}
		}
		$this->setState('ChurchLeaders.churchid', $churchid);	
		 return true;
	}
	
	/* function to remove leader of church
	 * @params mixed data from list
	 * @params get churchleaderid if same combination is already exist
	 */
	
	function removeLeaders()
	{
		// getting selected leader to save
		$table = $this->getTable('ChurchLeaders','ChristianconnectTable');
		$churchleaderid=$this->getState('churchleaders.leaderid');	
        $table->load($churchleaderid);
        if(!$table->Delete())
			{
				return false;
			}
		
		return true;
	}
       
	/* function to get the id churchleader table
	 * @params churchid
	 * @params leaderid
	 * @return id if the combination of churchid and leaderid is available
	 */
      function getChurchLeaderId($churchid,$leaderid)
        {
        	$db=JFactory::getDBO();
       		$leaderidquery = $db->getQuery(true);
       		$leaderidquery->clear();
        	//select field
			$leaderidquery->select('a.id');
			$leaderidquery->from('#__church_leaders AS a');
			$leaderidquery->where('a.leaderid='.$leaderid);
			$leaderidquery->where('a.churchid='.$churchid);
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($leaderidquery);
			 
			// Load the results as a list of stdClass objects.
			$xid = intval($db->loadResult());
			
			return $xid;
        }
        

        /*function to get friend that attend the church
         * @params church id 
         * @return object of u
         */
        function getChurchLeader()
        {
        	$churchid=$this->getState('churchleaders.churchid');	
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
			
			return $this->_getList($query);
        }
        

		
}
?>
