<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php

class CheckParameter{
    
    private $mandatoryParameter=array();
    private $nonMandatoryParameter=array();
    
    
    static function getInstance(){
        
        $newObject=new CheckParameter();
        return $newObject;
    }

    /* thus is the validate function*/
    /* This is just used for checking the paramenter which is mondatory or not*/
    private function validateParameter(){
         $date = date('d.m.Y h:i:s'); 
        foreach ($this->mandatoryParameter as $keyParameter=>$value) { 
        	
                    	$data=  json_decode($_REQUEST['data'],true);
                    	if(empty($data)){
	                    	
	                            echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_VALUE_NOT_PROVIDED."Data (main param)");
	                            error_log("Date::".$date."\n Error::".WebService::MSG_DATA_NOT_PROVIDED."Data (main param) \n",3,  WebService::LOGFILEPATH);
	                            exit;
	                    }
	                    if (!array_key_exists($keyParameter, $data)) {
		 			
	                                echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_DATA_NOT_PROVIDED.$keyParameter);
	                                error_log("Date::".$date."\n Error::".WebService::MSG_DATA_NOT_PROVIDED.$keyParameter."\n",3,  WebService::LOGFILEPATH);
	                                exit;
	                                
	                    }elseif(is_array($data[$keyParameter]))
		                    {
		                    	foreach ($this->mandatoryParameter[$keyParameter]as $subkey=>$subvalue) {
		                    		
				                    	if (!array_key_exists($subkey, $data[$keyParameter])) {
				                    		
					 						echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_DATA_NOT_PROVIDED.$subkey);	
				                    		error_log("Date::".$date."\n Error::".WebService::MSG_DATA_NOT_PROVIDED.$subkey."\n",3,  WebService::LOGFILEPATH);
				                    	 	exit;
				                    	}elseif(trim($data[$keyParameter][$subkey])==''){
				                    		  	echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_VALUE_NOT_PROVIDED.$subkey);
				                    	        error_log("Date::".$date."\n Error::".WebService::MSG_VALUE_NOT_PROVIDED.$subkey."\n",3,  WebService::LOGFILEPATH);
				                                 exit;
				                    	}
		                    		}
		                    }elseif(trim($data[$keyParameter])==''){
	    				            echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_VALUE_NOT_PROVIDED.$keyParameter);
	                                error_log("Date::".$date."\n Error::".WebService::MSG_VALUE_NOT_PROVIDED.$keyParameter."\n",3,  WebService::LOGFILEPATH);
	                                exit;
	                    }
                    }
        	
	               	 foreach ($this->nonMandatoryParameter as $key=>$value) {
	                   
		                    if (!array_key_exists($key, $data)) {
		                    	
			 			        error_log("Date::".$date."\n Warning::".WebService::MSG_DATA_NOT_PROVIDED.$key."\n",3,  WebService::LOGFILEPATH);
		                                
		                    }else if(is_array($data[$key]))
		                    {
		                    	foreach ($this->nonMandatoryParameter[$key]as $subkey=>$subvalue) {
		                    		
				                    	if (!array_key_exists($subkey, $data[$key])) {
				                    		
					 							error_log("Date::".$date."\n Error::".WebService::MSG_DATA_NOT_PROVIDED.$subkey."\n",3,  WebService::LOGFILEPATH);
				                    	
				                    	}elseif(trim($data[$key][$subkey])==''){
				                    		
				                    	        error_log("Date::".$date."\n Error::".WebService::MSG_VALUE_NOT_PROVIDED.$subkey."\n",3,  WebService::LOGFILEPATH);
				                                
				                    	}
		                    		}
		                    }elseif(trim($data[$key])==''){
		                    	
		    				     error_log("Date::".$date."\n Warning::".WebService::MSG_VALUE_NOT_PROVIDED.$key."\n",3,  WebService::LOGFILEPATH);       
		                    }
				
	                }
            
                return TRUE;
        
    }
    
    /*Method to validate data variable 
     * called by functions that need some input value from request
     *  @data variable from request
     *  @return error if not set or empty
     */
    protected function validateData()
    {
    	 $date = date('d.m.Y h:i:s'); 
    	if(!isset($_REQUEST['data'])){
				 echo WebService::handleError(WebService::ERR_DATA_CODE, WebService::MSG_DATA_NOT_PROVIDED.'data');
	             error_log("Date::".$date."\n Error::".WebService::MSG_DATA_NOT_PROVIDED."data(main param) \n",3,  WebService::LOGFILEPATH);
	             exit;
		}
  
   	}

