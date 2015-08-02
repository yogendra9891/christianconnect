<?php
/**
 * @version     1.0.0
 * @package     com_myfriend
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      yogendra <yogendra.singh@daffodilsw.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Myfriendss list controller class.
 */
class MyfriendControllerMyfriendss extends MyfriendController
{
   public function __construct($config = array())
    {   
       $user = JFactory::getUser(); 
       parent::__construct($config);
       if($user->id == 0 || $user->id == '')
        {
          $msg = JText::_( 'Please Login First.....' );
          $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false), $msg);
        }
   }
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Myfriendss', $prefix = 'MyfriendModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
 /*
   * function for searching friends.......
   */	
	public function searchfriend()
	{
	     $friend = JRequest::getVar('searchfriend');
	     $model = $this->getModel();
         $mainframe = JFactory::getApplication();
         // Get pagination request variables
         $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
 	     $model->setState('limit', JRequest::getVar('limit', $limit, '', 'int'));
	     $model->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		 $this->result = $model->getFriends($friend); 
		 $this->_total = count($this->result); 
	     if ($model->getState('limit') > 0) {
	       $this->result    = array_splice($this->result , $model->getState('limitstart'), $model->getState('limit'));
	     }	 
	    jimport('joomla.html.pagination'); 
	    $this->_pagination = new JPagination($this->_total, $model->getState('limitstart'), $model->getState('limit') );
	    $pagination = $this->_pagination;  
		$view = $this->getView('Myfriendss', 'html');
		$view->assign('items', $this->result);
		$view->assign('pagination', $pagination);
	    $view->setLayout('friends');
	    $view->friends();
	}
  /*
   * function for seeing profile ofsearched friend........
   */
	public function viewprofile()
	{
	 $userid = JRequest::getVar('userid');
	 $model = $this->getModel();
	 $results = $model->viewprofile($userid);
	 $view = $this->getView('Myfriendss', 'html');
	 $view->assign('items', $results);
	 $view->assign('userid', $userid);
	 $view->setLayout('viewprofile');
     $view->viewprofile();
	}
