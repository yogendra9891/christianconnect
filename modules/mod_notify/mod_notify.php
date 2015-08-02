<?php
//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

//This is the parameter we get from our xml file above
$userCount = $params->get('usercount');

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
//getting current loggedin user..
$user = JFactory::getUser()->id; 
//finding the new friend request for logged in user...
$friendrequest = modNotifyHelper::getRequestNotification($user);
//finding the new messages for logged in user...
$friendmessages = modNotifyHelper::getMessagesNotification($user); 
//Returns the path of the layout file
require JModuleHelper::getLayoutPath('mod_notify', $params->get('layout', 'default'));
?>