/*
 * code started by yogendra.....
 */
   	/*
   	 * function for doing the registration....
   	 */
   	public function checkRegisterParam(){
        $this->validateData();
		$this->mandatoryParameter['email']='';
        $this->mandatoryParameter['firstName']='';
        $this->mandatoryParameter['password']='';
        $this->mandatoryParameter['isSocialAuth']='';
        $this->nonMandatoryParameter['lastName']='';
        return $this->validateParameter();
   	}
	/*
	 * function for cheking the parameter for login..
	 */
   	public function checkLoginParam(){
        $this->validateData();
//		$this->mandatoryParameter['email']='';
//      $this->mandatoryParameter['password']='';
        $this->mandatoryParameter['isSocialAuth']='';
        return $this->validateParameter();
   	}
   	/*
   	 * function for checking parameter of forget password.
   	 */
   	public function checkForgetPasswordParam(){
        $this->validateData();
		$this->mandatoryParameter['email']='';
        return $this->validateParameter();
   	}
   	/*
   	 * function for checking the parameter of forget passowrd confirmation.. 
   	 */
   	public function checkForgetPasswordConfirmParam(){
        $this->validateData();
		$this->mandatoryParameter['email']='';
        $this->mandatoryParameter['verificationcode']='';
        $this->mandatoryParameter['newpassword']='';
        $this->mandatoryParameter['newpassword1']='';
        return $this->validateParameter();
   		
   	}
   	/*
   	 * function for cheking the update profile 
   	 */
   	public function checkUpdateProfileParam(){ 
        $this->validateData();
		$this->mandatoryParameter['sessionId']='';
        $this->nonMandatoryParameter['userProfileObject']='';
        $this->nonMandatoryParameter['userProfileObject']['firstName']='';
        $this->nonMandatoryParameter['userProfileObject']['lastName']='';
        $this->nonMandatoryParameter['userProfileObject']['DOB']='';
        $this->nonMandatoryParameter['userProfileObject']['town']='';
        $this->nonMandatoryParameter['userProfileObject']['email']='';
        $this->nonMandatoryParameter['userProfileObject']['religion']='';
        $this->nonMandatoryParameter['userProfileObject']['localChurch']='';
        $this->nonMandatoryParameter['userProfileObject']['otherChurches']='';
        $this->nonMandatoryParameter['userProfileObject']['interest']='';
        $this->nonMandatoryParameter['userProfileObject']['favoriteBibleQuotes']='';
        $this->nonMandatoryParameter['userProfileObject']['profilePic']='';
//	  	if(!empty($_FILES['profilePic']['name']))
//	  	{
//	  		$filedata = array('name' => $_FILES['profilePic']['name'], 'file' => '@'.$_FILES['profilePic']['tmp_name']); print_r($filedata); die;
//	  	}
        
        return $this->validateParameter();
   	}
	/*
	 * function for checking the params for finding the religions..
	 */
   	public function checkGetReligionParam(){ 
		$this->validateData(); 
		$this->mandatoryParameter['sessionId']=''; 
		$this->nonMandatoryParameter['keywords']='';
        return $this->validateParameter();
   	}
   	/*
   	 * checking the params for the finding the church category..
   	 */
   	public function getchurchCategoryParam(){
		$this->validateData(); 
		$this->mandatoryParameter['sessionId']=''; 
        return $this->validateParameter();
   	}
   	/*
   	 * function for checking the logout parameter..
   	 */
   	public function checkLogoutParam(){
		$this->validateData(); 
		$this->mandatoryParameter['sessionId']=''; 
        return $this->validateParameter();
   	}
