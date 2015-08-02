<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.application.component.helper');
jimport('joomla.event.dispatcher');
define('PROFILE_IMAGE_PATH','components/com_users/images/profileimage/thumbs/');
require_once('components/com_users/tables/userprofileaccess.php');
require_once('components/com_users/tables/christianusers.php');
require_once('components/com_users/tables/userchurch.php');
require_once('components/com_users/helpers/users.php');
require_once('components/com_users/tables/church.php');
/**
 * Profile model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersModelProfile extends JModelForm
{
	/**
	 * @var		object	The user profile data.
	 * @since	1.6
	 */
	protected $data;

	/**
	 * Method to check in a user.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkin($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int)$this->getState('user.id');

		if ($userId) {
			// Initialise the table with JUser.
			$table = JTable::getInstance('User');

			// Attempt to check the row in.
			if (!$table->checkin($userId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check out a user for editing.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkout($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int)$this->getState('user.id');

		if ($userId) {
			// Initialise the table with JUser.
			$table = JTable::getInstance('User');

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $userId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the profile form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		if ($this->data === null) {

			$userId = $this->getState('user.id');

			// Initialise the table with JUser.
			$this->data	= new JUser($userId);

			// Set the base user data.
			$this->data->email1 = $this->data->get('email');
			$this->data->email2 = $this->data->get('email');

			// Override the base user data with any data in the session.
			$temp = (array)JFactory::getApplication()->getUserState('com_users.edit.profile.data', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			$registry = new JRegistry($this->data->params);
			$this->data->params = $registry->toArray();

			// Get the dispatcher and load the users plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.profile', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		if (!JComponentHelper::getParams('com_users')->get('change_login_name'))
		{
			$form->setFieldAttribute('username', 'class', '');
			$form->setFieldAttribute('username', 'filter', '');
			$form->setFieldAttribute('username', 'description', 'COM_USERS_PROFILE_NOCHANGE_USERNAME_DESC');
			$form->setFieldAttribute('username', 'validate', '');
			$form->setFieldAttribute('username', 'message', '');
			$form->setFieldAttribute('username', 'readonly', 'true');
			$form->setFieldAttribute('username', 'required', 'false');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return $this->getData();
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		if (JComponentHelper::getParams('com_users')->get('frontend_userparams'))
		{
			$form->loadFile('frontend', false);
			if (JFactory::getUser()->authorise('core.login.admin')) {
				$form->loadFile('frontend_admin', false);
			}
		}
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$params	= JFactory::getApplication()->getParams('com_users');

		// Get the user id.
		$userId = JFactory::getApplication()->getUserState('com_users.edit.profile.id');
		$userId = !empty($userId) ? $userId : (int)JFactory::getUser()->get('id');

		// Set the user id.
		$this->setState('user.id', $userId);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data, $accessdata, $file, $localchurch, $otherchurch)
	{   
		$userId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('user.id');

		$user = new JUser($userId);
		//user email for checking in the else part for sending the mail.
		$userpremail = $user->email;
		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];
		
		// Unset the username if it should not be overwritten
		if (!JComponentHelper::getParams('com_users')->get('change_login_name'))
		{
			unset($data['username']);
		}

		// Unset the block so it does not get overwritten
		unset($data['block']);

		// Unset the sendEmail so it does not get overwritten
		unset($data['sendEmail']);
		//override the username by new emailid coming from form data..
		$data['username']		= $data['email1'];
		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('USERS PROFILE BIND FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Null the user groups so they don't get overwritten
		$user->groups = null;

		// Store the data. 
		// in else part we will save the data into the christianuser and userprofileaccess table...corrosponding theuser..
		if (!$user->save()) {
			$this->setError($user->getError());
			return false;
		}
		else{ 
			//checking if a user change his/her email id then sending a notification to admin of the site...
			if($userpremail != $data['email'])
			{
				$this->sendemailtoadmin($data['email']);
			}
			// save other updated info to the christianusers table	
			$address = $data['address1']." ".$data['address2']." ".$data['postcode']." ".$data['city']." ".$data['state']." ".$data['country'];
			// find the latitude and longitude...
			$locationdata = $this->getLatLon($address);
			$data['lat'] = $locationdata['lat'];
			$data['lng'] = $locationdata['lng'];
			// id filed is removing from data array because we have to load the christianconnect table data to 
			//userprofileid of the christianconnect table..(removing the id(userid) field from array of user table..o/w the id field will conflict to christianuser tabel id) 
			$remove = array_flip(array('id')); 
			$output = array_diff_key($data, $remove);
			
			//checking the other religion value is not null, if not then we will make a new entry in religion table and will send a notification to admin....
			if(!empty($data['otherreligion']))
			{
				$religionid = $this->otherreligion($data['otherreligion']);
				$output['religion'] = $religionid;
				$this->sendmailtoadmin($religionid, $output['religion']);
				
			}
			else{
				// if religion is not empty then save the id of selected religion not name..(see in script.js) 
				if(!empty($data['religion']))
				{
					$output['religion'] = $data['religionid'];
				}
			}
			
			if(!empty($file['name']))
			{
				$newfilename = UsersHelper::ImageUpload($file);
			}		
			if(!empty($newfilename)) $output['profileimage'] = JURI::base().PROFILE_IMAGE_PATH.$newfilename;
			
			$christianuser =& $this->getTable('ChristianUsers','UsersTable');
			$christianuser->load($output['userprofileid']); 
			$output['userid'] = $user->id; 
			

			if (!$christianuser->save($output)) { 
				$this->setError(JText::sprintf('COM_USER_PROFILE_SAVE_FAILED', $christianuser->getError()));
				return false;
			} 
			/*
			 * below code for saving the access level for the user profile field in Userprofileaccess table...
			 * we are saving the access of the fields according to the user id here.....
			 * finding the id from the main output array
			 */
		  $christianuserprofileaccess =& $this->getTable('Userprofileaccess','UsersTable'); 
		  $christianuserprofileaccess->load($output['userprofileaccessid']);
		
		  if (!$christianuserprofileaccess->save($accessdata)) { 
				$this->setError(JText::sprintf('COM_USER_PROFILE_SAVE_FAILED', $christianuserprofileaccess->getError()));
				return false;
		  } 
			/*
			 * code for saving the user local church...
			 */
		  if(!empty($localchurch))
		  {
		  	$localchurch = $this->localchurchrepeat($localchurch);
		  }
		  if(!empty($otherchurch))
		  {
		  	$otherchurch = $this->otherchurchrepeat($otherchurch);
		  }
		  
		  $christianuserchurch =& $this->getTable('Userchurch','UsersTable'); 
		  $churchdata['userid'] =  $user->id;
		  $churchdata['localchurch'] = $localchurch;
		  $churchdata['otherchurch'] = $otherchurch;
		  $christianuserchurch->load($output['userchurchid']);
		  if(!empty($churchdata['localchurch']) || !empty($churchdata['otherchurch'])){
			  if (!$christianuserchurch->save($churchdata)) { 
					$this->setError(JText::sprintf('COM_USER_PROFILE_SAVE_FAILED', $christianuserchurch->getError()));
					return false;
			  } 
		  }
		}
		
		return $user->id;
	}
	
	/*
	 * getting the extra information data for editing the profile from christianusers table.......(fname,lname, email etc....)
	 * calling from view.html.php of profile view...
	 */
	
	public function getChristiandata()
	{
		$userId = $this->getState('user.id'); 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__christianusers AS a');
		$query->where('a.userid ='.$userId );
     	// Get the options.
		$db->setQuery($query);
        $options = $db->loadObjectList(); 
        return $options; 
	}

	/*
	 * getting the accessibilty information data for set the accessibilty on editing profile
	 * calling from view.html.php of profile view...
	 */
	
	public function getChristianAccessibilty()
	{
		$userId = $this->getState('user.id'); 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.*');
		$query->from('#__userprofileaccess AS a');
		$query->where('a.userid ='.$userId );
     	// Get the options.
		$db->setQuery($query);
        $optionsarray = $db->loadObjectList(); 
        return $optionsarray; 
		
	}
