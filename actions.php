<?php
require_once('configuration.php');
?>

<?php
/*
 * Used to handle all the request action fron here
 */
class Action {

    static private $extraData;// Used to send a extra data except request data

    const TIMEOUT = 5;//time used for waiting to make conection through curl

 //code edit by yogendra started....

    /* function to Register a user...
    * @Params 
    * email, firstname,lastname, password, isSocialAuth*/
    static public function Register() {
		$context='registration.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkRegisterParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
    
   /*
    * function for login..
    * @params 
    * 
    */   
     static function Login(){
		$context='registration.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkLoginParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     /*
      * function for forget password..
      * @params
      * emailId
      */
     static function forgetpassword(){
		$context='registration.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkForgetPasswordParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     /*
      * function for reset the password, after verification code sent..
      * @params
      * emailId,activationcode, newpassword,newpassword1
      */
     static function forgetpasswordconfirm(){
		$context='registration.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkForgetPasswordConfirmParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     /*
      * function for update user profile..
      */
     static function updateprofile(){
		$context='profile.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkUpdateProfileParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getProfileData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
	/*
	 * function for finding the alll religions..
	 */  
     static function getreligions(){
		$context='religion.'. $_REQUEST['action']; 
        $check = CheckParameter::getInstance()->checkGetReligionParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     } 
	/*
	 * function for finding the alll religions..
	 */  
     static function getchurchcategory(){
		$context='religion.'. $_REQUEST['action']; 
        $check = CheckParameter::getInstance()->getchurchCategoryParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     } 
     
     /*
      * function for logout a user on its sessionid basis..
      */  
     static function Logout(){
		$context='profile.'. $_REQUEST['action']; 
        $check = CheckParameter::getInstance()->checkLogoutParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
//code end edit by yogendra...
  

    /* function  to set action for getchurches
    * @Params 
    * none */

    static public function getChurches() {
		$context='findchurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetChurchesParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }

	/* function  to set action for getuserChurches
    * @Params 
    * none */

    static public function getMyChurches() {
		$context='mychurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkgetMyChurchesParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
	/* function  to set action for delete MyChurch 
    * @Params 
    * none */

    static public function deleteMyChurch() {
		$context='mychurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkdeleteMyChurchParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
	/* function  to set action for seach Church 
    * @Params 
    * none */

    static public function searchChurch() {
		$context='findchurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkSearchChurchParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
     
	/* function to set action for getChurchDetails
    * @Params 
    * none */

    static public function getChurchDetails() {
		$context='mychurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetChurchDetailsParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
     
	/* function to set action for delete MyChurch 
    * @Params 
    * none */

    static public function getChurchLeaders() {
		$context='mychurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetChurchLeadersParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
	/* function to set action for getChurchFriends
    * @Params 
    * none */

    static public function getChurchFriends() {
		$context='mychurch.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetChurchFriendsParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
 
     
	/* function to set action for getMyFriends
    * @Params 
    * none */

    static public function getMyFriends() {
		$context='myfriend.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetMyFriendsParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
	/* function to set action for getmyprofile
    * @Params 
    * none */

    static public function getMyProfile() {
		$context='profile.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetMyProfileParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
	/* function to set action for getUserProfile
    * @Params 
    * none */

    static public function getUserProfile() {
		$context='profile.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetUserProfileParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
     
	/* function to set action for getMessages
    * @Params 
    * none */

    static public function getMessages() {
		$context='messages.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetMessagesParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
  
	/* function to set action for getUnreadMessageCount
    * @Params 
    * none */

    static public function getUnreadMessageCount() {
		$context='messages.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkGetUnreadMessageCountParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     /*
      * function for set the action for sendMessage
      * @params
      */
     static public function sendMessage(){
		$context='messages.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checksendMessageParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     /*
      * function for set the action for deleteMessage
      * @params
      */
     static public function deleteMessage(){ 
		$context='messages.'. $_REQUEST['action'];
        $check = CheckParameter::getInstance()->checkdeleteMessageParam();
        //if check is ture send request
	        if ($check) {
	        	$url=self::getUrl($context);
	        	$data = self::getData($url);
	        	echo $data;
	        } else {
	            return;
	        }
	        return;
     }
     
    /*
     * used to make a joomla url 
     * retun a url string
     */
    static protected function getUrl($context) {

        $protocol = isset($_SERVER['HTTPS']) ? "https" : "http";
        $baseurl = $protocol . "://" . $_SERVER['HTTP_HOST'] . implode('/', explode('/', $_SERVER['REQUEST_URI'], -1));
        $requestUri = '/index.php?option=com_webservice&task=' .$context;
        return $baseurl . $requestUri;
    }

    /* it always provide the data for which you requested */

  	static public function getData($url) {
  		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_REQUEST);
        //curl_setopt ($ch, CURLOPT_POSTFIELDS, self::$extraData);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TIMEOUT);
        $data = curl_exec($ch); 
        curl_close($ch);
        return $data;
    }
    /*
     * function for uploadating the profile data + image..
     */
	static public function getProfileData($url)
	{   
		if(!empty($_FILES['profilePic']['name']))
		{
		$formdata = array('data'=>$_REQUEST['data'],'name' => $_FILES['profilePic']['name'], 'file' => '@'.$_FILES['profilePic']['tmp_name']); 
		}else{
			$formdata = array('data'=>$_REQUEST['data']);
		}
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $_REQUEST);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $formdata);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, self::TIMEOUT);
		$data = curl_exec($ch); 
		curl_close($ch);
		return $data;

	}
}
