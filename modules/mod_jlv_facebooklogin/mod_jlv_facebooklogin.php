<?php
/**
 * JLV Facebook Login Module
 * @version 2.5.6
 * @author Le Xuan Thanh
 * @website http://joomla.name.vn
 * @Copyright (C) 2010 - 2012 joomla.name.vn. All Rights Reserved.
 * @license GNU General Public License version 2, see LICENSE.txt or http://www.gnu.org/licenses/gpl-2.0.html
 * fanpage: https://www.facebook.com/jlvextension
 * youtube: http://www.youtube.com/jlvextension
 * twitter: https://twitter.com/jlvextension
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once dirname(__FILE__).'/src/facebook.php';

$params->def('greeting', 1);
$appId = $params->get('appId', '');
$secret = $params->get('secret', '');

$loading = $params->get('loading', 1);
$loading_msg = $params->get('loading_msg', JText::_('MOD_JLVFACEBOOKLOGIN_LOADING_MSG'));
$modalwidth = $params->get('modalwidth', 400);
$modalheight = $params->get('modalheight', 60);

$facebookimage = $params->get('facebookimage', '');

$loginform = $params->get('loginform', 1);
$forgot = $params->get('forgot', 1);
$forgotuser = $params->get('forgotuser', 1);
$create = $params->get('create', 1);

$user = JFactory::getUser();

$type	= modJLVFacebookLoginHelper::getType();
$return	= modJLVFacebookLoginHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();

$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $secret,
));
$fbuser = $facebook->getUser();

if ($fbuser && $user->guest && isset($_GET['fb']) && $_GET['fb']=='login' ) {

	try {
		$user_profile = $facebook->api('/me'); 
		//get user fb detail
		$fields	= array('first_name' , 'last_name' , 'birthday_date' , 'current_location' , 'status' , 'pic' , 'sex' , 'name' , 'pic_square' , 'profile_url' , 'pic_big');
		$connectId	= $facebook->getUser();
		$userInfo	= $facebook->getUserInfo( $fields , $connectId );

		$userdata['fname'] = $userInfo['first_name'];
		$userdata['lname'] = $userInfo['last_name'];
		$userdata['dob'] = $userInfo['birthday_date'];
		$userdata['city'] = $userInfo['current_location']['city'];
		$userdata['state'] = $userInfo['current_location']['state'];
		//finding country code..
		$userdata['country'] = modJLVFacebookLoginHelper::findcountry($userInfo['current_location']['country']);
		$address = $userInfo['current_location']['city']." ".$userInfo['current_location']['state']." ".$userInfo['current_location']['country'];
		//finding lat and long from city, state and country..
		$locationdata = modJLVFacebookLoginHelper::gettingLatLon($address);
		$userdata['lat'] = $locationdata['lat'];
		$userdata['lng'] = $locationdata['lng'];
		
		$userdata['profileimage'] = $userInfo['pic'];
		$userdata['gender'] = $userInfo['sex'];
		$userdata['fbid'] = $user_profile['id'];
		$userdata['email'] = $user_profile['email'];

		$isjoomlauser = modJLVFacebookLoginHelper::getJoomlaId($user_profile['email']);
		
		
		if((int)$isjoomlauser==0) {
			jimport( 'joomla.user.helper' );
			$password = JUserHelper::genRandomPassword(5);
			$joomlauser = modJLVFacebookLoginHelper::addJoomlaUser($user_profile['name'], $user_profile['username'], $password, $user_profile['email']);
		}
		else {
			$joomlauser = JFactory::getUser($isjoomlauser);
		}
		modJLVFacebookLoginHelper::loginFb($joomlauser,$return,$userdata);

	}
	catch (FacebookApiException $e) {
		error_log($e);
		$fbuser = null;
	}
}

require JModuleHelper::getLayoutPath('mod_jlv_facebooklogin', $params->get('layout', 'default'));
