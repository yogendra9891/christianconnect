<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
require_once (JPATH_ROOT.DS.'components'.DS.'com_myfriend'.DS.'tables'.DS.'christianmessage.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_myfriend'.DS.'tables'.DS.'christianthread.php');
/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelMessages extends JModelItem
{
	
			/*function to get Messages
			 * @param mixed data
			 * @return message object if successful
			 */
		 function getMessages()
		 {
		 	$resultdata=new stdClass();
		 	$messagedata=$this->getState('messages.messagedata');
		 	$userId=$this->getState('messages.userId');
		 	
		 	$type=$messagedata['Type'];
		 	$pageIndex=$messagedata['pageIndex'];
		 	$pageSize=$messagedata['pageSize'];
		 	$threadId=$messagedata['threadId'];
		 	
		 	$db=JFactory::getDBO();
		 	$mainquery=$db->getquery(true);
		 	$mainquery->clear();
		 	
		 	$subquery=$db->getquery(true);
		 	$subnquery->clear();
		 	
		 	$mainquery->select('a.id,a.messagefrom,a.messageto,a.subject,a.body,a.status');
		 	$mainquery->from('#__christianmessages AS a');
		 	$mainquery->join('INNER', '#__users AS b ON a.messageto = b.id');
		 	$mainquery->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
			$mainquery->join('INNER', '#__christianconnection AS cc ON ((a.messageto = cc.connectfrom OR a.messageto = cc.connectto) AND (a.messagefrom = cc.connectfrom OR a.messagefrom = cc.connectto))');
		 	$mainquery->where('a.messagefrom='.$userId);
		 	$mainquery->where('a.isDelete!=1');
		 	$db->setQuery($mainquery);
		 	$threadmessage=$db->loadObject();
		 	
		 	var_dump($threadmessage); die;
		 	
		 	$subquery->select('a.id,a.messagefrom,a.messageto,a.subject,a.body,a.status');
		 	$mainquery->from('#__christianmessages AS a');
		 	$mainquery->where('a.messagefrom='.$userId);
		 	$mainquery->where('a.isDelete=0');
		 
		 	echo $query; die;
		 	$resultdata->messages=$this->_getList($query,$pageIndex,$pageSize);
		 	$resultdata->totalCount=$this->_getListCount($query);
		 	
		 	return $resultdata;
		   
		 }
		 
			/*function to get UnreadMessageCount
			 * @param userid
			 * @return count if successful
			 */
		 function getUnreadMessageCount($data)
		 {
		 	$userId = $this->getState('messages.userId');
		 	$UnreadMessageCount = "0";
	        // Create a new query object.
	        $db = JFactory::getDbo();
	        $query = $db->getQuery(true);
	        $query1 = $db->getQuery(true);
	        
	  // finding themessages from message table........     
	    	$query1->select('count(a.id)');
	    	$query1->from('#__christianmessage as a');
	    	$query1->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
	    	$query1->join('INNER', '#__users AS b ON a.messageto = b.id');
			$query1->join('INNER', '#__christianconnection AS cc ON ((a.messageto = cc.connectfrom OR a.messageto = cc.connectto) AND (a.messagefrom = cc.connectfrom OR a.messagefrom = cc.connectto))');
	    	$query1->where('a.messageto= '.$userId);
			$query1->where('a.status = '.(int)'0');
			$query1->where('cc.status = '.(int)'1');
			$query1->where('FIND_IN_SET('. $userId.',a.isdelete) = 0');// FIND_IN_SET mysql function for checking the database multiple as(2,4,65) value is matching by one value($user). Here 0 value is deciding that $user value is exists in column isdeleted.
			$query1->order('a.postedon DESC'); 
	        $db->setQuery($query1);  
	        $result = $db->loadResult(); 
	        return $result;
		 }
		 
		/*
		 * function for sending the message
		 * @param userid, data(sendTo,body)..
		 * return success?failure.
		 */ 
		 function sendMessage($data)
		 {
			 $userId=$this->getState('messages.userId');
			 //checking for the message object is an array..
			 if(!is_array($data['messageObject'])){
			 	return 0;
			 }
			 $db=JFactory::getDBO();
			 $query=$db->getquery(true);
			 $query->clear();
			 $threaddata = array();
			 $table =& JTable::getInstance('christianthread','MyfriendTable');
			 if(!$table->save($threaddata))
			 {
			 	return 0;
			 }
			 else
			 {
			 	$threadid =  $table->id;
			 	$unreadcount = $table->unreadcount;
			 	//save the data in the child table..
			 	$query1 = $db->getQuery(true);
			 	$data1['messagefrom'] = $userId;
			 	$data1['messageto'] = $data['messageObject']['sendTo'];
			 	$data1['threadid'] = $threadid;
//				$date1 =& JFactory::getDate();
//				$data1['postedon'] = $date1->toFormat();
				$data1['postedon'] = $data['messageObject']['timeStamp'];
				$data1['subject'] = '';
				$data1['body'] = $data['messageObject']['body'];
		        $table1 =& JTable::getInstance('christianmessage','MyfriendTable');
		        if(!$table1->save($data1)){return 0;}
		        else {
		        	$table2 =& JTable::getInstance('christianthread','MyfriendTable');
		        	$data2['unreadcount'] = $unreadcount+1;
		        	$table2->load($threadid);
		        	$table2->save($data2);
		        	return $table2->id;
		            }		
			 }
		 }
		 /*
		  * function for deleting a message.
		  * @params sessionId, messageId
		  * return success
		  */
		 public function deleteMessage($data){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$userId = $this->getState('messages.userId');
			$data1 = array();
			$data1['id'] = $messageid = $data['messageId'];
			$table =& JTable::getInstance('christianmessage','MyfriendTable');
			$table->load($messageid);
			$threadid = $table->threadid;
			if($table->isdelete != 'null'){
				$data1['isdelete'] = $table->isdelete. ','.$userId;
			}else{
				$data1['isdelete'] = $userId;
			}
			$status = $table->status;
			if(!$table->save($data1))
			{
			 return 0;
			}
			else{
				$table1 =& JTable::getInstance('christianthread','MyfriendTable');
				$table1->load($threadid);
				if($status ==1){
					 if($table1->readcount > 0) {$readcount = $table1->readcount;}else{$readcount = 0;}
			         $data2['readcount'] = $readcount;	
				}
				else{
					 if($table1->unreadcount > 0) {$unreadcount = $table1->unreadcount;}else{$unreadcount = 0;}
			         $data2['unreadcount'] = $unreadcount;	
				}
				$table1->save($data2);
				return 1;
			}
		 }
}
