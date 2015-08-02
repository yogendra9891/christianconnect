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
class WebserviceModelMyFriend extends JModelItem
{
	
			/*function to get my friend data
			 * @param session id, pageindex and pagesize 
			 * @return friend object if successful
			 */
		 function getMyFriends()
		 {
		 	
		 	$resultdata=new stdClass();
		 	$data=$this->getState('myfriend.frienddata');
		 	$userId=$this->getState('myfriend.userId');
		 	$pageIndex=$data['pageIndex'];
		 	$pageSize=$data['pageSize'];
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select("a.userid,CONCAT_WS(' ',a.fname,a.lname) AS name,a.profileimage AS image ");
		 	$query->from('#__christianusers  AS a');
		 	$query->where('a.userid IN(SELECT DISTINCT b.connectfrom FROM #__christianconnection AS b WHERE b.connectfrom='.$userId.' AND b.status=1)');
		 			
		 	$resultdata->friends=$this->_getList($query,$pageIndex,$pageSize);
		 	$resultdata->totalCount=$this->_getListCount($query);
		 	
		 	return $resultdata;
		   
		 }
		 
	
}
