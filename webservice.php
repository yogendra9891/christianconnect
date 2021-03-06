<?php
/*
 * Created By ::- Daffodil
 * Date  ::-  01/04/2013
 */
require_once('actions.php');
require_once('checkparameter.php');
define('LOGFILEPATH1', getcwd().'/errorfile.txt'); 
?>

<?php
$webservice= new WebService();//make a object of below webseervice class

if(!isset($_REQUEST['action']))
{
    echo WebService::handleError(WebService::ERR_ACTION_NOT_FOUND, "Request URl does not have the action param");
    return;
}
 $functionName=$webservice->checkMethods($_REQUEST['action']); 

if($functionName){
		call_user_func('Action::'.$functionName);//call a static function of action class
        return;

}else{
 	echo WebService::handleError(WebService::ERR_ACTION_NOT_SUPPORT, "Requested Method not supported ");
    return;
}

/*
 * Main class of webservice
 * Requeat always start from here
 */
Class WebService {

    
    const LOGFILEPATH= LOGFILEPATH1;/*log file path*/
    const ERR_ACTION_NOT_FOUND='46';
    const ERR_ACTION_NOT_SUPPORT='47';
    const ERR_DATA_CODE='1';
    const MSG_DATA_NOT_PROVIDED="Parameter missing : ";
    const MSG_VALUE_NOT_PROVIDED="Parameter value missing : ";
    
    /*function for returning the error message and error code
     * Function except two parametet $errNo(integer),$msgString(string)
     * Retrun a json string
     */
    static public function handleError($errNo,$msgString)
    {
        $response = array();
        $response['resultObject']=0;    
  		$response['message']=$msgString;
  		$response['statusCode']=$errNo;
        return json_encode($response);
        
        
    }
    
    
    /*Check action is valid or not */
    /* return the function name*/
    public function checkMethods($action)
    {
        $validateArray=array(
            "Register"=>"register",
            "Login"=>"login",
            "ForgetPassword"=>"forgetpassword",
            "ForgetPasswordConfirm"=>"forgetpasswordconfirm",
            "UpdateProfile"=>"updateprofile",
            "getReligions"=>"getreligions",
       	    "Logout"=>"logout",
            "getChurches"=>"getchurches",
            "getMyChurches"=>"getmychurches",
        	"deleteMyChurch"=>"deletemychurch",
        	"searchChurch"=>"searchchurch",
        	"getChurchDetails"=>"getchurchdetails",
        	"getChurchLeaders"=>"getchurchleaders",
        	"getChurchFriends"=>"getchurchfriends",
        	"getMyFriends"=>"getmyfriends",
        	"getMyProfile"=>"getmyprofile",
        	"getUserProfile"=>"getuserprofile",
            "sendMessage"=>"sendmessage",
            "deleteMessage"=>"deletemessage",
        	"getMessages"=>"getmessages",
        	"getUnreadMessageCount"=>"getunreadmessagecount",
        	"emailAdmin"=>"emailadmin",
		    "getMyChristianLife"=>"getmychristianlife",
            "getchurchCategory"=>"getchurchcategory"
        
        );
        
        return array_search(strtolower($action),$validateArray);
    }

}
