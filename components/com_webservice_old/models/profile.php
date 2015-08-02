<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
define('IMG_UPLOAD_DIR_PATH', 'components/com_users/images/profileimage/original/');
define('IMG_THUMB_UPLOAD_DIR_PATH', 'components/com_users/images/profileimage/thumbs/');
define('PROFILE_IMAGE_PATH','components/com_users/images/profileimage/thumbs/');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userprofileaccess.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianusers.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userchurch.php');
define('LOGFILEPATH', getcwd().'/errorfile.txt'); 

/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelProfile extends JModelItem
{
	  // const LOGFILEPATH= '/opt/lampp/htdocs/christianconnect_yogi/errorfile.txt';/*log file path*/

	 /*
	 * updating user profile.
	 * @params
	 * sessionId, email etc.
	 */
	  public function UpdateProfile($data, $filedata){ 
		$db=JFactory::getDBO();
		$config = JFactory::getConfig();
	  	$returnarray = $this->checkSessionId($data['sessionId']);
	  	if(array_key_exists('userProfileObject', $data)){
	  	if(is_array($data['userProfileObject'])){	
	  	if(array_key_exists('email', $data['userProfileObject'])){
	  	if($data['userProfileObject']['email'] == ''){
			 error_log("Error::".JText::_('EMAIL_IN_ERROR')."\n",3,  LOGFILEPATH);
			 echo $this->responseData(JText::_('ERRCODE_EMAIL_IN_ERROR'),  JText::_('EMAIL_IN_ERROR'),false);
			 exit;
	  	}
	  	//check for email id already not exists of other user..
	  	if(!empty($data['userProfileObject']['email']))
	  	{
	  		$resultid1 = $this->checkOtherUser($returnarray, $data['userProfileObject']['email']);
	  		if($resultid1 > 0 )
	  		{ 
			 error_log("Error::".JText::_('EMAIL_INUSE_ERROR')."\n",3,  LOGFILEPATH);
			 echo $this->responseData(JText::_('ERRCODE_EMAIL_INUSE_ERROR'),  JText::_('EMAIL_INUSE_ERROR'),false);
			 exit;
	  		}
	  	}}}}
	  	$params = JComponentHelper::getParams('com_users');
	
		// Initialise the table with JUser.
		$user = new JUser($returnarray->userid);
		$userdata = array();
		// Prepare the data for the user object.
		if(array_key_exists('userProfileObject', $data)){
		if(array_key_exists('email', $data['userProfileObject'])){
		$userdata['email']	= $data['userProfileObject']['email'];
		$userdata['username']	= $data['userProfileObject']['email'];}
		if(isset($data['userProfileObject']['firstName'])){ $userdata['name'] = $data['userProfileObject']['firstName']. ' '. $data['userProfileObject']['lastName'];} 
		}
		// Bind the data.
		if (!$user->bind($userdata)) {
			error_log("Error::".JText::_('PROFILE_UPDATE_DATA_BIND_ERROR')."\n",3,  LOGFILEPATH);		}
		// Load the users plugin group.
		JPluginHelper::importPlugin('user');
		$user->groups = null;
		// Store the data. 
		// in else part we will save the data into the christianuser and userprofileaccess table...corrosponding the user..
		if (!$user->save()) {
			error_log("Error::".JText::_('PROFILE_UPDATE_DATA_SAVE_ERROR')."\n",3,  LOGFILEPATH);	
		    return false;
		}
		else{ 
			//update other info..
			$check = $this->saveotherInfo($data, $filedata, $user->id);
			//send email to admin if a user changed his/her email..id
			if(@$userdata['email'] != $returnarray->username)
			$this->sendemailtoadmin($returnarray->username, $user->id);
			return $check;
		}
		return true;
	  }
	  /*
	   * check for other user not exists by same username(email).
	   */
	  private function checkOtherUser($returnarray, $newemail){
	     	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id');
		 	$query->from('#__users AS a');
		 	$query->where('(a.username='.$db->quote($newemail). ' OR a.email ='.$db->quote($newemail).')' );
		 	$query->where('(a.id != '.(int)$returnarray->userid.')');
		 	$db->setQuery($query); 
		 	$db->query();
	  		$resultid = $db->loadResult(); 
	  		return $resultid;
	  }
	  
	  /*
	   * function for finding the userid and emailid from session id. 
	   */
	private function checkSessionId($sessionid)
		 {
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.userid, a.username');
		 	$query->from('#__session AS a');
		 	$query->where("a.session_id=".$db->quote($sessionid));
		 	$db->setQuery($query);
		 	$logininfo = $db->loadObject();
		 	return $logininfo;
		 }
	  /*
	   * function for saving the other info of a user at the profile update time...
	   */
	 private function saveotherInfo($data, $filedata, $userid){ 	
			$christianuser = & JTable::getInstance('ChristianUsers','UsersTable');
			$userdata = array();
			if(array_key_exists('userProfileObject', $data)){
				if(is_array($data['userProfileObject'])){
				$userdata['userid'] = $userid;
				if(array_key_exists('firstName', $data['userProfileObject'])){
				$userdata['fname'] = $data['userProfileObject']['firstName'];}
				if(array_key_exists('lastName', $data['userProfileObject'])){
				$userdata['lname'] = $data['userProfileObject']['lastName'];}
				if(array_key_exists('DOB', $data['userProfileObject'])){
				$userdata['dob'] = $data['userProfileObject']['DOB'];}
				if(array_key_exists('email', $data['userProfileObject'])){
				$userdata['email'] = $data['userProfileObject']['email'];}
				if(array_key_exists('town', $data['userProfileObject'])){
				$userdata['city'] = $data['userProfileObject']['town'];}
		//		$userdata['country'] = $data['userProfileObject']['country'];
		//		$userdata['postcode'] = $data['userProfileObject']['postcode'];
				if(array_key_exists('religion', $data['userProfileObject'])){
				$userdata['religion'] = $data['userProfileObject']['religion'];}
				if(array_key_exists('interest', $data['userProfileObject'])){
				$userdata['interest'] = $data['userProfileObject']['interest'];}
				if(array_key_exists('favoriteBibleQuotes', $data['userProfileObject'])){
				$userdata['favouritebiblequote'] = $data['userProfileObject']['favoriteBibleQuotes']; }
			}}
		//	@TOdo...
//			$address = $data['address1']." ".$data['address2']." ".$data['userProfileObject']['postcode']." ".$data['userProfileObject']['town']." ".$data['state']." ".$data['userProfileObject']['country'];
//			// find the latitude and longitude...
//		    $locationdata = $this->getLatLon($address);
//			$userdata['lat'] = $locationdata['lat'];
//			$userdata['lng'] = $locationdata['lng'];
	
			if(!empty($filedata['name']))
			{
				$newfilename = $this->ImageUpload($filedata);
			}		
			
			if(!empty($filedata['name'])){$userdata['profileimage'] = JURI::base().PROFILE_IMAGE_PATH.$newfilename;}
			$christianuserid = $this->findchristianuserid($userid);
			$christianuser->load($christianuserid); 
			if (!$christianuser->save($userdata)) { 
				error_log("Error::".JText::_('PROFILE_UPDATE_OTHER_PROFILE_DATA_SAVE_ERROR')."\n",3,  LOGFILEPATH);
				return false;
			}
			/*
			 * below code for saving the access level for the user profile field in Userprofileaccess table, by default we are saving it public=>1 
			 * only we are saving the registered user id here.....
			 */
		   $christianuserprofileaccess = & JTable::getInstance('Userprofileaccess','UsersTable');
		   $data1 = array();
		   $data1['userid'] = $userid;
		   $christianprofileuserid = $this->findchristianprofileuserid($userid);
		   $christianuserprofileaccess->load($christianprofileuserid);
		   if (!$christianuserprofileaccess->save($data1)) { 
				error_log("Error::".JText::_('PROFILE_UPDATE_OTHER_PROFILE_ACCESS_DATA_SAVE_ERROR')."\n",3,  LOGFILEPATH);
		   }
		   //save church details..
		   $christianuserchurch = & JTable::getInstance('Userchurch','UsersTable');
		   $data2 = array();
		   if(array_key_exists('userProfileObject', $data)){
			   if(array_key_exists('localChurch', $data['userProfileObject'])){	
			   $data2['localchurch'] =  $data['userProfileObject']['localChurch'];}
			   if(array_key_exists('otherChurches', $data['userProfileObject'])){
			   $data2['otherchurch'] =  $data['userProfileObject']['otherChurches'];}
		   }
		   $data2['userid'] =  $userid;
		   $churchrowid = $this->findchurchrow($userid);
		   $christianuserchurch->load($churchrowid);
		   if (!$christianuserchurch->save($data2)) { 
				error_log("Error::".JText::_('PROFILE_UPDATE_OTHER_PROFILE_CHURCH_DATA_SAVE_ERROR')."\n",3,  LOGFILEPATH);
		   }
		   
		   return true;
	 }
	/*
	 * function for sending the mail to admin when a user changing his/her email id....
	 */    
   public function sendemailtoadmin($oldemail, $userid)
   {
   		$user = JFactory::getUser($userid);
   		$mail =& JFactory::getMailer();
 		$app		= JFactory::getApplication();  		
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$mail->setSubject(JText::_('COM_WEBSERVICE_EMAIL_CHANGED'));
		$text = $user->name.' '. JText::_('COM_WEBSERVICE_USER_CHANGED_EMAIL').' '. $oldemail.' '. JText::_('COM_WEBSERVICE_TO_EMAIL').' '. $user->email.' '.JText::_('COM_WEBSERVICE_EMAIL_WITH').' '.$user->id ; 
		$mail->setBody($text);
		$mail->IsHTML(true);
		$joomla_config = new JConfig();
		$mail->addRecipient($mailfrom);
		$mail->setSender($mailfrom, $fromname);
		$mail->Send(); 
   }
	 /*
	  * function for finding the user churches row ids,....
	  */
   private function findchurchrow($userid){
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id');
		 	$query->from('#__userchurch AS a');
		 	$query->where("a.userid=".(int)$userid);
		 	$db->setQuery($query);
		 	$chuserrowid = $db->loadResult();
		 	return $chuserrowid;
   }
	 /*
	  * function for finding the table row id on the basis of userid..
	  */
	 private function findchristianprofileuserid($userid){
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id');
		 	$query->from('#__userprofileaccess AS a');
		 	$query->where("a.userid=".(int)$userid);
		 	$db->setQuery($query);
		 	$chuserprofileid = $db->loadResult();
		 	return $chuserprofileid;
	 }
	 /*
	  * function for finding the id of a christianuser..
	  */
	 private function findchristianuserid($userid){
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id');
		 	$query->from('#__christianusers AS a');
		 	$query->where("a.userid=".(int)$userid);
		 	$db->setQuery($query);
		 	$chuserid = $db->loadResult();
		 	return $chuserid;
	 }
    /*
     * function for uplaoding a profile picture.....
     */

	private function ImageUpload($file){ 
		$response = array();
		$response['error'] = false;
		$response['msg'] = '';
		$response['src'] = '';
		// Make the file name safe.
		jimport('joomla.filesystem.file');
		$user = JFactory::getUser();
		$filename = $file['name'] = time().self::clean(JFile::makeSafe(strtolower($file['name'])));

		// Move the uploaded file into a permanent location.
		if (isset( $file['name'] )) {
			// Make sure that the full file path is safe.
			$filepath = JPath::clean( IMG_UPLOAD_DIR_PATH.strtolower( $file['name'] ) );
			// Move the uploaded file.
			if(JFile::upload( $file['tmp_name'], $filepath )){
			 // if orignal file uploaded then create thumb..
			 	$this->createThumbnail($filename);
			}else{
				$response['error'] = true;
				$response['msg'] = JText::_('COM_USERIMAGE_UPLOAD_FAILED');
			}
		}
		return $file['name'];
	}

	/*
	 * creating a thumb of the profile image... 
	 * reading the file from orignal folder and create its thumbs..
	 */
	private function createThumbnail($filename) {   
        $path_to_thumbs_directory = IMG_THUMB_UPLOAD_DIR_PATH; 
    	$path_to_image_directory =  JURI::root().IMG_UPLOAD_DIR_PATH; 
    	$final_width_of_image = 100;  
   
        if(preg_match('/[.](jpg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](jpeg)$/', $filename)) {  
            $im = imagecreatefromjpeg($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](gif)$/', $filename)) {  
            $im = imagecreatefromgif($path_to_image_directory . $filename);  
        } else if (preg_match('/[.](png)$/', $filename)) {  
            $im = imagecreatefrompng($path_to_image_directory . $filename);  
        }  
        $ox = imagesx($im);  
        $oy = imagesy($im);  
        $ratio = $ox / $oy;
		$nx = $ny = min($final_width_of_image, max($ox, $oy));
		if ($ratio < 1) {
 		   $nx = $ny * $ratio;
		} else {
           $ny = $nx / $ratio;
		}
		
        $nm = imagecreatetruecolor($nx, $ny);  
        imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);  
        if(!file_exists($path_to_thumbs_directory)) {  
          if(!mkdir($path_to_thumbs_directory)) {  
               die("There was a problem. Please try again!");  
          }  
           }
        imagejpeg($nm, $path_to_thumbs_directory . $filename);   
		return true;
	}  
	
	/**
	 * Method to remove space from the string
	 * @param unknown_type $string
	 */
	public function clean($string){
		return JString::str_ireplace(' ', '', $string);
	}
