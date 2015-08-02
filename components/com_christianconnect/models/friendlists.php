<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
 
/**
 * HelloWorld Model
 */
class ChristianconnectModelFriendLists extends JModelList
{
 		/**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
        	$churchid=JRequest::getVar('churchid');
        	$user_id=JFactory::getUser()->id;
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
        	 // Select some fields
        	 $query->select('a.id,b.email,b.fname,b.lname,b.profileimage');
        	 // From the tablename
        	 $query->from( '#__users AS a LEFT JOIN #__christianusers AS b ON b.userid=a.id');
        	 // Where the condition with the subquery
        	 $query->where('a.id IN (SELECT DISTINCT a.userid FROM #__userchurch AS a WHERE (FIND_IN_SET('.$churchid.',a.localchurch) OR FIND_IN_SET('.$churchid.',a.otherchurch) ) AND a.userid !='.$user_id.')');
			 // orderby
			 $query->orderby('a.id DESC');
        	 return $query;
        }
        
	
        

	
}
?>
