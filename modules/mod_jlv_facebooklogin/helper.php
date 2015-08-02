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
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianusers.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianuserstype.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userprofileaccess.php');
class modJLVFacebookLoginHelper
{
	static function getReturnURL($params, $type)
	{
		$app	= JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		if ($itemid =  $params->get($type))
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName('link'));
			$query->from($db->quoteName('#__menu'));
			$query->where($db->quoteName('published') . '=1');
			$query->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult()) {
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}
		if (!$url)
		{
			// stay on the same page
			$uri = clone JFactory::getURI();
			$vars = $router->parse($uri);
			unset($vars['lang']);
			if ($router->getMode() == JROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = $app->getMenu();
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query) {
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JURI::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JURI::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JURI::buildQuery($vars);
			}
		}
		$url = str_replace('fb=login','',$url);
		$url = str_replace('?&','?',$url);
		return base64_encode($url);
	}

	static function getType()
	{
		$user = JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}
	
	function addJoomlaUser($name, $username, $password, $email) { 
		jimport('joomla.application.component.helper');
		$config	= JComponentHelper::getParams('com_users');
		$defaultUserGroup = $config->get('new_usertype', 2);
		$data = array(
            "name"=>$name, 
            "username"=>$email, 
            "password"=>$password,
            "password2"=>$password,
			"groups"=>array($defaultUserGroup),
            "email"=>$email
        );

        $user = clone(JFactory::getUser());
        if(!$user->bind($data)) {
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }
        if (!$user->save()) {
            throw new Exception("Could not save user. Error: " . $user->getError());
        }
        return $user;
	}
	function getJoomlaId($email) {
		$db		= JFactory::getDbo();
		$query = "SELECT id FROM #__users WHERE email='".$email."';";
		$db->setQuery($query);
		$ejuser = $db->loadResult();
		return $ejuser;
	}
	function loginFb($fbuser,$loginredirect,$userdata) {
		$db		= JFactory::getDbo();
		
		//get ChristianUsers table object
		$table = JTable::getInstance( 'ChristianUsers', 'UsersTable' );
		
		$query = "SELECT password FROM #__users WHERE id='".$fbuser->id."';";
		$db->setQuery($query);
		$oldpass = $db->loadResult();

		jimport( 'joomla.user.helper' );
		$password = JUserHelper::genRandomPassword(5);
		$query = "UPDATE #__users SET password='".md5($password)."' WHERE id='".$fbuser->id."';";
		$db->setQuery($query);
		$db->query();
		$app = JFactory::getApplication();
		$credentials = array();
		$credentials['username'] = $fbuser->username;
		$credentials['password'] = $password;
		$options = array();
		$options['remember']	= true;
		$options['silent']		= true;
		$app->login($credentials, $options);
		$query = "UPDATE #__users SET password='".$oldpass."' WHERE id='".$fbuser->id."';";
		$db->setQuery($query);
		$db->query();

		$query = "SELECT id from #__christianusers where userid=".$db->Quote($fbuser->id);
		$result = $db->setQuery($query);
		$db->query();
		$userid = $db->loadResult();
		
		//update userinfo
		$userdata['id'] = $userid;
		$userdata['userid'] = $fbuser->id;
		$table->save($userdata);

		$query = "SELECT count(id) from #__christianusers where userid=".$db->Quote($fbuser->id);
		$result = $db->setQuery($query);
		$db->query();
		$usercount = $db->loadResult();
		if($usercount == 0){
			//$query = "insert into "
		}

		//get ChristianUsersTypes table object
		$row =& JTable::getInstance( 'Christianuserstype', 'UsersTable' );
		//saving the data into the christianuserstype table....for saving the FB id and usertype..
		$query1 = "SELECT id from #__christianuserstype where userid=".$db->Quote($fbuser->id);
		$result = $db->setQuery($query1);
		$db->query();
		$rowid = $db->loadResult();
		$userdatatype['userid'] = $fbuser->id;
		$userdatatype['fbtwitterid'] = $userdata['fbid'];
		$userdatatype['type'] = 'FB';
		$row->load($rowid);
		  if (!$row->save($userdatatype)) { 

		  } 		
			/*
			 * below code for saving the access level for the user profile field in Userprofileaccess table...
			 * we are saving the access of the fields according to the user id here.....
			 *
			 */
		  
		$christianuserprofileaccess =& JTable::getInstance('Userprofileaccess','UsersTable'); 
		$query2 = "SELECT id from #__userprofileaccess where userid=".$db->Quote($fbuser->id);
		$db->setQuery($query2);
		$db->query();
		$rowid1 = $db->loadResult();
		
		$accessdata['userid'] = $fbuser->id;
		$christianuserprofileaccess->load($rowid1);
		
		if (!$christianuserprofileaccess->save($accessdata)) { 

		  } 
		  
		$app->redirect(base64_decode($loginredirect));
	}
//finding the country code for saving by country name...
	
	public function findcountry($country)
	{
		$db		= JFactory::getDbo();
		
		$query = "SELECT ccode FROM #__countries WHERE country='".$country."';";
		$db->setQuery($query);
		$countrycode = $db->loadResult(); 
		return $countrycode;
	}
/*
 * 
 * Function added for Lat and Long...
 */	
    public function gettingLatLon($address) {
        $address = str_replace(" ", "+", $address);
        $output = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=false", true);
        $resultInArray = json_decode(utf8_encode($output), true);
      
        return $resultInArray['results'][0]['geometry']['location'];
    }
	
}