/*
 * code end edit by yogendra..
 */    
    
    /* function to validate input for GetChurches service
     * 
     */
    public function checkGetChurchesParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
        $this->mandatoryParameter['pageIndex']='';
        $this->mandatoryParameter['pageSize']='';
        $this->nonMandatoryParameter['keywords']='';
        $this->nonMandatoryParameter['userLocation']='';
        $this->nonMandatoryParameter['userLocation']['lat']='';
        $this->nonMandatoryParameter['userLocation']['long']='';
        return $this->validateParameter();
        
    }
    
     /* function to validate input for getMyChurchesParam
     * 
     */
 	public function checkgetMyChurchesParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
        return $this->validateParameter();
        
    }
    
	/* function to validate input for GetChurchDetails
     * 
     */
 	public function checkGetChurchDetailsParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['churchId']='';
        return $this->validateParameter();
        
    }
    
 	/* function to validate input for deleteMyChurch
     * 
     */
 	public function checkdeleteMyChurchParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['churchId']='';
        return $this->validateParameter();
        
  	  }
  	  
	/* function to validate input for SearchChurch
     * 
     */
 	public function checkSearchChurchParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->nonMandatoryParameter['churchType']='';
		$this->mandatoryParameter['userLocation']='';
		$this->mandatoryParameter['userLocation']['lat']='';
        $this->mandatoryParameter['userLocation']['long']='';
        $this->mandatoryParameter['pageIndex']='';
		$this->mandatoryParameter['pageSize']='';
        return $this->validateParameter();
        
  	  }
    
	/* function to validate input for GetChurchLeaders
     * 
     */
 	public function checkGetChurchLeadersParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['churchId']='';
		return $this->validateParameter();
        
  	  }
  	  
	/* function to validate input for GetChurchLeaders
     * 
     */
 	public function checkGetChurchFriendsParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['churchId']='';
		 $this->mandatoryParameter['pageIndex']='';
		$this->mandatoryParameter['pageSize']='';
		return $this->validateParameter();
        
  	  }
  	  
	/* function to validate input for GetMyFriends service
     * 
     */
 	public function checkGetMyFriendsParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['pageIndex']='';
		$this->mandatoryParameter['pageSize']='';
		return $this->validateParameter();
        
  	  }
  	  
/* function to validate input for getMyProfile service
     * 
     */
 	public function checkGetMyProfileParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		return $this->validateParameter();
        
  	  }
  	  

	/* function to validate input for getUserProfile service
     * 
     */
 	public function checkGetUserProfileParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['userId']='';
		return $this->validateParameter();
        
  	  }
  	  
	/* function to validate input for getMessages service
     * 
     */
 	public function checkGetMessagesParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['Type']='';
		$this->mandatoryParameter['pageIndex']='';
		$this->mandatoryParameter['pageSize']='';
		$this->mandatoryParameter['threadId']='';
		return $this->validateParameter();
        
  	  }
  	  
 /* function to validate input for getMyChurchesParam
     * 
     */
 	public function checkGetUnreadMessageCountParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
        return $this->validateParameter();
    }
 /*
  * function for checking the parameter for send message..
  */
    public function checksendMessageParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['messageObject']='';
		$this->mandatoryParameter['messageObject']['sendTo']='';
		$this->mandatoryParameter['messageObject']['body']='';
		$this->mandatoryParameter['messageObject']['timeStamp']='';
		return $this->validateParameter();
    }
    /*
     * checking parameter for delete message..
     */
    public function checkdeleteMessageParam(){
		$this->validateData();
		$this->mandatoryParameter['sessionId']='';
		$this->mandatoryParameter['messageId']='';
		return $this->validateParameter();
    }
}
