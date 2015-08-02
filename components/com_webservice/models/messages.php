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
		 	
		 	$pageIndex=$messagedata['pageIndex'];
		 	$pageSize=$messagedata['pageSize'];
		 	$threadId=$messagedata['threadId'];
		 	
		 	$db=JFactory::getDBO();
		 	$mainquery=$db->getquery(true);
		 	$mainquery->clear();
		
		
			$mainquery->select("a.id AS messageId, a.body, CONCAT_WS( ' ', e.fname, e.lname ) AS senderName,e.profileimage as senderImage, CONCAT_WS( ' ', c.fname, c.lname ) AS senderTo, a.postedon as timestamp, a.status as isRead, if( a.messagefrom =".$userId.", 0, 1 ) AS isReciever");
			$mainquery->from('#__christianmessage AS a');
			$mainquery->join('INNER', '#__users AS b ON a.messageto = b.id');
			$mainquery->join('LEFT',  '#__christianusers AS c ON c.userid = b.id');
			$mainquery->join('INNER', '#__users AS d ON a.messagefrom = d.id');
			$mainquery->join('LEFT',  '#__christianusers AS e ON e.userid = d.id');
			$mainquery->where('a.threadid ='.$threadId);
			$mainquery->where('FIND_IN_SET( '.$userId.', a.isDelete ) =0');
			
			$resultdata->messages=$this->_getList($mainquery,$pageIndex,$pageSize);
			$resultdata->totalCount=$this->_getListCount($mainquery);

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
	    	/*$query1->select('COUNT(DISTINCT a.threadid)');*/
		$query1->select('COUNT(a.id)');
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
	        $result = (int)$db->loadResult(); 
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
			$connectionId=0;
			 //checking for the message object is an array..
			 if(!is_array($data['messageObject'])){
			 	return 0;
			 }
			$connectionId=$this->checkFriendConnection($userId,$data['messageObject']['sendTo']);
				if($connectionId==0)
				{
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
				$data1['body'] = base64_decode($data['messageObject']['body']);
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

/*function to save reply Message
	 * @param session id
	 * @return true message if successfull
	 */
	function replyMessage()
	{
			$userId=$this->getState('messages.userId');
			$messageObject=$this->getState('messages.messageObject');
			
			$date=JFactory::getDate();
		 	$messagePostedon=$date->toMysql($messageObject['timestamp']);
			$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	// check for thread already exist
		 	$tid=$this->isThreadExist($messageObject['threadId']); 
		 	if($tid!=0)
		 	{
		 		//create save object
			 	$saveMessageObject= new stdClass();
			 	$saveMessageObject->id=0;
			 	$saveMessageObject->threadid=$messageObject['threadId'];
			 	$saveMessageObject->messagefrom=$userId;
			 	$saveMessageObject->messageto=$messageObject['recieverId'];
			 	$saveMessageObject->body=base64_decode($messageObject['body']);
			 	$saveMessageObject->status=0;
			 	$saveMessageObject->postedon=$messagePostedon;
			 	
			 	//getting table to save messageobject
				$table =& JTable::getInstance('christianmessage','MyfriendTable');
			 	return $table->save($saveMessageObject);
		 	}else{
		 	return false;
		 	}
	}
	 
	 /*function to check thread exist in thread table
	  * @accept $threadId
	  * @return thread id if exist else return 0
	  * 
	  */
	 function isThreadExist($threadId)
	 {
	  		$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select('a.id');
		 	$query->from('#__christianthread AS a');
		 	$query->where('a.id='.$threadId);
		 	
		 	$db->setQuery($query);
		 	$tid=(int)$db->loadResult();
		 	return $tid;
	}

/*function to get Messages Thread
			 * @param sessionId
			 * @return message object if successful
			 */
		 function getMessageThreads()
		 {
		 	$resultdata=array();
		 	$i=0;
		 	$userId=$this->getState('messages.userId');
		 	$messagedata=$this->getState('messages.data');
		 	
		 	$pageIndex=$messagedata['pageIndex'];
		 	$pageSize=$messagedata['pageSize'];
		 	
		 	$db=JFactory::getDBO();
		 	$mainquery=$db->getquery(true);
		 	$mainquery->clear();
		 	
		 	$subquery=$db->getquery(true);
		 	
			
			
			$mainquery->select('count( a.id ) AS totalMessages, a.threadid AS threadId, b.unreadcount AS totalUnreadMessages, CONCAT_WS( "", c.fname, c.lname ) AS senderName, c.profileimage AS senderImage, a.messageto');
			$mainquery->from('#__christianmessage AS a ');
			$mainquery->join('INNER','#__christianthread AS b ON b.id = a.threadid ');
			$mainquery->join('LEFT','#__christianusers AS c ON c.userid = a.messagefrom ');
			$mainquery->join('LEFT','#__users AS cc ON cc.id = c.userid ');
			$mainquery->where('a.messagefrom ='.$userId.' OR a.messageto ='.$userId);
			$mainquery->group('a.threadid');
			
			$messagesObjs=$this->_getList($mainquery,$pageIndex,$pageSize);
			
			foreach($messagesObjs as $messagesObj)
			{
				$subquery->clear();	
				$subquery->select('a.id AS messageId, a.threadid, a.body, CONCAT_WS( "", c.fname, c.lname ) AS senderName, CONCAT_WS( "", d.fname, d.lname ) AS sendTo, c.profileimage AS senderImage, a.postedon AS timestamp,a.status AS isRead');
				$subquery->from('#__christianmessage AS a');
				$subquery->join('LEFT','#__christianusers AS c ON c.userid = a.messagefrom ');
				$subquery->join('LEFT','#__users AS cc ON cc.id = c.userid ');
				$subquery->join('LEFT','#__christianusers AS d ON d.userid = a.messageto ');
				$subquery->join('LEFT','#__users AS dd ON dd.id = d.userid ');
				$subquery->where(' a.threadid ='.$messagesObj->threadId);
				$subquery->order('a.postedon DESC');
				
				$db->setQuery($subquery);
		 		$latestMessageObj=$db->loadObject();
		 		$resultdata[$i]->threadId=$messagesObj->threadId;
		 		$resultdata[$i]->senderName=$messagesObj->senderName;
		 		$resultdata[$i]->senderImage=$messagesObj->senderImage;
		 		$resultdata[$i]->latestMessage=$latestMessageObj;
		 		$resultdata[$i]->totalMessages=$messagesObj->totalMessages;
		 		$resultdata[$i]->totalUnreadMessages=$messagesObj->totalUnreadMessages;
		 		$i++;
			}
	
			$resultdata['totalCount']=$this->_getListCount($mainquery);
	
		 	return $resultdata;
		   
		 }

/*function to test the connection between message sender and message reciever
		  * @param sender id and reciever id
		  * @return connection id
		  */
		 function checkFriendConnection($connectFrom,$connectTo)
		 {
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id');
		 	$query->from('#__christianconnection AS a');
		 	$query->where('(a.connectfrom='.$connectFrom.' AND a.connectto='.$connectTo.' AND a.status=1) OR (a.connectfrom='.$connectTo.' AND a.connectto='.$connectFrom.' AND a.status=1)');

		 	$db->setQuery($query);
		 	$connectionId=(int)$db->loadResult();
		 	return $connectionId;
		 }
}