/*
 * function for checking friendship........
 */		
	public function checkfriendship()
	{
	 $userid = JRequest::getVar('userid');
	 $currentuser = JFactory::getUser()->id;
	 $model = $this->getModel();
	 //checking friendship.....if already friend....
     $friendshipid = $model->checkfriendsship($userid); 
     if($friendshipid[0]->status == (int)1 && (int)$friendshipid[0]->id > 0){ 
	 $this->setMessage(JText::_('COM_MYFRIEND_ALREADY_FRIEND'));}
	 else{
	 //checking friend request already sent....
	 $requestsent = $model->requestalreadysent($userid);
	 if($requestsent[0]->status == (int)0 && (int)$requestsent[0]->id > 0)
	 { 
		 $this->setMessage(JText::_('COM_MYFRIEND_REQUEST_ALREADY_SENT'));
	 }	 
	 else{
	 	//sending request or checking if current user is already inivited...
	 	$id = $model->sendrequest($userid);
	 	if($id == 1)$this->setMessage(JText::_('COM_MYFRIEND_ALREADY_INVITED'));
	 	elseif($id == 2) $this->setMessage(JText::_('COM_MYFRIEND_REQUEST_SENT'));
	 	else $this->setMessage(JText::_('COM_MYFRIEND_REQUEST_NOT_SENT'));
	  }
	 }
	$this->setRedirect(JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewprofile&userid='.$userid.'&tmpl=component', false)); 
	}
/*
 * function for accepting friend request....
 */	
	public function acceptrequest()
	{ 
	 $requestid = JRequest::getVar('requestid');
	 $connectfrom = JRequest::getVar('connectfrom');
	 $model = $this->getModel();
	 $id = $model->acceptrequest($requestid, $connectfrom);
	 echo $id;
	 exit;
	}
/*
 * function for denied friend request....
 */	
	public function denyrequest()
	{ 
	 $rejectid = JRequest::getVar('rejectid');
	 $model = $this->getModel();
	 $id = $model->rejectrequest($rejectid);
     echo $id; exit;
	}
/*
 * function for remove friendship.....
 */	
	public function removefriendhip()
	{
	 $removeid = JRequest::getVar('userid');
	 $model = $this->getModel();
	 $id = $model->removefriendhip($removeid);
	 echo $id;
	 exit;
	}
/*
 * function for sending the message....
 */
	public function sendMessage()
	{
	 $friendid = JRequest::getVar('userid');
	 $messagesubject = JRequest::getVar('messagesubject');
	 $messagebody = JRequest::getVar('messagebody');
	 $model = $this->getModel();
	 $id = $model->sendMessageToFriend($friendid, $messagesubject, $messagebody);	 
	 echo $id; 
	 exit;
	}	
/*
 * function for friend message read......
 */
	public function friendmessage()
	{
	 $messageid = JRequest::getVar('messageid');
	 $messagesender = JRequest::getVar('messagefrom');
	 $threadid = JRequest::getVar('parentid');	
	 $model = $this->getModel(); 
	 $messagedata = $model->friendMessage($messageid, $messagesender, $threadid); 
	 $view = $this->getView('Myfriendss', 'html');
	 $view->assign('messagedata', $messagedata);
	 $view->setLayout('messages');
	 $view->message();
	}
/*
 * function for reply of a message by current loggedin user by ajax call from messages view....
 */
	public function messagereply()
	{
	 $data = JRequest::get('post'); 
	 $model = $this->getModel();
	 $id = $model->friendMessagereply($data);
	}
/*
 * function for showing all messages..
 */	
	public function allmessage()
	{ 
	 $model = $this->getModel();
     $mainframe = JFactory::getApplication();
     // Get pagination request variables
     $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
 	 $model->setState('limit', JRequest::getVar('limit', $limit, '', 'int'));
	 $model->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
	 
	 $allmessagedata = $model->allmessages();
	 $this->_total = count($allmessagedata); 
	 if ($model->getState('limit') > 0) {
	       $allmessagedata = array_splice($allmessagedata , $model->getState('limitstart'), $model->getState('limit'));
	  }	 
	 jimport('joomla.html.pagination'); 
	 $this->_pagination = new JPagination($this->_total, $model->getState('limitstart'), $model->getState('limit') );
	 $pagination = $this->_pagination;  
	 $view = $this->getView('Myfriendss', 'html'); 
	 $view->assign('messagedata', $allmessagedata);
	 $view->assign('pagination', $pagination);	 
	 $view->setLayout('allmessages');
	 $view->message();
	}
/*
 * delete a message.......
 */	
	public function deletemessage()
	{
	 $data = JRequest::get('post'); 
	 $model = $this->getModel();
	 $id = $model->deletemessage($data);
	 echo $id;
	 exit;	
	}
/*
 * fucntion for making a message as unread by ajax call and redirect to message inbox page......
 */	
	public function unreadmessage()
	{
	 $data = JRequest::get('post'); 
	 $model = $this->getModel();
	 $id = $model->unreadmessage($data);
	 echo $id;
	 exit;	
	}
/*
 * function for seeing profile of a reuested user before accepting request.......
 */
	public function viewrequestprofile()
	{
	 $userid = JRequest::getVar('userid');
	 $requestid = JRequest::getVar('requestid');	 
	 $friendsuccess = JRequest::getVar('friendsuccess'); //echo $friendsuccess; exit;
	 $model = $this->getModel();
	 $results = $model->viewprofile($userid);
	 //check friendship.......
	 $friend = $model->checkfriend($userid);
	 //check if request is denied........
	 $deinedfriend = $model->chkdeniedfriend($userid);
	 $view = $this->getView('Myfriendss', 'html'); 
	 $view->assign('items', $results);
	 $view->assign('userid', $userid);
	 $view->assign('requestid', $requestid);
	 $view->assign('friend', $friend);	
	 $view->assign('deinedfriend', $deinedfriend); 	 
	 $view->setLayout('viewrequestprofile');
	 if((int)$friendsuccess)
	 {
	  $friendsuccess = JRequest::setVar('friendsuccess', '');
	  $app =& JFactory::getApplication(); 
	  $app->enqueueMessage(JText::_('COM_MYFRIEND_REQUEST_ACCEPTED'));
	 }
     $view->viewprofile();
	}
}