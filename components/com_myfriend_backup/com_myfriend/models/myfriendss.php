<?php

/**
 * @version     1.0.0
 * @package     com_myfriend
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      yogendra <yogendra.singh@daffodilsw.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once('components/com_myfriend/tables/christianconnection.php');
require_once('components/com_myfriend/tables/christianmessages.php');
require_once('components/com_myfriend/tables/christianchildmessages.php');
/**
 * Methods supporting a list of Myfriend records.
 */
class MyfriendModelMyfriendss extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);



		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
		$this->getState(
                        'list.select', 'a.*'
                        )
                        );

                        $query->from('`#__christianconnection` AS a');




                        // Filter by search in title
                        $search = $this->getState('filter.search');
                        if (!empty($search)) {
                        	if (stripos($search, 'id:') === 0) {
                        		$query->where('a.id = '.(int) substr($search, 3));
                        	} else {
                        		$search = $db->Quote('%'.$db->escape($search, true).'%');

                        	}
                        }



                        return $query;
	}
	/*
	 * function for getting myfriends.....
	 */
	public function getMyfriends()
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.connectfrom, a.connectto');
		$query->from('#__christianconnection as a');
		$query->where('(a.connectfrom ='.$currentuser.' OR a.connectto= '.$currentuser.')' );
		$query->where('a.status = '.(int)'1');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$friends = array();
		if((int)($result[0]->connectfrom) > 0)
		{
			$friends = $this->removerepetation($result);
		}
		$this->friendProfiledata = array();
		//finding friend profile data.....according to id..
		if(count($friends))
		{
			$this->friendProfiledata = $this->getmyFriendsProfile($friends);
		}
		$mainframe = JFactory::getApplication();
		// Get pagination request variables from configuration file.....
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->setState('limit', JRequest::getVar('limit', $limit, '', 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		$this->_totalresult = count($this->friendProfiledata);
		if ($this->getState('limit') > 0) {
			$this->friendProfiledata    = array_splice($this->friendProfiledata , $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->friendProfiledata;
	}
	/*
	 * function for custom pagination.....
	 */
	public function getPagination()
	{
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}
	/*
	 * total result of friends.
	 */
	public function getTotal()
	{
		return $this->_totalresult;
	}
	/*
	 * removing repetation from friends arry,,,,,
	 */

	private function removerepetation($result)
	{
		$uniquedata = array();
		foreach($result as $data)
		{
			$uniquedata[] = $data->connectfrom;
			$uniquedata[] = $data->connectto;
		}
		$finalarray = array_unique($uniquedata);
		return $finalarray;
	}
	/*
	 * function for getting the serached friend and also not block by admin.....
	 */
	private function getmyFriendsProfile($friends)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$friendsstring = implode(',', $friends);
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, b.*');
		$query->from('#__users as a');
		$query->join('INNER', $db->quoteName('#__christianusers').' AS b ON a.id = b.userid');
		$query->where('a.block = '.'0');
		$query->where('a.id IN ('.$friendsstring.')');
		$query->where('a.id != '.(int)$currentuser);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	/*
	 * function for getting the serached friend and also not block by admin.....
	 */
	public function getFriends($friends)
	{
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, b.*');
		$query->from('#__users as a');
		$query->join('INNER', $db->quoteName('#__christianusers').' AS b ON a.id = b.userid');
		$query->where('a.block = '.'0');
		$query->where('LOWER(a.name)'.'  like "%'.$friends.'%"');
		$query->where('a.id != '.(int)$currentuser);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	/*
	 * function for seeing the profile fileds....
	 */
	public function viewprofile($userid)
	{
		$check = $this->checkfriend($userid);
		$friend = '';
		if((int)$check !=0 || !empty($check)){$friend = ',3'; } // 3 cloumn access for friend accessibilty.......
		$columnfind = $this->checkcolumn($userid, $friend);
		$columnname = array();
		$j =0;
		$k=0;
		for($i=0; $i<count($columnfind); $i++){
			if($columnfind[$i] == 'localchurch'){$j = 1;}
			if($columnfind[$i] == 'otherchurch'){$k = 1;}
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query = 'select '. $columnfind[$i].' from #__christianusers where userid='.(int)$userid;
			$db->setQuery($query);
			$columnname[] = $db->loadObject();
		}
	 //checking if localchurch/otherchurchs are coming in column by below two if blocks....
		if($j == 1) {
			$localchurch = $this->getUserlocalChurch($userid);
			$columnname[] = $localchurch;
		}
		if($k ==1) {
			$otherchurch = $this->getUserotherChurch($userid);
			$columnname[] = $otherchurch;
		}
		//converting index based array to associated array, we will get it by key name on view file.......
		foreach($columnname as $col){
			if(!empty($col)){
				foreach($col as $te=>$value)
				{
		  			$arry[$te] = $value;
				}
			}
		}
		return $arry;
	}
	/*
	 * function for checking friendship........
	 */
	public function checkfriend($userid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.status');
		$query->from('#__christianconnection as a');
		$query->where('((a.connectfrom ='.$currentuser.' AND a.connectto= '.$userid.') OR (a.connectfrom ='.$userid.' AND a.connectto= '.$currentuser.'))' );
		$query->where('a.status = '.(int)'1');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	/*
	 * find all column name........
	 */
	public function checkcolumn($userid, $friend)
	{
		$column = array();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query = 'SHOW COLUMNS FROM #__userprofileaccess';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach($result as $items)
		{
			$column[] = $items->Field;
		}
		$columnresult = $this->columnfind($userid, $column, $friend);
		return $columnresult;
	}
	/*
	 * find filtered column name.....
	 */
	public function columnfind($userid, $column, $friend)
	{
		$columnname = array();
		for($i=0; $i<count($column); $i++){

			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query = 'select '. $column[$i].' from #__userprofileaccess where userid='.(int)$userid. ' AND '.$column[$i].' IN (1'.$friend.')';
			$db->setQuery($query);
			$columnname[] = $db->loadObject();
		}
		$test = array();
		foreach($columnname as $cols)
		{   if(!empty($cols)){
			foreach($cols as $fet=>$value){
				$test[] = $fet;
			}
		}
		}
		return $test;
	}
	/*
	 * getting the user church data function calling from same file..
	 *
	 */
	public function getUserlocalChurch($userid)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id, a.localchurch');
		$query->from('#__userchurch AS a');
		$query->where('a.userid ='.$userid);
		// Get the options.
		$db->setQuery($query);
		$optionsarray = $db->loadObjectList();
		$curchname = $this->getchurchname($optionsarray[0]->localchurch);
		$option = array();
		$option['localchurchname'] = $curchname;
		return $option;
	}
	public function getUserotherChurch($userid)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id, a.otherchurch');
		$query->from('#__userchurch AS a');
		$query->where('a.userid ='.$userid);
		// Get the options.
		$db->setQuery($query);
		$optionsarray = $db->loadObjectList();
		$curchname = $this->getchurchname($optionsarray[0]->otherchurch);
		$option = array();
		$option['otherchurchname'] = $curchname;
		return $option;
	}
	 
	/*
	 * getting church name..
	 */
	private function getchurchname($churchstring)
	{
		$churchid = explode(",", $churchstring);
		$db		= JFactory::getDbo();
		$result = array();
		foreach($churchid as $id)
		{
			$query	= $db->getQuery(true);
			$query->select('a.cname');
			$query->from('#__church AS a');
			$query->where('a.id ='.$id);
			// Get the options.
			$db->setQuery($query);
			$name = $db->loadResult();
			$result[] = $name;
		}
		return $result;
	}
	/*
	 * check users are already friend...........
	 */
	public function checkfriendsship($userid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.status');
		$query->from('#__christianconnection as a');
		$query->where('((a.connectfrom ='.$currentuser.' AND a.connectto= '.$userid.') OR (a.connectfrom ='.$userid.' AND a.connectto= '.$currentuser.'))' );
		$query->where('a.status = '.(int)'1');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	/*
	 * function for cheking the denied request...
	 */
	public function chkdeniedfriend($userid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.status');
		$query->from('#__christianconnection as a');
		$query->where('((a.connectfrom ='.$currentuser.' AND a.connectto= '.$userid.') OR (a.connectfrom ='.$userid.' AND a.connectto= '.$currentuser.'))' );
		$query->where('a.status = '.(int)'2');
		$db->setQuery($query);
		$result1 = $db->loadObjectList();
		return $result1;
	}
	/*
	 *function for checking request already sent....
	 */
	public function requestalreadysent($userid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.status');
		$query->from('#__christianconnection as a');
		$query->where('(a.connectfrom ='.$currentuser.' AND a.connectto= '.$userid.')' );
		$query->where('a.status = '.(int)'0');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	/*
	 * function for sending friend request........
	 */
	public function sendrequest($userid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//checking if a current user is already inivited by the requesting user........
		$query->select('a.id, a.status');
		$query->from('#__christianconnection as a');
		$query->where('(a.connectfrom = '. $userid. ' AND a.connectto = '.$currentuser.')');
		$query->where('a.status ='.(int)0);
		$db->setQuery($query);
		$result = $db->loadObjectList();

	 if($result[0]->status == (int)0 && (int)$result[0]->id > 0)
	 {
	 	return 1;
	 }
	 else{
		 $data['connectfrom'] = $currentuser;
		 $data['connectto'] = $userid;
		 $data['status'] = 0;
		 $data['msg'] = JText::_('COM_MYFRIEND_GRETTING_MSG');
		 $date =& JFactory::getDate();
		 $data['requestsent'] = $date->toFormat();
		 $table =& JTable::getInstance('christianconnection','MyfriendTable');
		 if(!$table->save($data))
		 {
		 	return 0;
		 }
		 else
		 {
		 	return 2;
		 }
	 }
	}
	/*
	 * function for accepting friend request.....
	 */
	public function acceptrequest($requestid, $connectfrom)
	{
	 //getting current loggedin user....
	 $currentuser = JFactory::getUser()->id;
	 $db = JFactory::getDbo();
	 $query = $db->getQuery(true);
	 $data['id'] = (int)$requestid;
	 $data['status'] = 1;
	 $date =& JFactory::getDate();
	 $data['requestaccept'] = $date->toFormat();
	 $table =& JTable::getInstance('christianconnection','MyfriendTable');
	 if(!$table->save($data))
	 {
	 	return 0;
	 }
	 else
	 {
	 	return $table->id;
	 }
	}
	/*
	 * function for rejecting the friendrequest.....
	 */
	public function rejectrequest($rejectid)
	{
	 $db = JFactory::getDbo();
	 $query = $db->getQuery(true);
	 $data['id'] = (int)$rejectid;
	 $data['status'] = 2;
	 $table =& JTable::getInstance('christianconnection','MyfriendTable');
	 if(!$table->save($data))
	 {
	 	return 0;
	 }
	 else
	 {
	 	return $table->id;
	 }
	}
	/*
	 * function for removing friendship...........
	 */
	public function removefriendhip($removeid)
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = 'DELETE FROM #__christianconnection where connectfrom = '.(int)$removeid. ' AND connectto = '.$currentuser;
		$db->setQuery($query);
		$db->query();
		$query1 = '';
		//removing if currentuser is request sender of friendship request....
		$query1 = 'DELETE FROM #__christianconnection where connectfrom = '.$currentuser. ' AND connectto = '.(int)$removeid;
		$db->setQuery($query1);
		$db->query();
		return 1;
	}
	/*
	 * function for sending the message to friend......
	 */
	public function sendMessageToFriend($friendid, $messagesubject, $messagebody)
	{
	 //getting current loggedin user....
	 $currentuser = JFactory::getUser()->id;
	 $db = JFactory::getDbo();
	 $query = $db->getQuery(true);
	 $data['messagefrom'] = $currentuser;
	 $data['messageto'] = (int)$friendid;
	 $data['subject'] = $messagesubject;
	 $data['body'] = $messagebody;
	 $date =& JFactory::getDate();
	 $data['postedon'] = $date->toFormat();
	 $table =& JTable::getInstance('christianmessages','MyfriendTable');
	 if(!$table->save($data))
	 {
	 	return 0;
	 }
	 else
	 {
	 	return $table->id;
	 }
	}
	/*
	 * function for finding data of a message send by current loggen- in user.......
	 */
	public function friendMessage($messageid, $messagesender, $parentid)
	{
	 //getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if((int)$parentid >0 && $parentid != '')
		{
			$query->select('a.id as messageid, a.parentid, a.replyfrom as messagefrom, a.replyto as messageto, a.body, a.replyon as postedon, a.subject, c.fname,c.email, c.profileimage');
			$query->from('#__christianchildmessages as a');
			$query->join('INNER', '#__christianusers AS c ON a.replyfrom = c.userid');
			$query->join('INNER', '#__users AS b ON a.replyto = b.id');
			$query->where('a.replyto= '.$currentuser);
			$query->where('a.id = '.(int)$messageid);
			$query->where('a.isdelete != '.(int)'1');
		}
		else{
			$query->select('a.id as messageid, a.messagefrom, a.messageto, a.subject, a.body, a.postedon, c.fname,c.email, c.profileimage');
			$query->from('#__christianmessages as a');
			$query->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
			$query->join('INNER', '#__users AS b ON a.messageto = b.id');
			$query->where('a.messageto= '.$currentuser);
			$query->where('a.messagefrom= '.$messagesender);
			$query->where('a.id = '.(int)$messageid);
		}
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		// Updating status of the method from unread to read......
		$this->updatemessagestatus($messageid, $parentid);
		return $result;
	}
	/*
	 * function for update the message state(unread to read)....
	 */
	private function updatemessagestatus($messageid, $parentid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data['id'] = $messageid;
		$data['parentid'] = $parentid;
		$data['status'] = 1;
		if((int)$parentid >0 && $parentid != '')
		{
			$table =& JTable::getInstance('christianchildmessages','MyfriendTable');
			$table->save($data);
		}
		else{
			$table =& JTable::getInstance('christianmessages','MyfriendTable');
			$table->save($data);
		}
		return true;
	}
	/*
	 * function for saving the message reply...
	 */
	public function friendMessagereply($data)
	{   
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$data['replyfrom'] = $currentuser;
		$date =& JFactory::getDate();
		$data['replyon'] = $date->toFormat();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$table =& JTable::getInstance('christianchildmessages','MyfriendTable');
		if(!$table->save($data))
		{
		 return 0;
		}
		else
		{
		 return $table->id;
		}
	}
	/*
	 * finding parent message data....
	 */
	public function ParentMessageData($messageid)
	{
	 //getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as messageid, a.messagefrom, a.messageto, a.subject, a.body, a.postedon, c.fname,c.email, c.profileimage');
		$query->from('#__christianmessages as a');
		$query->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
		$query->join('INNER', '#__users AS b ON a.messageto = b.id');
		$query->where('a.id = '.(int)$messageid);
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
	/*
	 * function for finding all messages......
	 */
	public function allmessages()
	{
		//getting current loggedin user....
		$currentuser = JFactory::getUser()->id;
		$db = JFactory::getDbo();
		$result = array();
		$result1 = array();
		$query = $db->getQuery(true);
		$query1 = $db->getQuery(true);
		$query->select('a.id as messageid, a.messagefrom, a.messageto, a.status, a.subject, a.postedon, c.fname,c.email, c.profileimage');
		$query->from('#__christianmessages as a');
		$query->join('INNER', '#__christianusers AS c ON a.messagefrom = c.userid');
		$query->join('INNER', '#__users AS b ON a.messageto = b.id');
		$query->join('INNER', '#__christianconnection AS cc ON ((a.messageto = cc.connectfrom OR a.messageto = cc.connectto) AND (a.messagefrom = cc.connectfrom OR a.messagefrom = cc.connectto))');
		$query->where('a.messageto= '.$currentuser);
		$query->where('a.isdelete != '.(int)'1');
		$query->where('cc.status = '.(int)'1');		
		$query->order('a.postedon DESC');
		$db->setQuery($query); 
		$result = $db->loadObjectList(); 

		// Now finding the child messages from child messages table........
		$query1->select('a.id as messageid, a.parentid, a.replyfrom as messagefrom,  a.status, a.replyto as messageto, a.replyon as postedon, a.subject, c.fname,c.email, c.profileimage');
		$query1->from('#__christianchildmessages as a');
		$query1->join('INNER', '#__christianusers AS c ON a.replyfrom = c.userid');
		$query1->join('INNER', '#__users AS b ON a.replyto = b.id');
		$query1->join('INNER', '#__christianconnection AS cc ON ((a.replyto = cc.connectfrom OR a.replyto = cc.connectto) AND (a.replyfrom = cc.connectfrom OR a.replyfrom = cc.connectto))');
		$query1->where('a.replyto= '.$currentuser);
		$query1->where('a.isdelete != '.(int)'1');
		$query1->where('cc.status = '.(int)'1');
		$query1->order('a.replyon DESC');
		$db->setQuery($query1); 
		$result1 = $db->loadObjectList(); 
		//merging two array and short them ok key postedon basis after converting it in strtotime..
		$finalarray = array_merge($result, $result1); 
		$postedon = array();
		foreach ($finalarray as $key => $row)
		{
			$postedon[$key] = strtotime($row->postedon);
		}
		array_multisort($postedon, SORT_DESC, $finalarray);
		return $finalarray;

	}
		/*
	 * delete a message........making its status 1....
	 */
	public function deletemessage($data)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data1 = array();
		$data1['id'] = (int)$data['messageid'];
		$data1['parentid'] = $parentid = (int)$data['parentid'];
		$data1['isdelete'] = 1; 
		if($parentid >0 && $parentid != '')
		{
			$table =& JTable::getInstance('christianchildmessages','MyfriendTable');
			$table->save($data1);
		}
		else{
			$table =& JTable::getInstance('christianmessages','MyfriendTable');
			$table->save($data1);
		}
		return 1;
	}
	/*
	 * function for making a message unread....
	 */
	public function unreadmessage($data)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data2 = array();
		$data2['id'] = (int)$data['messageid'];
		$data2['parentid'] = $parentid = (int)$data['messageparentid'];
		$data2['status'] = 0;
		if((int)$parentid >0 && $parentid != '')
		{
			$table =& JTable::getInstance('christianchildmessages','MyfriendTable');
			$table->save($data2);
		}
		else{
			$table =& JTable::getInstance('christianmessages','MyfriendTable');
			$table->save($data2);
		}
		return $table->id;
	}
}
