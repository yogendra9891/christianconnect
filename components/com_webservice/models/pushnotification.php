<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_christianconnect'.DS.'tables'.DS.'location.php');
include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_christianconnect'.DS.'tables'.DS.'userdevice.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_webservice'.DS.'library'.DS.'notificationconfig.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_myfriend'.DS.'tables'.DS.'christianmessage.php');
require_once (JPATH_ROOT.DS.'components'.DS.'com_myfriend'.DS.'tables'.DS.'christianthread.php');

/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelPushnotification extends JModelItem
{
			/*function to send pushnotification to all myfriend 
			 * @param session id
			 * @return 
			 */
		public function requestFriendLocation()
		 {
		 	$nconfig = new NConfig;
		 	$message=array();
		 	//getting the device token to send notification
		 	$deviceInfos=$this->getNotificationDeviceInfo();
		 	//save request record in location table
			$this->saveLocationRequest();
		 	//getting the user name who send notification
		 	$username=$this->getUserNameBYId();
		 	$message['requesterMessage']=str_replace('%s',$username,$nconfig->sendrequestlocation_message);
		 	if(count($deviceInfos)!=0){
			 	foreach($deviceInfos as $deviceInfo) 
			 	{
						//set request id
			 			$response=$this->sendPushNotification($deviceInfo,'0',$message);
			 			$responsedecode=json_decode($response);
			 			/*if message successfully send than register the number in user friend list*/ 
						if(isset($responsedecode->results[0]->message_id))
							{
								if(isset($responsedecode->results[0]->registration_id))
									{
										/*update device_token of the user if it is changed by gcm*/
										$devicedata['olddevice_token']=$deviceInfo['device_token'];
										$devicedata['newdevice_token']=$responsedecode->results[0]->registration_id;
										$return=$this->updateDeviceToken($devicedata);
									}
							}
			 	}
		 	return true;
		 	}else{
		 	return false;
		 	}
		 	
		 }
		 
		/**
		 * function to get username send in notification
		 * @param 
		 * @return boolean
		 */
		protected function getUserNameBYId(){
				$userId=$this->getState('pushnotification.userId');
			 	$db=JFactory::getDBO();
			 	$query=$db->getquery(true);
			 	$query->clear();
			 	//query to get username by id if username blank get email
			 	$query->select("IF(b.fname ='',b.email,CONCAT_WS(' ',b.fname,b.lname) ) AS name");
			 	$query->from('#__users AS a');
			 	$query->join('INNER','#__christianusers AS b ON b.userid=a.id');
			 	$query->where('a.id='.$db->quote($userId));
			 	$query->where('a.block=0');
			 	$db->setQuery($query);
				$userName = $db->loadResult();
				 return $userName;
		}
		 

			/*function to get the device information to send pushnotification to all mkarne yfriend 
			 * @param session id
			 * @return 
			 */
		protected function getNotificationDeviceInfo()
		 {
		 	$resultdata= array();
		 	$sessionId=$this->getState('pushnotification.sessionId');
		 	$userId=$this->getState('pushnotification.userId');
		 	//getting friend ids whom to send notification
		 	$friendIds=$this->getFriendIds();
		 	$implodeFriendIds=implode(",", $friendIds);   
		  
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
 			//query to get friend's device information		 	
		 	$query->select("a.id,d.device_type,d.device_token");
		 	$query->from('#__users  AS a');
		 	$query->join('LEFT','#__christianusers  AS b ON b.userid=a.id');
		 	$query->join('LEFT','#__user_device  AS d ON b.userid=d.user_id');
		 	$query->where('a.id IN('.$implodeFriendIds.')');
		 	$query->where('a.block=0');
		 	//$query->where('a.id NOT IN (SELECT dd.friend_id FROM #__friend_location AS dd WHERE dd.user_id ='.$userId.' AND dd.response =1) ');
		 	//$query->where(' (a.id IN(SELECT DISTINCT c.connectfrom FROM #__christianconnection AS c WHERE c.connectto='.$userId.' AND c.status=1) OR a.id IN(SELECT DISTINCT cc.connectto FROM #__christianconnection AS cc WHERE cc.connectfrom='.$userId.' AND cc.status=1))');
		 	$db->setQuery($query);	
		 	$results=$db->loadAssocList();
		 	
		 	return $results;
		   
		 }
		 
	
	
			/**
			 * function to get message from config file
			 * @param userdetail array,boolean
			 * @return boolean
			 */

			protected function sendPushNotification($deviceInfo,$type,$pushdata){
				$nconfig = new NConfig;
				$devicetype = strtolower($deviceInfo['device_type']);
				$response='';
				//creating message to be send
				switch($type){
					
					//to send broadcast message request for share location 
					case 0:
						$msg=array();
						$msg['aps']=array('alert'=>$pushdata['requesterMessage'],
		   			 					   'badge'=>$nconfig->badge,
		   			 					   'sound'=>$nconfig->sound);
						break;
				//to send message in response to request for share location that friend has accept the request 		
				   case 1:
						$msg=array();
						$msg['aps']=array('alert'=>$pushdata['responderMessage'],
		   			 					   'badge'=>$nconfig->badge,
		   			 					   'sound'=>$nconfig->sound);
						break;
				};
				
				//getting device token in array form
				$registrationIds=array(trim($deviceInfo['device_token']));
				
				if(strtolower(trim($devicetype))=="android")
				{
					//call android notification function
					$response=$this->androidNotification($registrationIds,$msg,$nconfig);
				
		        }
				if(strtolower(trim($devicetype))=="iphone")
				{
				
					//call iPhone notification function
					$response=$this->iosNotification($registrationIds,$msg,$nconfig);
					
		        }
		        return $response;
			}

	/**
	 * send android notification
	 * @params device_token,message,config object
	 */
	protected function androidNotification($registrationIds,$message,$config){
	         // echo "<pre>";
	         // echo json_encode($message);
	         // die;
			
		$headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $config->androidApiKey);
		$data = array(
				     		'registration_ids' =>$registrationIds,
		                     'data' => array('message'=>$message)
		);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send" );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
		$response = curl_exec($ch);  
		curl_close($ch);
		return $response;
	}

	/**
	 * send iphone notification
	 * @params device_token,message,config object
	 */
	protected function iosNotification($registrationIds,$message,$config){
		$preparemessage=json_encode($message);
		
		$ctx = stream_context_create();
		
		//get pem file path
		$pem_filepath = JPATH_ROOT.DS.'components'.DS.'com_webservice'.DS.'library'.DS.'Certificates.pem';
		
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_filepath);

		// Open a connection to the APNS server
		$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp){
			$response=false;
		}
		$response=true;
		
		foreach($registrationIds as $deviceToken){
		$msg1 = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($preparemessage)) . $preparemessage;
		}
		
		// Send it to the server
		$result = fwrite($fp, $msg1, strlen($msg1));
		if (!$result)
		$response=false;
		else
		$response=true;
		// Close the connection to the server
		fclose($fp);
		return $response;
	}
	
	/*function to update device token if canonical id send by gcm
	 * accept new device token and replace the older one 
	 * updation is valuable for future notification
	 */
		
	protected function updateDeviceToken($devicedata)
		{
				/* get user details id from userid*/
				$db = JFactory::getDBO();
                $query = $db->getQuery(true);
                
                // update some fields
                $query->update('#__user_device');
                $query->set('device_token='.$devicedata['newdevice_token']);
                $query->where('device_token='.$db->quote($devicedata['olddevice_token']));
                $db->setQuery($query);
                
                if($db->query())
                {
                	return	true;
                }else{
                	return	false;
                }
               	
        }
        
        
        /* function to update friend location who wants to share there location with user
         * @accept friend id, location, response
         * @ return true if update successfull
         */
        function respondFriendLocation()
        {
        //initianlising some variable
        	$updateData= new stdClass();
        	$message= array();
		 	$friendId=$this->getState('pushnotification.userId');
		 	$locationData=$this->getState('pushnotification.locationData');
		 	$requesterId=$locationData['requesterId'];
		 	$updateData->friendId=$friendId;
		 	$updateData->requesterId=$requesterId;
		 	$updateData->lat=$locationData['location']['lat'];
		 	$updateData->lng=$locationData['location']['long'];
		 	$updateData->response=$locationData['response'];
		 	if($locationData['response']==1)
		 	{
		 		// case1 share location request accepted
		 		$nconfig = new NConfig;
		 		$message=array();
		 		//getting the friend name who accept the sharelocation notification
		 		$username=$this->getUserNameBYId();
		 		$message['responderMessage']=str_replace('%s',$username,$nconfig->sendrespondlocation_message);
		 		//getting the device token to send notification
		 		$deviceInfos=$this->getRequesterDeviceInfo();
		 		if(count($deviceInfos)!=0){
			 			//set request id
			 			$response=$this->sendPushNotification($deviceInfos,'1',$message);
			 			$responsedecode=json_decode($response);
			 			/*if message successfully send than register the number in user friend list*/ 
						if(isset($responsedecode->results[0]->message_id))
							{
								if(isset($responsedecode->results[0]->registration_id))
									{
										/*update device_token of the user if it is changed by gcm*/
										$devicedata['olddevice_token']=$deviceInfo['device_token'];
										$devicedata['newdevice_token']=$responsedecode->results[0]->registration_id;
										$return=$this->updateDeviceToken($devicedata);
									}
							}
			return true;
		 	}else{
		 	return false;
		 	}
		 	
		 		return $this->updateFriendLocation($updateData);
		 		
		 	}
		 	
		 	}
        
         
        
        
        /* function to get requester device information
         * @accept user id 
         * @ return true
         */
        function getRequesterDeviceInfo()
        {
        	$locationData=$this->getState('pushnotification.locationData');
		 	$requesterId=$locationData['requesterId'];
		 	
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
 			//query to get friend's device information		 	
		 	$query->select("a.id,d.device_type,d.device_token");
		 	$query->from('#__users  AS a');
		 	$query->join('LEFT','#__christianusers  AS b ON b.userid=a.id');
		 	$query->join('LEFT','#__user_device  AS d ON b.userid=d.user_id');
		 	$query->where('a.id ='.$requesterId);
		 	$query->where('a.block=0');
		 	$db->setQuery($query);	
		 	$results=$db->loadAssoc();
		 	
		 	return $results;
        } 
        
        /* function to save friend location request
         * @accept user id 
         * @ return true
         */
        function saveLocationRequest()
        {
        	//initianlising some variable
        	$userId=$this->getState('pushnotification.userId');
        	//getting friend ids whom to send notification
		 		$friendIds=$this->getFriendIds();
		 		foreach($friendIds as $friendid){
        		$saveobj=new StdClass();
	        	$saveobj->id=0;
	        	$saveobj->user_id=(int)$userId;
	        	$saveobj->friend_id=(int)$friendid;
	        	$table=$this->getTable('location','ChristianconnectTable');
	        	$table->save($saveobj);
	        	}
        } 
        
         /* function to save friend location request
         * @accept user id 
         * @ return true
         */
      function updateFriendLocation($updateData)
        {
        	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$where=array();
		 	$where['user_id']=$updateData->requesterId;
		 	$where['friend_id']=$updateData->friendId;
		 	$query->update("#__friend_location AS a");
		 	$query->set('a.lat='.$db->quote($updateData->lat).',a.lng='.$db->quote($updateData->lng).',a.response='.$db->quote($updateData->response));
		 	$query->where('a.user_id='.$updateData->requesterId.' AND a.friend_id='.$updateData->friendId);
		 	$db->setQuery($query);
		 	//echo $query; die;
		 	return $db->query();		
        } 
        
			/*function to get the user's friend information whom request has been send
			 * @param session id
			 * @return 
			 */
		 function getFriendIds()
		 {
		 	$resultdata= array();
		 	$sessionId=$this->getState('pushnotification.sessionId');
		 	$userId=$this->getState('pushnotification.userId');
		 	
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select("DISTINCT a.id");
		 	$query->from('#__users  AS a');
		 	$query->join('LEFT','#__christianusers  AS b ON b.userid=a.id');
		 	$query->join('LEFT','#__user_device  AS d ON b.userid=d.user_id');
		 	$query->where('a.id NOT IN (SELECT dd.friend_id FROM #__friend_location AS dd WHERE dd.user_id ='.$userId.' AND dd.response =1) ');
		 	$query->where(' (a.id IN(SELECT DISTINCT c.connectfrom FROM #__christianconnection AS c WHERE c.connectto='.$userId.' AND c.status=1) OR a.id IN(SELECT DISTINCT cc.connectto FROM #__christianconnection AS cc WHERE cc.connectfrom='.$userId.' AND cc.status=1))');
		 	$query->where('a.block=0');
		 	$db->setQuery($query);	
		 	$results=$db->loadColumn();
		 	
		 	return $results;
		   
		 }
		 
		   /* function to get getFriendLocation
         * @accept user id 
         * @ return true
         */
        function getFriendLocation()
        {
        	$resultData=array();
        	$i=0;
		 	$userId=$this->getState('pushnotification.userId');
		 	
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select("DISTINCT a.id, IF( b.fname = ' ', b.email, CONCAT_WS( ' ', b.fname, b.lname ) ) AS name, b.profileimage, c.lat, c.lng");
		 	$query->from('#__users  AS a');
		 	$query->join('LEFT','#__christianusers  AS b ON b.userid=a.id');
		 	$query->join('INNER','#__friend_location  AS c ON c.friend_id=a.id');
		 	$query->where('c.user_id='.$userId);
		 	$query->where('a.block=0');
		 	$query->where('c.response=1');
		 	
		 	$results=$this->_getList($query);
		 	// format data as requirement
		 	foreach($results as $result){
		 		$resultData[$i]['id']=$result->id;
		 		$resultData[$i]['name']=$result->name;
		 		$resultData[$i]['image']=$result->profileimage;
		 		$resultData[$i]['location']= new stdClass();
		 		$resultData[$i]['location']->lat=$result->lat;
		 		$resultData[$i]['location']->long=$result->lng;
		 		$i++;
		 	}
		 	return $resultData;
        }

        /* function to save user device related information
         * @accept user id 
         * @ return true
         */
        function updateDeviceInfo()
        {
        	//initianlising some variable
        	$userId=$this->getState('pushnotification.userId');
        	$deviceData=$this->getState('pushnotification.deviceData');
        	
        	$infoId=$this->checkIsDeviceTokenExist($deviceData['deviceToken']);
        	//getting friend ids whom to send notification
		 		$deviceobj=new StdClass();
	        	$deviceobj->id=$infoId;
	        	$deviceobj->user_id=(int)$userId;
	        	$deviceobj->device_token=$deviceData['deviceToken'];
	        	$deviceobj->device_type=$deviceData['deviceType'];
	        	$table=$this->getTable('UserDevice','ChristianconnectTable');
	        	return $table->save($deviceobj);
	    } 
	    
	    /*function to check device token is already exist
	     * @param device token
	     * @return row id of the device token if exist
	     */
	    
	    function checkIsDeviceTokenExist($deviceToken)
	    {
	   		$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select("a.id");
		 	$query->from('#__user_device  AS a');
			$query->where('a.device_token='.$db->quote($deviceToken));
		 	$db->setQuery($query);	
		 	$id=(int)$db->loadResult();
		 	return $id;
		 }
		 
}
