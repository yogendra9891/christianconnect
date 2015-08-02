<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userprofileaccess.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianusers.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userchurch.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianuserstype.php');
require_once(JPATH_ROOT.DS.'components'.DS.'com_slogin'.DS.'tables'.DS.'slogin_users.php');
define('LOGFILEPATH', getcwd().'/errorfile.txt'); 
/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelRegistration extends JModelItem
{
	   //const LOGFILEPATH= '/opt/lampp/htdocs/christianconnect/errorfile.txt';/*log file path*/

	    /*
	     * function for register a user...
	     * @params email, firstName, lastName, password..
	     */
		 public function Register($userdata){ 
		 	$db=JFactory::getDBO();
			$config = JFactory::getConfig();
			$db		= $this->getDbo();
			$params = JComponentHelper::getParams('com_users');
	
			// Initialise the table with JUser.
			$user = new JUser;
			// Prepare the data for the user object.
			//make username to email..
			$data['username']	= $userdata['email'];
			$data['email']		= $userdata['email'];
	    	$data['groups']     = array('2');/* we assign user group. 2 is used for app user*/
			$data['password']	= $realpassword = base64_decode($userdata['password']);
			$data['password2']	= base64_decode($userdata['password']); /*because joomla need password2 in library file*/
			$data['name'] = $userdata['firstName']. ' '.$userdata['lastName']  ;
			// Check if the user needs to activate their account.
			if (($useractivation == 1) || ($useractivation == 2)) {
				$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
				$data['block'] = 1;
			}
			
			//check if email id already exits..
			 $userexists = $this->checkuser($data['username']);
			 //checking the email is already exists, if not then if clause execute o/w exit from above function "checkuser" called..
			 if($userexists){
			 	if((int)$userdata['isSocialAuth'] == 0){	
					// Bind the data.
					if (!$user->bind($data)) { 
						error_log("Error::".JText::_('REGISTRATION_DATA_BIND_ERROR')."\n",3, LOGFILEPATH);
					}
					// Load the users plugin group.
					JPluginHelper::importPlugin('user');
					// Store the data.
					if (!$user->save()) { 
						error_log("Error::".JText::_('REGISTRATION_DATA_SAVE_ERROR')."\n",3, LOGFILEPATH);
					}
					else{
					      $this->saveotherinfo($userdata, $user->id);
					    }	
					if ($useractivation == 1){
						 $joomlaregisteruser = JFactory::getUser($user->id);
						 //now login to registered user,....
						 $userloggeddata =  $this->loginuser($joomlaregisteruser, $realpassword);	
						 return $userloggeddata;
					    }
					elseif ($useractivation == 2){
						 $joomlaregisteruser = JFactory::getUser($user->id);
						 //now login to registered user,....
						 $userloggeddata =  $this->loginuser($joomlaregisteruser, $realpassword);	
						 return $userloggeddata;
					    }
					else{
					         $joomlaregisteruser = JFactory::getUser($user->id);
						 //now login to registered user,....
						 $userloggeddata =  $this->loginuser($joomlaregisteruser, $realpassword);	
						 $this->sendEmailRegistartion($user->id);
						 return $userloggeddata;
					    }
			 	}
			 	elseif((int)$userdata['isSocialAuth'] == 1){
			 		//social(Facebook and Twitter) registration..
			 		$chkregisparams =$this->chksocialparams($userdata);
			 		//checking this if a user already register by this socialid and forgot that , user already register to the site by this socialid.for more secure registration..
			 		$chksocialuser = $this->checksocialuser($userdata);
			 		if($chksocialuser != 0 && $chksocialuser>0){
						error_log("Error::".JText::_('SOCIALID_INUSE_ERROR')."\n",3, LOGFILEPATH);
						echo $this->responseData(JText::_('ERRCODE_SOCIALID_INUSE_ERROR'),  JText::_('SOCIALID_INUSE_ERROR'),null);
						exit;
			 		}
					// Bind the data.
					if (!$user->bind($data)) { 
						error_log("Error::".JText::_('REGISTRATION_DATA_BIND_ERROR')."\n",3, LOGFILEPATH);
					}
					// Load the users plugin group.
					JPluginHelper::importPlugin('user');
					// Store the data.
					if (!$user->save()) { 
						error_log("Error::".JText::_('REGISTRATION_DATA_SAVE_ERROR')."\n",3, LOGFILEPATH);
						return 0;
					}
					else{
					      $this->saveotherinfo($userdata, $user->id);
					      //save the social id into other tables..
					      $this->savesocialinfo($userdata, $user->id);
						  $joomlauser1 = JFactory::getUser($user->id);
						  //now login to registered user,....
						  $userlogindata1 =  $this->loginsocialuser($joomlauser1);	
						  $this->sendEmailRegistartion($user->id);
						  return $userlogindata1;
					    }	
			 	}else{
					error_log("Error::".JText::_('WRONG_SOCIALAUTH_PARAMETER_ERROR')."\n",3,  LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_WRONG_SOCIALAUTH_PARAMETER_ERROR'),  JText::_('WRONG_SOCIALAUTH_PARAMETER_ERROR'),false);
					exit;
			 	}
		   }else{
		   	return null;
		   }	
		}
		 /*
		 * function for checking if a user already exists by this user name..
		 *  @param - username(email)
		 */
		private function checkuser($username){
			$db = JFactory::getDBO();	
			$query = $db->getQuery(true); 
			if(!empty($username))
			{
				// check for existing email
				$query->clear();
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__users'));
				$query->where('email=  ' .$db->quote($username).'OR username='. $db->quote($username));
				$db->setQuery($query);
				$db->query();
				$xid = intval($db->loadResult()); 
				if ($xid)
				{   
					error_log("Error::".JText::_('EMAIL_INUSE_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_EMAIL_INUSE_ERROR'),  JText::_('EMAIL_INUSE_ERROR'),false);
					exit;
				}
			}else{
			 return 0;
			}
			return 1;
		}
		
		/*
		 * function for saving the other info of a user at registration time...
		 */
		private function saveotherinfo($userdata, $userid){
			$christianuser = & JTable::getInstance('ChristianUsers','UsersTable');
			$userdata['userid'] = $userid;
			$userdata['fname'] = $userdata['firstName'];
			$userdata['lname'] = $userdata['lastName'];
			if (!$christianuser->save($userdata)) { 
				error_log("Error::".JText::_('REGISTRATION_OTHER_PROFILE_DATA_SAVE_ERROR')."\n",3, LOGFILEPATH);
			}
			/*
			 * below code for saving the access level for the user profile field in Userprofileaccess table, by default we are saving it public=>1 
			 * only we are saving the registered user id here.....
			 */
		   $christianuserprofileaccess = & JTable::getInstance('Userprofileaccess','UsersTable');
		   $data1 = array();
		   $data1['userid'] = $userid;
		   if (!$christianuserprofileaccess->save($data1)) { 
				error_log("Error::".JText::_('REGISTRATION_OTHER_PROFILE_DATA_SAVE_ERROR')."\n",3, LOGFILEPATH);
		   }
		  return true;	
		}
        /*
         * check social(FB/TW) registration parameters..
         * @params
         * socialId, socialType..
         */
		 private function chksocialparams($socialdata){
			if(array_key_exists('socialId', $socialdata)){
				if($socialdata['socialId'] == ''){
				error_log("Error::".JText::_('BLANK_SOCIALID_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_BLANK_SOCIALID_ERROR'),  JText::_('BLANK_SOCIALID_ERROR'),false);
				exit;}
			}else{
				error_log("Error::".JText::_('SOCIALID_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_SOCIALID_PARAMETER_MISSING_ERROR'),  JText::_('SOCIALID_PARAMETER_MISSING_ERROR'),false);
				exit;
			}
			if(array_key_exists('socialType', $socialdata)){
				if(($socialdata['socialType'] == '')||(($socialdata['socialType'] != 'TW') && ($socialdata['socialType'] != 'FB'))){
					error_log("Error::".JText::_('SOCILATYPE_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_SOCILATYPE_ERROR'),  JText::_('SOCILATYPE_ERROR'),false);
					exit;
				}
			}
			else{
					error_log("Error::".JText::_('SOCILATYPE_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_SOCILATYPE_PARAMETER_MISSING_ERROR'),  JText::_('SOCILATYPE_PARAMETER_MISSING_ERROR'),false);
					exit;
			}
			return true;
		 }
	/*
	 * function for saving the social info (FB/TW id and its type)
	 * $params
	 * data, userid  
	 */	
		 private function savesocialinfo($userdata, $userId){
		 	$data1['fbtwitterid'] = $userdata['socialId'];
		 	$data1['userid'] = $userId;
		 	$data1['type'] = $userdata['socialType'];
	        $christianusertype = & JTable::getInstance('christianuserstype','UsersTable');
		 	if(!$christianusertype->save($data1)){
		 		return 0;
		 	}
		 	//if a user is trying to registaer with twitter account..
		 	if($userdata['socialType'] == 'TW'){
		 	$data2['slogin_id'] = $userdata['socialId'];
		 	$data2['user_id'] = $userId;
		 	$data2['provider'] = 'twitter';
	        $christiansloginusers = & JTable::getInstance('slogin_users','SloginTable');
		 	if(!$christiansloginusers->save($data2)){
		 		return 0;
		 	}
		 	}
		 }
    
	/*
	 * function for login
	 * @params
	 * email,password,isSocialauth
	 */
		public function Login($data){
			$username = $data['email'];
			$password = base64_decode($data['password']);
			$remember = (int)$data['remember'];
			if($remember){
			  $rememberme = 'true';
			} else{
			 $rememberme = 'false';
			}
			//cheking for isSocialAuth..
			if($data['isSocialAuth'] == 1){
				$finalresult = $this->socialLogin($data);
				return $finalresult;
			}
			elseif($data['isSocialAuth'] ==0){
				$this->checkemailpass($data);
			    $check = JFactory::getApplication()->login(array('username'=>$username,'password'=>$password),array('remember'=>$rememberme));
		
			    $user = JFactory::getUser();
			    // Perform the log in.
				if ( true === $check ) {
					error_log("Error::".JText::_('MSG_LOGIN_SUCCESS')."\n",3, LOGFILEPATH);				
					// Success
					//find sessionId ...
					$sessionid = $this->findSessionid($user->id, $user->email);
					$userdata['sessionId'] = $sessionid;
					$userdata['userName'] = $user->email;
					return $userdata;
				} else {
					error_log("Error::".JText::_('MSG_LOGIN_FAIELD')."\n",3, LOGFILEPATH);				
					// Login failed !
					return false;
				}
			}
			else{
				error_log("Error::".JText::_('WRONG_SOCIALAUTH_PARAMETER_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_WRONG_SOCIALAUTH_PARAMETER_ERROR'),  JText::_('WRONG_SOCIALAUTH_PARAMETER_ERROR'),false);
				exit;
			}
		}
       /*
        * function for checking the email and password parameter.
        * @params
        * email,password..
        */
		private function checkemailpass($data){
			if(array_key_exists('email', $data)){
				if($data['email'] == ''){
				error_log("Error::".JText::_('BLANK_EMAIL_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_BLANK_EMAIL_ERROR'),  JText::_('BLANK_EMAIL_ERROR'),false);
				exit;}
			}else{
				error_log("Error::".JText::_('EMAIL_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_EMAIL_PARAMETER_MISSING_ERROR'),  JText::_('EMAIL_PARAMETER_MISSING_ERROR'),false);
				exit;
			}
			if(array_key_exists('password', $data)){
				if($data['password'] == ''){
					error_log("Error::".JText::_('PASSWORD_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_PASSWORD_ERROR'),  JText::_('PASSWORD_ERROR'),false);
					exit;
				}
			}
			else{
					error_log("Error::".JText::_('PASSWORD_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_PASSWORD_PARAMETER_MISSING_ERROR'),  JText::_('PASSWORD_PARAMETER_MISSING_ERROR'),false);
					exit;
			}
		}
		/*
		 * function for socilalogin..
		 * @params
		 * FB/Twitter id and type..
		 */
		private function socialLogin($data){
			if(array_key_exists('socialId', $data)){
				if($data['socialId'] == ''){
				error_log("Error::".JText::_('BLANK_SOCIALID_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_BLANK_SOCIALID_ERROR'),  JText::_('BLANK_SOCIALID_ERROR'),false);
				exit;}
			}else{
				error_log("Error::".JText::_('SOCIALID_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_SOCIALID_PARAMETER_MISSING_ERROR'),  JText::_('SOCIALID_PARAMETER_MISSING_ERROR'),false);
				exit;
			}
			if(array_key_exists('socialType', $data)){
				if(($data['socialType'] == '')||(($data['socialType'] != 'TW') && ($data['socialType'] != 'FB'))){
					error_log("Error::".JText::_('SOCILATYPE_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_SOCILATYPE_ERROR'),  JText::_('SOCILATYPE_ERROR'),false);
					exit;
				}
			}
			else{
					error_log("Error::".JText::_('SOCILATYPE_PARAMETER_MISSING_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_SOCILATYPE_PARAMETER_MISSING_ERROR'),  JText::_('SOCILATYPE_PARAMETER_MISSING_ERROR'),false);
					exit;
			}
			//checking for new user if then send a mesage of new user and its id/type..otherwise login it..
			$checknewuser = $this->checksocialuser($data);
			if($checknewuser != '' || (int)$checknewuser >0){
			 $joomlauser = JFactory::getUser($checknewuser);
			 $userlogindata =  $this->loginsocialuser($joomlauser);	
			 return $userlogindata;
			}
			else{
				$return = new stdClass();
				$return->socialid = $data['socialId'];
				$return->socialtype = $data['socialType'];
				error_log("Success::".JText::_('SOCIAL_NEW_USER_SUCCESS')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_SOCIAL_NEW_USER_SUCCESS'),  JText::_('SOCIAL_NEW_USER_SUCCESS'),$return);
				exit;
			}
		}
		/*
		 * login to a user after user registration...
		 */
		private function loginuser($joomlaregisteruser, $realpassword){
			$app = JFactory::getApplication();
			$credentials = array();
			$credentials['username'] = $joomlaregisteruser->username;
			$credentials['password'] = $realpassword;
			$options = array();
			$options['remember']	= true;
			$options['silent']		= true;
			$check = $app->login($credentials, $options);
			$user = JFactory::getUser();
			 // Perform the log in.
			if ( true === $check ) {
				error_log("Error::".JText::_('MSG_LOGIN_SUCCESS')."\n",3,  LOGFILEPATH);				
				// Success
				//find sessionId ...
				$sessionid = $this->findSessionid($user->id, $user->email);
				$userdata = array();
				$userdata['sessionId'] = $sessionid;
				$userdata['userName'] = $user->email;
				return $userdata;
			} else {
				error_log("Error::".JText::_('MSG_LOGIN_FAIELD')."\n",3, LOGFILEPATH);				
				// Login failed !
				return false;
			}
		}

		/*
		 * login to a social user if user is a existing user...
		 */
		private function loginsocialuser($joomlauser){
			$db		= JFactory::getDbo();
			$query = $db->getQuery(true);	
			$query = "SELECT password FROM #__users WHERE id='".$joomlauser->id."';";
			$db->setQuery($query);
			$oldpass = $db->loadResult();
	
			jimport( 'joomla.user.helper' );
			$password = JUserHelper::genRandomPassword(5);
			$query = "UPDATE #__users SET password='".md5($password)."' WHERE id='".$joomlauser->id."';";
			$db->setQuery($query);
			$db->query();
			$app = JFactory::getApplication();
			$credentials = array();
			$credentials['username'] = $joomlauser->username;
			$credentials['password'] = $password;
			$options = array();
			$options['remember']	= true;
			$options['silent']		= true;
			$check = $app->login($credentials, $options);
			$query = "UPDATE #__users SET password='".$oldpass."' WHERE id='".$joomlauser->id."';";
			$db->setQuery($query);
			$db->query();
			$user = JFactory::getUser();
			 // Perform the log in.
			if ( true === $check ) {
				error_log("Error::".JText::_('MSG_LOGIN_SUCCESS')."\n",3, LOGFILEPATH);				
				// Success
				//find sessionId ...
				$sessionid = $this->findSessionid($user->id, $user->email);
				$userdata = array();
				$userdata['sessionId'] = $sessionid;
				$userdata['userName'] = $user->email;
				return $userdata;
			} else {
				error_log("Error::".JText::_('MSG_LOGIN_FAIELD')."\n",3, LOGFILEPATH);				
				// Login failed !
				return false;
			}
		}
		/*
         * Function for checking the socila type user is exists or not..
         * @params
         * SicialId, type 
         */
		private function checksocialuser($data){
			$socialid = trim($data['socialId']);
			$socialtype = trim($data['socialType']);
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.userid');
		 	$query->from($db->quoteName('#__christianuserstype'). 'AS a');
			$query->join('INNER', $db->quoteName('#__christianusers').' AS b ON b.userid = a.userid');
			$query->join('INNER', $db->quoteName('#__users').' AS c ON c.id = a.userid');			
		 	$query->where("a.fbtwitterid='".$socialid."'");
		 	$query->where("a.type='".$socialtype."'");
		 	$db->setQuery($query); 
		 	$db->query();  
		 	$result = $db->loadResult(); 
		 	return $result;
		}
		/*
		 *function for getting the session id by current logged user by userid.. 
		 *@params
		 *userid, email(for more secure)
		 */
		private  function findSessionid($userid, $email)
		 {
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.session_id');
		 	$query->from('#__session AS a');
		 	$query->where("a.userid=".$userid);
		 	$query->where("a.username=".$db->quote($email));
		 	$db->setQuery($query); 
		 	$sessionid = $db->loadResult();
			if($sessionid != null && $sessionid != '')
				{
					return $sessionid;
				}else{
					return false;
				}
		 	
		 }
		/*
		 * fucntion for forget the password..
		 * @params
		 * emailId
		 */
		 public function ForgetPassword($data){
			$config	= JFactory::getConfig();
		 	// Find the user id for the given email address.
			$db	= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__users'));
			$query->where($db->quoteName('email').' = '.$db->Quote($data['email']));
			// Get the user object.
			$db->setQuery((string) $query);
			$userId = $db->loadResult();
			// Check for a user.
			if (empty($userId)) {
				error_log("Error::".JText::_('EMAIL_WRONG_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_EMAIL_WRONG_ERROR'),  JText::_('EMAIL_WRONG_ERROR'),false);
				exit;
			}
			// Get the user object.
		   $user = JUser::getInstance($userId);
		   			// Make sure the user isn't blocked.
			if ($user->block) {
					error_log("Error::".JText::_('FORGET_PASSWORD_USER_BLOCK_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_USER_BLOCK_ERROR'),  JText::_('FORGET_PASSWORD_USER_BLOCK_ERROR'),false);
					exit;
			  }

			// Make sure the user isn't a Super Admin.
		   if ($user->authorise('core.admin')) {
				error_log("Error::".JText::_('EMAIL_ADMIN_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_ADMIN_ERROR'),  JText::_('EMAIL_ADMIN_ERROR'),false);
				exit;
		   }
			// Set the confirmation token.
			$token = JApplication::getHash(JUserHelper::genRandomPassword()); 
			$salt = JUserHelper::getSalt('crypt-md5');
			$hashedToken = md5($token.$salt).':'.$salt;
	
			$user->activation = $hashedToken;
	
			// Save the user to the database.
			if (!$user->save(true)) {
				error_log("Error::".JText::_('EMAIL__FORGET_CODE_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_EMAIL__FORGET_CODE_ERROR'),  JText::_('EMAIL__FORGET_CODE_ERROR'),false);
				exit;
			} 
		   		// Put together the email template data.
			$data1 = $user->getProperties();
			$data1['fromname']	= $config->get('fromname');
			$data1['mailfrom']	= $config->get('mailfrom');
			$data1['sitename']	= $config->get('sitename');
//			$data1['link_text']	= JRoute::_($link, false, $mode);
//			$data1['link_html']	= JRoute::_($link, true, $mode);
			$data1['token']		= $token;
	
			$subject = JText::sprintf(
				'COM_WEBSERVICE_EMAIL_PASSWORD_RESET_SUBJECT',
				$data1['sitename']
			);
	
			$body = JText::sprintf(
				'COM_WEBSERVICE_EMAIL_PASSWORD_RESET_BODY',
				$data1['sitename'],
				$data1['token']
			);
	
			// Send the password reset request email.
			$return = JFactory::getMailer()->sendMail($data1['mailfrom'], $data1['fromname'], $user->email, $subject, $body);
			// Check for an error.
			if ($return) {
				return true;
			}
			else{
				return false;
			}
			
		 }
		/*
		 * function for forget password confirmation
		 * params
		 * email, veriicationcode, newpassword, newpassword1
		 */ 
		 public function ForgetPasswordConfirm($data){
		 	if(base64_decode($data['newpassword']) != base64_decode($data['newpassword1'])){
				error_log("Error::".JText::_('FORGET_PASSWORD_MATCH_CODE_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_MATCH_CODE_ERROR'),  JText::_('FORGET_PASSWORD_MATCH_CODE_ERROR'),false);
				exit;
		 	}
			$config	= JFactory::getConfig();
		 	// Find the user id for the given email address.
			$db	= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__users'));
			$query->where($db->quoteName('email').' = '.$db->Quote($data['email']));
	
			// Get the user object.
			$db->setQuery((string) $query);
			$userId = $db->loadResult();
			// Check for a user.
			if (empty($userId)) {
				error_log("Error::".JText::_('EMAIL_WRONG_ERROR')."\n",3, LOGFILEPATH);
				echo $this->responseData(JText::_('ERRCODE_EMAIL_WRONG_ERROR'),  JText::_('EMAIL_WRONG_ERROR'),false);
				exit;
			}

			// Find the user id for the given token.
			$db	= $this->getDbo();
			$query1	= $db->getQuery(true);
			$query1->select('activation');
			$query1->select('id');
			$query1->select('block');
			$query1->from($db->quoteName('#__users'));
			$query1->where($db->quoteName('username').' = '.$db->Quote($data['email']));
	
			// Get the user id.
			$db->setQuery((string) $query1);
			$user = $db->loadObject();
	
			// Check for a user.
			if (empty($user)) {
					error_log("Error::".JText::_('FORGET_PASSWORD_USER_NOTFOUND_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_USER_NOTFOUND_ERROR'),  JText::_('FORGET_PASSWORD_USER_NOTFOUND_ERROR'),false);
					exit;
			}
	
			$parts	= explode( ':', $user->activation );
			$crypt	= $parts[0];
			if (!isset($parts[1])) {
					error_log("Error::".JText::_('FORGET_PASSWORD_USER_NOTFOUND_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_USER_NOTFOUND_ERROR'),  JText::_('FORGET_PASSWORD_USER_NOTFOUND_ERROR'),false);
					exit;
			 }
			$salt	= $parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($data['verificationcode'], $salt);
	
			// Verify the token
			if (!($crypt == $testcrypt))
			{
					error_log("Error::".JText::_('FORGET_PASSWORD_CODE_NOTFOUND_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_CODE_NOTFOUND_ERROR'),  JText::_('FORGET_PASSWORD_CODE_NOTFOUND_ERROR'),false);
					exit;
			  }
	
			// Make sure the user isn't blocked.
			if ($user->block) {
					error_log("Error::".JText::_('FORGET_PASSWORD_USER_BLOCK_ERROR')."\n",3, LOGFILEPATH);
					echo $this->responseData(JText::_('ERRCODE_FORGET_PASSWORD_USER_BLOCK_ERROR'),  JText::_('FORGET_PASSWORD_USER_BLOCK_ERROR'),false);
					exit;
			  }
			// Get the user object.
			$user1 = JUser::getInstance($userId);
			// Generate the new password hash.
			$salt		= JUserHelper::genRandomPassword(32);
			$crypted	= JUserHelper::getCryptedPassword(base64_decode($data['newpassword1']), $salt);
			$password	= $crypted.':'.$salt;
	
			// Update the user object.
			$user1->password			= $password;
			$user1->activation		= '';
			$user1->password_clear	= base64_decode($data['newpassword1']);
	
			// Save the user to the database.
			if (!$user1->save(true)) {
				return false;
			}else{
				return true;
			}
		 }
		 /*
		  * function for sending the email after registration of welcome..
		  */
		 private function sendEmailRegistartion($userid){
		 	
	   		$user = JFactory::getUser($userid);
	   		$mail =& JFactory::getMailer();
	 		$app		= JFactory::getApplication();
	 		$root = JURI::base();  		
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$mail->setSubject(JText::_('Welcome to Christian Connect!'));
			$text = 'Hi '.ucfirst($user->name).','.'<br/><br/>Welcome to Christian Connect!'.'<br/><br/>'.'We are proud to have you as the newest member of Christian Connect.'.'<br/><br/>'.
			        'As a registered member of Christian Connect, you can look for churches nearby your location, connect with friends that attend same church. Although you can make new friends by login on our website '.
			        ' '.'<a href="'.$root.'" target="_blank">'.$root.'</a><br/><br/>'.
			        'Your account information is:<br/>'.
			        'User Id: '.$user->email.'<br/><br/>'.
			        'If at any time you forget your password, you can retrieve it by clicking on forgot password link on login page.'. '<br><br/>'.

			'We value your relationship and look forward to delighting you with our services.<br/><br/>'.
			        'For any queries please refer to Christian Connect Help.<br/><br/><br/>'.
					'Best Regards<br/>'.
			        'Team Christian Connect';
			     
			$mail->setBody($text);
			$mail->IsHTML(true);
			$joomla_config = new JConfig();
			$mail->addRecipient($user->email);
			$mail->setSender($mailfrom, $fromname);
			$mail->Send(); 
		 }
		/*function to response data
		 * @params errNo,msgString,result if any other wise set 0
		 * @return json string
		 */
   
	  private function responseData($errNo,$msgString,$result){
	        
	   		 	$this->response['resultObject']=$result;    
	  			$this->response['message']=$msgString;
	  			$this->response['statusCode']=$errNo;
    	        return json_encode($this->response); 
	    }
		
}