/*
 * 
 * Function added for Lat and Long...
 */	
    public function getLatLon($address) {
        $address = str_replace(" ", "+", $address);
        $output = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=false", true);
        $resultInArray = json_decode(utf8_encode($output), true);
      
        return $resultInArray['results'][0]['geometry']['location'];
    }
/*
 * 
 * Function for getting the religion for the autocomplete functionality...
 */	
    public function getReligion($ch){
    	
		$userId = JFactory::getUser()->id; 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
    	$query->select('a.id, a.name');
    	$query->from('#__userreligion AS a');
    	$query->where('a.published = 1');
    	$query->where('a.name LIKE '.'"'.$ch.'%"');
     	// Get the options.
		$db->setQuery($query);
        $optionsarray = $db->loadObjectList(); 
        return $optionsarray; 
    	
    }
/*
 * saving the other religion if user inserting a new religion...making a new entry in religion table..
 */    
    public function otherreligion($religion)
    {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
    	$query  = 'insert into #__userreligion (name) values('.$db->Quote($religion).')';
     	// Get the options.
		$db->setQuery($query);
		$db->query();
        $lastid = $db->insertid();
    	return $lastid;
    }
/*
 * function for sending the mail to admin when a user edit a new religion....
 */    
   public function sendmailtoadmin($id, $name)
   {
   		$mail =& JFactory::getMailer();
 		$app		= JFactory::getApplication();  		
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
   		
		$mail->setSubject(JText::_('COM_USERS_RELIGION_ADDED'));
		$text = JText::_('COM_USERS_USER_ADDED').' '.$email .' '.JText::_('COM_USER_NEW_RELIGION_ADDED').' '.$name.' '.JText::_('COM_USERS_RELIGION ADDED').' '.$id ; 
		$mail->setBody($text);
		$mail->IsHTML(true);
		$joomla_config = new JConfig();
		$mail->addRecipient($mailfrom);
		$mail->setSender($mailfrom, $fromname);
		$mail->Send();
   }
