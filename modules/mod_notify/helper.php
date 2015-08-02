<?php
/**
 * JLV Notify Module
 * @version 2.5.6
 * @author Yogendra Singh
 * @website http://joomla.name.vn
 * @Copyright (C) 2010 - 2013 joomla.name.vn. All Rights Reserved.
 * @license GNU General Public License version 2, see LICENSE.txt or http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;
class modNotifyHelper
{
/*
* getting frined request notification.....
*/
 static function getRequestNotification($user)
 {
        // Create a new query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
    	$query->select('a.id as requestid, a.connectfrom, a.connectto, c.fname,c.email, c.profileimage');
    	$query->from('#__christianconnection as a');
    	$query->join('INNER', '#__christianusers AS c ON a.connectfrom = c.userid');
    	$query->join('INNER', '#__users AS b ON a.connectto = b.id');
		$query->where('a.connectto= '.$user);
		$query->where('a.status = '.(int)'0');
		$query->order('a.requestsent DESC');
        $db->setQuery($query); 
        $result = $db->loadObjectList(); 
        return $result;
 	
 }
/*
 * getting new messages notifications.......
 */
 static function getMessagesNotification($user)
 {
        // Create a new query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query1 = $db->getQuery(true);
        
  // finding themessages from message table........     
    	$query1->select('a.id as messageid, a.threadid, a.messagefrom, a.messageto, a.postedon, a.subject, a.body, c.fname,c.email, c.profileimage');
    	$query1->from('#__christianmessage as a');
    	$query1->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
    	$query1->join('INNER', '#__users AS b ON a.messageto = b.id');
		$query1->join('INNER', '#__christianconnection AS cc ON ((a.messageto = cc.connectfrom OR a.messageto = cc.connectto) AND (a.messagefrom = cc.connectfrom OR a.messagefrom = cc.connectto))');
    	$query1->where('a.messageto= '.$user);
		$query1->where('a.status = '.(int)'0');
		$query1->where('cc.status = '.(int)'1');
		$query1->where('FIND_IN_SET('. $user.',a.isdelete) = 0');// FIND_IN_SET mysql function for checking the database multiple as(2,4,65) value is matching by one value($user). Here 0 value is deciding that $user value is exists in column isdeleted.
		$query1->order('a.postedon DESC'); 
        $db->setQuery($query1);  
        $result = $db->loadObjectList(); 
        return $result;
 }
}