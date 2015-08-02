<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');

/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelLogin extends JModelItem
{
	
			/*function to check Session
			 * @param string sessionId
			 * @return true if id exist
			 */
		 function checkSession()
		 {
		 	$sessionId=trim($this->getState('login.sessionId'));
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select('a.session_id');
		 	$query->from('#__session AS a');
		 	$query->where("a.session_id=".$db->quote($sessionId));
		 	$db->setQuery($query);
		 	$xid = $db->loadResult();
			if($xid!==null)
				{
					$this->updateUserSession();
					return true;
				}else{
					return false;
				}
		 	
		 }
		 
			/*function to get userid 
			 * @param string sessionId
			 * @return userid if sessionid exist
			 */
		 function getUserIdBySession()
		 {
		 	$sessionId=$this->getState('login.sessionId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select('a.userid');
		 	$query->from('#__session AS a');
		 	$query->where('a.session_id='.$db->quote($sessionId));
		 	$db->setQuery($query);
			$xid = intval($db->loadResult());
			if($xid!==0)
				{
					return $xid;
				}else{
					return false;
				}
		 	
		 }

 /*function to update user session
		  * @accept session id
		  * @return true on update
		  */
		 function updateUserSession()
		 {
		 	$sessionId=$this->getState('login.sessionId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
			    // update some fields
                $query->update('#__session AS a');
                $query->set('time='.time());
                $query->where('a.session_id='.$db->quote($sessionId));
                $db->setQuery($query);
                if($db->query())
                {
                	return	true;
                }else{
                	return	false;
                }
		    }
	
}