/*
 * 
 * Function added for Lat and Long...
 */	
    private function getLatLon($address) {
        $address = str_replace(" ", "+", $address);
        $output = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=false", true);
        $resultInArray = json_decode(utf8_encode($output), true);
      
        return $resultInArray['results'][0]['geometry']['location'];
    }
  /*
   * function for logout a user..
   * @param
   * sessionId
   */	
    public function Logout($data){
    	
		$userarray = $this->checkSessionId($data['sessionId']);  
		$app = JFactory::getApplication();   
		$options = array();
		$options['sessionId'] = $data['sessionId'];   
		$check = $app->logout((int)$userarray->userid, $options); 
	    $user = JFactory::getUser(); 
	    // Perform the log in.
		if ( true === $check ) {
			// Success
			return true;
		} else {
			// Logout failed !
			return false;
		}
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
		

	
			/*function to get my friend data
			 * @param userid
			 * @return friend object if successful
			 */
		 function getMyProfile()
		 {
		 	$resultdata=new stdClass();
		 	$resultdata->userProfileObject=new stdClass();
		 	$resultdata->userProfileObject->localchurch=new stdClass();
		 	$resultdata->userProfileObject->otherchurch=new stdClass();
		 	
		 	$userId=$this->getState('profile.userId');
		 	
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
	
		 	$query->select('a.userid, a.fname As firstName, a.lname AS lastName, a.email,DATE_FORMAT(a.dob, "%Y-%m-%d") AS DOB, a.city AS town, b.name AS religion,a.interest,a.favouritebiblequote AS favoriteBibleQuotes, a.profileimage AS profilePic');
		 	$query->from('#__christianusers  AS a');
		 	$query->join('LEFT','#__userreligion AS b ON b.name = a.religion');
		 	$query->where('a.userid='.$userId);
		 	
		 	$db->setQuery($query);
		 	$resultdata->userProfileObject=$db->loadObject();
		 	
		 	$resultdata->userProfileObject->localchurch=$this->getLocalChurchByUserId();
		 	
		 	$resultdata->userProfileObject->otherchurch=$this->getOtherChurchByUserId();
		 	
		 	$resultdata->totalCount=$this->_getListCount($query);
		 	
		 	return $resultdata;
		   
		 }
		 
		 
		 /*function to get local church by userid
		  * @param userid
		  * @return church object
		  */
		 function getLocalChurchByUserId()
		 {
			$userId=$this->getState('profile.userId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$subquery=$db->getquery(true);
		 	$subquery->clear();
		 	//To Do keep this query in subquery
		 	$subquery->select('b.localchurch');
		 	$subquery->from('#__userchurch  AS b');
		 	$subquery->where('b.userid='.$userId);
		 	$db->setQuery($subquery);
		 	$lchurchs=$db->loadResult();
		 	
		 	$query->select("a.id AS churchId,a.cname AS name");
		 	$query->from('#__church  AS a');
		 	$query->where('a.id IN('.$lchurchs.')');
		 	$localchurchs=$this->_getList($query);
		 	return $localchurchs;
		 }
		 
		/*function to get church by userid
		  * @param userid
		  * @return church object
		  */
		 function getOtherChurchByUserId()
		 {
			$userId=$this->getState('profile.userId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$subquery=$db->getquery(true);
		 	$subquery->clear();
		 	//To Do keep this query in subquery
		 	$subquery->select('b.otherchurch');
		 	$subquery->from('#__userchurch  AS b');
		 	$subquery->where('b.userid='.$userId);
		 	$db->setQuery($subquery);
		 	$ochurchs=$db->loadResult();
		 	
		 	$query->select("a.id AS churchId,a.cname AS name");
		 	$query->from('#__church  AS a');
		 	$query->where('a.id IN('.$ochurchs.')');
		 	$otherchurchs=$this->_getList($query);
		 	return $otherchurchs;
		  	
		 }
		
 }
