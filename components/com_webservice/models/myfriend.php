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
		 	
		 	$query->select("a.id,IF( b.fname = '' , a.email, CONCAT_WS(' ',b.fname,b.lname) )AS name,b.profileimage AS image ");
		 	$query->from('#__users  AS a');
		 	$query->join('LEFT','#__christianusers  AS b ON b.userid=a.id');
		 	$query->where('a.id IN(SELECT DISTINCT c.connectfrom FROM #__christianconnection AS c WHERE c.connectto='.$userId.' AND c.status=1) OR a.id IN(SELECT DISTINCT cc.connectto FROM #__christianconnection AS cc WHERE cc.connectfrom='.$userId.' AND cc.status=1)');
		 	$query->where('a.block=0');
                        $query->order('a.id ASC');		
		 	 
		 	$resultdata->friends=$this->_getList($query,$pageIndex,$pageSize);
		 	$resultdata->totalCount=$this->_getListCount($query);
		 	
		 	return $resultdata;
		   
		 }
		 
	
}