/*
 * function for getting the local church according to the character typed..
 */  
   public function getLocalChurch($church)
   {
		$userId = JFactory::getUser()->id; 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
    	$query->select('a.id, a.cname');
    	$query->from('#__church AS a');
    	$query->where('a.published = 1');
    	$query->where('a.cname LIKE '.'"'.$church.'%"');
     	// Get the options.
		$db->setQuery($query);
        $optionsarray = $db->loadObjectList(); 
        return $optionsarray; 
   	
   }
   /*
    * function for remove the repetation...
    */
   public function localchurchrepeat($localchurch)
   {
      $church = explode(",", $localchurch);
      $church = array_unique($church);
      $lastchurch = implode(',', $church);
      return $lastchurch;
   }
   /*
    * function for remove the repetation from other church...
    */
   public function otherchurchrepeat($otherchurch)
   {
      $otherchurch = explode(",", $otherchurch);
      $lastchurch = array_unique($otherchurch);
      $lastotherchurch = implode(',', $lastchurch);
      return $lastotherchurch;
   }
   
   /*
    * getting the user church data function calling from view html file..
    * 
    */
   public function getUserChurch()
   {
		$userId = JFactory::getUser()->id; 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
    	$query->select('a.id, a.localchurch, a.otherchurch');
    	$query->from('#__userchurch AS a');
      	$query->where('a.userid ='.$userId);
     	// Get the options.
		$db->setQuery($query);
        $optionsarray = $db->loadObjectList(); 
        $curchname = $this->getchurchname($optionsarray[0]->localchurch);
        $othercurchname = $this->getchurchname($optionsarray[0]->otherchurch);
        $optionsarray[0]->churchname = $curchname;
        $optionsarray[0]->otherchurchname = $othercurchname; 
        return $optionsarray; 
   }
   /*
    * getting church name..
    */
   private function getchurchname($churchstring)
   {
     $churchid = explode(",", $churchstring);
     $db		= JFactory::getDbo();
     $result = array();
     foreach($churchid as $id)
     {
		$query	= $db->getQuery(true);
    	$query->select('a.cname');
    	$query->from('#__church AS a');
      	$query->where('a.id ='.$id);
     	// Get the options.
		$db->setQuery($query);
        $name = $db->loadResult(); 
        if(!empty($name))
     	$result[] = $name;
     }
     return $result;
   }
   /*
    * gettinmg the new church form........
    */
   	public function getChurchForm($data = array(), $loadData = true)
	{    $app	= JFactory::getApplication();
		// Get the form.
		$form = $this->loadForm('com_users.newchurch', 'newchurch', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
/*
 * function for sending the mail to admin when a user changing his/her email id....
 */    
   public function sendemailtoadmin($newemail)
   {
   		$user = JFactory::getUser();
   		$mail =& JFactory::getMailer();
 		$app		= JFactory::getApplication();  		
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
   		
		$mail->setSubject(JText::_('COM_USERS_EMAIL_CHANGED'));
		$text = $user->name.' '. JText::_('COM_USERS_USER_CHANGED_EMAIL').' '. $user->email.' '. JText::_('COM_USERS_TO_EMAIL').' '. $newemail.' '.JText::_('COM_USERS_EMAIL_WITH').' '.$user->id ; 
		$mail->setBody($text);
		$mail->IsHTML(true);
		$joomla_config = new JConfig();
		$mail->addRecipient($mailfrom);
		$mail->setSender($mailfrom, $fromname);
		$mail->Send(); 
   }
/*
 * function for saving a new church and send notification to admin of the site..
 */	
   public function savenewchurch($churchdata, $churchfiledata)
   {	
		//find lat and long...
		$address = $churchdata['address1']." ".$churchdata['address2']." ".$churchdata['postcode']." ".$churchdata['city']." ".$churchdata['state']." ".$churchdata['country'];
	    $locationdata = $this->getLatLon($address); 
	    $churchdata['lat']= $locationdata['lat'];
	    $churchdata['lng']= $locationdata['lng']; 
		//check if the file is exists uploading the images and creating those thumbs..
		if(!empty($churchfiledata['profileimage1']['name']))
		{
			$profileimage1 = UsersHelper::ChurchImageUpload($churchfiledata['profileimage1']);
			$churchdata['profileimage1'] = $profileimage1;
		}
		if(!empty($churchfiledata['profileimage2']['name']))
		{
			$profileimage2 = UsersHelper::ChurchImageUpload($churchfiledata['profileimage2']);
			$churchdata['profileimage2'] = $profileimage2;
		}
		if(!empty($churchfiledata['logo']['name']))
		{
			$logoimage = UsersHelper::ChurchImageUpload($churchfiledata['logo']);
			$churchdata['logo'] = $logoimage;
		}
		//Now saving the data in church table..
		    $christianchurch =& $this->getTable('Church','UsersTable'); 
		if (!$christianchurch->save($churchdata)) { 
			$this->setError(JText::sprintf('COM_USER_CHURCH_SAVE_FAILED', $christianchurch->getError()));
			return false;
		  }
		else{ 
	   		$user = JFactory::getUser();
	   		$mail =& JFactory::getMailer();
	 		$app		= JFactory::getApplication();  		
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$mail->setSubject(JText::_('COM_USERS_NEW_CHURCH_ADDED_SUBJECT'));
			$text = $user->name.' '. JText::_('COM_USERS_NEW_CHURCH_ADDED').' '. $churchdata['cname'].' '.  JText::_('COM_USERS_NEW_CHURCH_ADDED_WITH_ID').' '.$christianchurch->id; 
			$mail->setBody($text);
			$mail->IsHTML(true);
			$joomla_config = new JConfig();
			$mail->addRecipient($mailfrom);
			$mail->setSender($mailfrom, $fromname);
			$mail->Send(); 
		}   
		return true;
   }
}
