<?php
/**
 * Social Login
 *
 * @version 	1.0
 * @author		SmokerMan, Arkadiy, Joomline
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access.
defined('_JEXEC') or die('(@)|(@)');

// import Joomla controller library
jimport('joomla.application.component.controller');

jimport('joomla.environment.http');
require_once('components/com_slogin/tables/slogin_usersverification.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianusers.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'christianuserstype.php');
include_once (JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'tables'.DS.'userprofileaccess.php');
//crutch to support 2 and 3 Joomla!
if(!class_exists('SLoginControllerParent')){
    if(class_exists('JControllerLegacy')){
        class SLoginControllerParent extends JControllerLegacy{}
    }
    else{
        class SLoginControllerParent extends JController{}
    }
}

/**
 * SLogin Controller
 *
 * @package        Joomla.Site
 * @subpackage    com_slogin
 */
class SLoginController extends SLoginControllerParent
{
    protected $config;

    public function __construct()
    {

        $cofig = array();
        parent::__construct($cofig);

        $this->config = JComponentHelper::getParams('com_slogin');
    }

    /**
     * user Authentication
     */
    public function auth()
    {
        $app	= JFactory::getApplication();

        $input = $app->input;

        $plugin = $input->getString('plugin', '');

        $app->setUserState('com_slogin.action.data', $input->getString('action', ''));

        $app->setUserState('com_slogin.return_url', $input->getString('return', ''));

        $redirect = JURI::base().'?option=com_slogin&task=check&plugin='.$plugin;

        $this->localAuthDebug($redirect);

        if(JPluginHelper::isEnabled('slogin_auth', $plugin))
        {
            $dispatcher	= JDispatcher::getInstance();

            JPluginHelper::importPlugin('slogin_auth', $plugin);

            $url = $dispatcher->trigger('onAuth');
            $url = $url[0];
        }
        else{
            echo 'Plugin ' . $plugin . ' not published or not installed.';
            exit;
        }

        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: " . date("r"));
        header('Location:' . $url);
    }

    /**
     * Authentication checks online donor
     * Create a new account on the website or utentifikatsiya if a user already has
     */
    public function check()
    {
        $input = JFactory::getApplication()->input;

        $plugin = $input->getString('plugin', '');

        $this->localCheckDebug($plugin);

        if(JPluginHelper::isEnabled('slogin_auth', $plugin))
        {
            $dispatcher	= JDispatcher::getInstance();

            JPluginHelper::importPlugin('slogin_auth', $plugin);

            $request = $dispatcher->trigger('onCheck');
            $request = $request[0];
        }
        else{
            echo 'Plugin ' . $plugin . ' not published or not installed.';
            exit;
        }
//code start by yogendra..
        if (isset($request->first_name))
        {   
        	$userEmail = '';
        	$result = $this->checkuservisting($request->id, $plugin); 
        	if(((int)$result ==0) || empty($result))
        	{    
        		$data['id'] = $request->id;
        		$data['first_name'] = $request->first_name;
        		$data['last_name'] = $request->last_name;
        		$data['profile_image_url'] = $request->profile_image_url;
        		$data['location'] = $request->location;
        		$data['all_request'] = $request->all_request;
        		JFactory::getApplication()->setUserState('com_slogin.linking_user.data.request', $data);
        		$oauth_verifier = JRequest::getVar('oauth_verifier');
        		$this->displayRedirect('index.php?option=com_slogin&task=verifiedcodeview&sloginid='.$request->id.'&oauth_verifier='.$oauth_verifier, $popup=true);
        	}
        	else
        	{ 
        		$userEmail = $this->findUserEmail($result); 
        	}
        }
//code edit end by yogendra..
        if (isset($request->first_name))
        {   
            $this->storeOrLogin($request->first_name, $request->last_name, $userEmail, $request->id, $request->profile_image_url,$request->location, $plugin, true, $request->all_request);
        }
    }

    /**
     * Method to query
     * @param string     $url    УРЛ
     * @param boolean     $method    false - GET, true - POST
     * @param string     $params    The parameters for the POST request
     * @return string    query result
     */
    function open_http($url, $method = false, $params = null)
    {

        if (!function_exists('curl_init')) {
            die('ERROR: CURL library not found!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $method);
        if ($method == true && isset($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
            'Content-Length: '.strlen($params),
            'Cache-Control: no-store, no-cache, must-revalidate',
            "Expires: " . date("r")
        ));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Set the name for the user
     * @param string $first_name    name
     * @param string $last_name        surname
     * @return string    Name of the user of the component parameters zovizimosti
     */
    protected function setUserName($first_name, $last_name)
    {
        if ($this->config->get('user_name', 1)) {
            $name = $first_name . ' ' . $last_name;
        } else {
            $name = $first_name;
        }

        return $name;
    }

    private function CheckUniqueName($username){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $uname = $username;
        $i = 0;
        while($uname){
            $name = ($i == 0) ? $username : $username.'-'.$i;

            $query->clear();
            $query->select($db->quoteName('username'));
            $query->from($db->quoteName('#__users'));
            $query->where($db->quoteName('username') . ' = ' . $db->quote($name));
            $db->setQuery($query, 0, 1);
            $uname = $db->loadResult();

            $i++;
        }
        return $name;
    }

    private function deleteSloginUser($slogin_id, $provider){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete();
        $query->from($db->quoteName('#__slogin_users'));
        $query->where($db->quoteName('slogin_id') . ' = ' . $db->quote($slogin_id));
        $query->where($db->quoteName('provider') . ' = ' . $db->quote($provider));
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Method to add a new account
     * @param string $username        Username account (no more than 150)
     * @param string $name           Account name
     * @param strind $email            Email
     * @param $uid                      User ID from the provider
     * @param $provider                 ID provider
     * @throws Exception
     */
    protected function storeUser($username, $name, $email, $slogin_id, $provider, $popup=false, $info=array())
    {
        $app	= JFactory::getApplication();

        //refer to confirm your ownership of soap when enabled and found
//        $userId = $this->CheckEmail($email);
//
//        if($userId){
//            $name = explode(' ', $name);
//            if(!isset($name[1])) $name[1] = '';
//
//            $data = array(
//                'email' => $email,
//                'first_name' => $name[0],
//                'last_name' => $name[1],
//                'provider' => $provider,
//                'slogin_id' => $slogin_id,
//            );
//            $app->setUserState('com_slogin.provider.data', $data);
//        }

        //setting up a group for the new user
        $user_config = JComponentHelper::getParams('com_users');
        $defaultUserGroup = $user_config->get('new_usertype', 2);

 //       $user['username'] = $this->CheckUniqueName($username);
 		$user['username'] = $email;
        $user['name'] = $name;
        $user['email'] = $email;
        $user['registerDate'] = JFactory::getDate()->toSQL();
        $user['usertype'] = 'deprecated';
        $user['groups'] = array($defaultUserGroup);

        $user_object = new JUser;

        if (!$user_object->bind($user)) {
            $this->setError($user_object->getError());
            return false;
            //throw new Exception($user_object->getError());
        }

        if (!$user_object->save()) {
            $this->setError($user_object->getError());
            return false;
            //throw new Exception($user_object->getError());
        }

        $this->storeSloginUser($user_object->id, $slogin_id, $provider);

        //Insert a new user to the table other components
        JPluginHelper::importPlugin('slogin_integration');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterStoreUser',array($user_object, $provider, $info));

        return $user_object;
    }

    /**
     * Method for user avtoriztsaii
     * @param int $id    ID user in Joomla
     */
    protected function loginUser($joomlaUser, $userdata, $provider, $info=array())
    {  $db = JFactory::getDBO();
 /*       $instance = JUser::getInstance($joomlaUser->id); 
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $db = JFactory::getDBO();

        JPluginHelper::importPlugin('slogin_integration');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoginUser',array($instance, $provider, $info));

        // If _getUser returned an error, then pass it back.
        if ($instance instanceof Exception) {
            return false;
        }

        // If the user is blocked, redirect with an error
        if ($instance->get('block') == 1) {
            $this->setError(JText::_('JERROR_NOLOGIN_BLOCKED'));
            return false;
        }

        // Mark the user as logged in
        $instance->set('guest', 0);

        $instance->set('usertype', 'deprecated');

        // Register the needed session variables
        $session->set('user', $instance);

        // Check to see the the session already exists.
        $app->checkSession();

        // Update the user related fields for the Joomla sessions table.
        $db->setQuery(
            'UPDATE ' . $db->quoteName('#__session') .
                ' SET ' . $db->quoteName('guest') . ' = ' . $db->quote($instance->get('guest')) . ',' .
                '	' . $db->quoteName('username') . ' = ' . $db->quote($instance->get('username')) . ',' .
                '	' . $db->quoteName('userid') . ' = ' . (int)$instance->get('id') .
                ' WHERE ' . $db->quoteName('session_id') . ' = ' . $db->quote($session->getId())
        );
        $db->query();

        // Hit the user last visit field
        $instance->setLastVisit();

        $dispatcher->trigger('onAfterLoginUser',array($instance, $provider, $info));
*/      
   
		$query = "SELECT password FROM #__users WHERE id='".$joomlaUser->id."';";
		$db->setQuery($query);
		$oldpass = $db->loadResult();

		jimport( 'joomla.user.helper' );
		$password = JUserHelper::genRandomPassword(5);
		$query = "UPDATE #__users SET password='".md5($password)."' WHERE id='".$joomlaUser->id."';";
		$db->setQuery($query);
		$db->query();
		$app = JFactory::getApplication();
		$credentials = array();
		$credentials['username'] = $joomlaUser->username;
		$credentials['password'] = $password;
		$options = array();
		$options['remember']	= true;
		$options['silent']		= true;
		$app->login($credentials, $options);
		$query = "UPDATE #__users SET password='".$oldpass."' WHERE id='".$joomlaUser->id."';";
		$db->setQuery($query);
		$db->query();
//store data in other table.....
		//get ChristianUsers table object
		$table =& JTable::getInstance( 'ChristianUsers', 'UsersTable' );

		$query = "SELECT id from #__christianusers where userid=".$db->Quote($joomlaUser->id);
		$result = $db->setQuery($query);
		$db->query();
		$userid = $db->loadResult();
		
		//update userinfo
		$userdata['id'] = $userid;
		$userdata['userid'] = $joomlaUser->id;
		$table->save($userdata);

		$query = "SELECT count(id) from #__christianusers where userid=".$db->Quote($joomlaUser->id);
		$result = $db->setQuery($query);
		$db->query();
		$usercount = $db->loadResult();
		if($usercount == 0){
			//$query = "insert into "
		}

		//get ChristianUsersTypes table object
		$row =& JTable::getInstance( 'Christianuserstype', 'UsersTable' );
		//saving the data into the christianuserstype table....for saving the FB id and usertype..
		$query1 = "SELECT id from #__christianuserstype where userid=".$db->Quote($joomlaUser->id);
		$result = $db->setQuery($query1);
		$db->query();
		$rowid = $db->loadResult();
		$userdatatype['userid'] = $joomlaUser->id;
		$userdatatype['fbtwitterid'] = $userdata['fbid'];
		$userdatatype['type'] = 'TW';
		$row->load($rowid);
		  if (!$row->save($userdatatype)) { 

		  } 		
			/*
			 * below code for saving the access level for the user profile field in Userprofileaccess table...
			 * we are saving the access of the fields according to the user id here.....
			 *
			 */
		  
		$christianuserprofileaccess =& JTable::getInstance('Userprofileaccess','UsersTable'); 
		$query2 = "SELECT id from #__userprofileaccess where userid=".$db->Quote($joomlaUser->id);
		$db->setQuery($query2);
		$db->query();
		$rowid1 = $db->loadResult();
		
		$accessdata['userid'] = $joomlaUser->id;
		$christianuserprofileaccess->load($rowid1);
		
		if (!$christianuserprofileaccess->save($accessdata)) { 

		  } 
		
    	return;
    }

    /**
     * Method to display the special redirect, and close the window
     */
    public function displayRedirect($redirect='index.php', $popup=false, $msg = '', $msgType = 'message')
    {   
        if($popup){
            $session = JFactory::getSession();
            $redirect = base64_encode(JRoute::_($redirect));
            $session->set('slogin_return', JRoute::_($redirect));
            $view = $this->getView('Redirect', 'html');
            $view->display();
            exit;
        }
        else{
            $app = JFactory::getApplication();
            $app->redirect(JRoute::_($redirect), $msg, $msgType);
        }
    }

    /**
     * Method for setting the error
     * @param string $error    bug
     */
    public function setError($error)
    {
        $session = JFactory::getSession();
        $error = $session->set('slogin_errors', $error);
        $this->displayRedirect();
        return false;
    }

    /**
     * Special redirect takes messages from the session
     * @return boolean
     */
    public function sredirect()
    {
        $session = JFactory::getSession();
        $app = JFactory::getApplication();

        $redirect = JRoute::_(base64_decode($session->get('slogin_return', '')));
        $session->clear('slogin_return');
        if ($error = $session->get('slogin_errors', null)) {
            $session->clear('slogin_errors');
            $app->redirect($redirect, $error, 'error');
            return false;
        } else {
            $app->redirect($redirect);
            return false;
        }


    }

    // check to see if already registered user with this email
    public function CheckEmail($email)
    {
        // Initialise some variables
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__users'));
        $query->where($db->quoteName('email') . ' = ' . $db->quote($email));
        $db->setQuery($query, 0, 1);
        $userId = $db->loadResult();

        if (!$userId) {
            return false;
        } else {
            return $userId;
        }
    }

    // check to see if already registered user with this email
    public function GetUserId($id, $provider)
    {
        // Initialise some variables
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('user_id'));
        $query->from($db->quoteName('#__slogin_users'));
        $query->where($db->quoteName('slogin_id') . ' = ' . $db->quote($id));
        $query->where($db->quoteName('provider') . ' = ' . $db->quote($provider));
        $db->setQuery($query, 0, 1);
        $userId = $db->loadResult();
        return $userId;
    }

    public function GetSloginStringId($slogin_id, $user_id, $provider)
    {
        // Initialise some variables
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__slogin_users'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id));
        $query->where($db->quoteName('slogin_id') . ' = ' . $db->quote($slogin_id));
        $query->where($db->quoteName('provider') . ' = ' . $db->quote($provider));
        $db->setQuery($query, 0, 1);
        $userId = $db->loadResult();
        return $userId;
    }

    /**
     * Binding to an existing user login if coincided email
     */
    public function join_mail()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $input = new JInput;

        $app = JFactory::getApplication();
        $appRedirect = $app->getUserState('com_slogin.return_url');
        $UserState = $app->getUserState('com_slogin.comparison_user.data');

        $msg = '';
        $user_id = $input->Get('user_id', 0, 'INT');
        $slogin_id = $input->Get('slogin_id', '', 'STRING');
        $provider = $input->Get('provider', '', 'STRING');

        // Populate the data array:
        $data = array();
        $return = base64_decode($appRedirect);
        $data['username'] = $input->Get('username', '', 'username');
        $data['password'] = $input->Get('password', '', JREQUEST_ALLOWRAW);

        // Get the log in options.
        $options = array();
        $options['remember'] = $input->Get('remember', false);
        $options['return'] = $return;

        // Get the log in credentials.
        $credentials = array();
        $credentials['username'] = $data['username'];
        $credentials['password'] = $data['password'];

        // Perform the log in.
        // Check if the log in succeeded.
        if (true === $app->login($credentials, $options)) {

            $app->setUserState('com_slogin.return_url', $appRedirect);
            $app->setUserState('com_slogin.comparison_user.data', $UserState);

            $joomlaUserId = JFactory::getUser()->id;

            //remove unnecessary user
            if($user_id != $joomlaUserId){

                //ask is, if the user has other providers
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from($db->quoteName('#__slogin_users'));
                $query->where($db->quoteName('user_id') . ' = ' . $db->quote($joomlaUserId));
                $query->where($db->quoteName('provider') . ' != ' . $db->quote($provider));
                $db->setQuery($query);
                $count = (int)$db->loadResult();

                //if other providers do not, then remove user
                if($count == 0){
                    $user_object = new JUser;
                    $user_object->id = $user_id;
                    $user_object->delete();
                }
            }
            //remove the old line by
            $this->deleteSloginUser($slogin_id, $provider);
            //the data in # __slogin_user
            $store = $this->storeSloginUser($joomlaUserId, $slogin_id, $provider);

            if(!$store){
                $msg = JText::_('ERROR_JOIN_MAIL');
            }

            $app->redirect(JRoute::_($return, false), $msg);
        } else {
            $app->setUserState('com_slogin.return_url', $appRedirect);
            $app->setUserState('com_slogin.comparison_user.data', $UserState);
            $app->redirect(JRoute::_('index.php?option=com_slogin&view=linking_user', false));
        }
    }

    public function recallpass(){
        $app	= JFactory::getApplication();
        $app->logout();
        $app->redirect(JRoute::_('index.php?option=com_users&view=reset'));
    }

    private function storeSloginUser($user_id, $slogin_id, $provider){
        if(empty($user_id) || empty($slogin_id) || empty($provider)){
            return false;
        }
        JTable::addIncludePath(JPATH_COMPONENT . '/tables');
        $SloginUser = &JTable::getInstance('slogin_users', 'SloginTable');
        $SloginUser->user_id = $user_id;
        $SloginUser->slogin_id = $slogin_id;
        $SloginUser->provider = $provider;
        $result = $SloginUser->store();
        return $result;
    }

    //Mile after checking the user manual filling
    public function check_mail()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app = JFactory::getApplication();
        $input = new JInput;

        $first_name =   $input->Get('first_name',   '', 'STRING');
        $last_name =    $input->Get('last_name',    '', 'STRING');
        $email =        $input->Get('email',        '', 'STRING');
        $slogin_id =    $input->Get('slogin_id',    '', 'STRING');
        $provider =     $input->Get('provider',     '', 'STRING');
        $info =         $app->getUserState('com_slogin.provider.info');

        //small validation
        if(empty($email)|| filter_var($email, FILTER_VALIDATE_EMAIL) === false){
             $this->queryEmail($first_name, $last_name, $email, $slogin_id, $provider);
        }
        else{
             $this->storeOrLogin($first_name, $last_name, $email, $slogin_id, $provider, false, $info);
        }
    }

    protected function storeOrLogin($first_name, $last_name, $email, $slogin_id, $profile_image_url, $location, $provider, $popup=false, $info = array())
    {   
    	//prepareing the data for the other table update.....
      	$userdata['fname'] = $first_name;
		$userdata['lname'] = $last_name;
		$userdata['email'] = $email;
		$userdata['city'] = $location;
		$userdata['profileimage'] = $profile_image_url;
		$userdata['fbid'] = $slogin_id;
 
        //check for empty record by id
        if(empty($slogin_id)){
            echo '<p>Provider return empty user code.</p>';
            die;
        }

        $msg = $msgType = '';

        $app = JFactory::getApplication();

        $app->setUserState('com_slogin.provider.info', $info);

        //if allowed to merge - merge
        if($app->getUserState('com_slogin.action.data') == 'fusion'){
            $this->fusion($slogin_id, $provider, $popup);
        }

        //check whether there is a user with that provider, and Widom
        $sloginUserId = $this->GetUserId($slogin_id, $provider);

        //Redirecting the user of the module
        $appReturn = $app->getUserState('com_slogin.return_url');
        $return = base64_decode($appReturn);
        //if no such user, we create
        if (!$sloginUserId) {
            //user login (edit by yogendra and below line commented..)
        //    $username = $this->transliterate($first_name.'-'.$last_name.'-'.$provider);
			$username = $email;
            //user name
       //     $name = $this->setUserName($first_name,  $last_name);
			$name = $first_name;
            //written by the user in the table and Joomla component
            $joomlaUser = $this->storeUser($username, $name, $email, $slogin_id, $provider, $popup, $info);

            if($joomlaUser->id > 0)
            {
                $data = array(
                    'email' => $email,
                    'id' => $joomlaUserId,
                    'provider' => $provider,
                    'slogin_id' => $slogin_id,
                );
                $app->setUserState('com_slogin.comparison_user.data', $data);

                $model = parent::getModel('Linking_user', 'SloginModel');

                $return = base64_decode($model->getReturnURL($this->config, 'after_reg_redirect'));

                //Login If the correct user ID
  //              $this->loginUser($joomlaUserId, $provider, $info);

                $app->setUserState('com_slogin.return_url', $appReturn);
            }
        }
        else {   //or login
           // $this->loginUser($sloginUserId, $provider, $info);
           jimport( 'joomla.user.helper' );
           $joomlaUser = JFactory::getUser((int)$sloginUserId); 
        } 
        $returnpath = $this->loginUser($joomlaUser, $userdata, $provider, $info);
        $this->displayRedirect($return, $popup, $msg);
    }

    /**  merging users
     * @param null $slogin_id - id issued by ISP
     * @param null $provider  - provider
     */
    protected function fusion($slogin_id= null, $provider= null, $popup=false)
    {
        $app = JFactory::getApplication();
        $app->setUserState('com_slogin.action.data', '');

        //the id of the current user
        $user_id = JFactory::getUser()->id;

        if((int)$user_id == 0 || !$slogin_id || !$provider){
            return;
        }

        //delete old accounts from # __slogin_users
        $this->deleteSloginUser($slogin_id, $provider);

        //add a new user account in # __slogin_users
        $store = $this->storeSloginUser($user_id, $slogin_id, $provider);

        $link = 'index.php?option=com_slogin&view=fusion';

        $this->displayRedirect($link, $popup);
    }

    public function detach_provider()
    {
        $input = new JInput;
        $provider = $input->Get('plugin', '', 'STRING');
        //the id of the current user
        $user_id = JFactory::getUser()->id;

        $link = 'index.php?option=com_slogin&view=fusion';

        if((int)$user_id == 0 ){
            $this->displayRedirect($link);
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete();
        $query->from($db->quoteName('#__slogin_users'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id));
        $query->where($db->quoteName('provider') . ' = ' . $db->quote($provider));
        $db->setQuery($query);
        $db->query();

        $this->displayRedirect($link);
    }

    private function transliterate($str){

        $trans = array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
            "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t",
            "у"=>"y","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch", "ш"=>"sh","щ"=>"shh",
            "ы"=>"i","э"=>"e","ю"=>"u","я"=>"ya","ї"=>"i","'"=>"","ь"=>"","Ь"=>"",
            "ъ"=>"","Ъ"=>"","і"=>"i","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
            "Е"=>"E", "Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"I","К"=>"K", "Л"=>"L",
            "М"=>"M","Н"=>"N","О"=>"O","П"=>"P", "Р"=>"R","С"=>"S","Т"=>"T","У"=>"Y",
            "Ф"=>"F", "Х"=>"H","Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh", "Ы"=>"I","Э"=>"E",
            "Ю"=>"U","Я"=>"Ya","Ї"=>"I","І"=>"I","_"=>"-");

        $res=str_replace(" ","-",strtr($str,$trans));

        //If necessary, cut out all but the Latin letters, digits, and hyphens (for example to form login)
        $res=preg_replace("|[^a-zA-Z0-9-]|","",$res);

        return $res;
    }

    protected function localAuthDebug($redirect){
        if($this->config->get('local_debug', 0) == 1){
            $app = JFactory::getApplication();
            $app->redirect(JRoute::_($redirect));
        }
    }

    protected function localCheckDebug($provider){
        if($this->config->get('local_debug', 0) == 1){
            $slogin_id =  '12345678910';
            $this->storeOrLogin('Вася', 'Пупкин', '', $slogin_id, $provider, true);
        }
    }

    protected function queryEmail($first_name, $last_name, $email, $slogin_id, $provider, $popup=false)
    {
        $app	= JFactory::getApplication();
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'slogin_id' => $slogin_id,
            'provider' => $provider
        );
        $app->setUserState('com_slogin.provider.data', $data);
    }

    private function getFreeMail($email){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $umail = $email;
        $parts = explode('@', $email);

        $i = 0;
        while($umail){
            $mail = ($i == 0) ? $email : $parts[0].'-'.$i.'@'.$parts[1];

            $query->clear();
            $query->select($db->quoteName('email'));
            $query->from($db->quoteName('#__users'));
            $query->where($db->quoteName('email') . ' = ' . $db->quote($mail));
            $db->setQuery($query, 0, 1);
            $umail = $db->loadResult();

            $i++;
        }
        return $mail;
    }

    private function getUserIdByMail($mail){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__users'));
        $query->where($db->quoteName('email') . ' = ' . $db->quote($mail));
        $db->setQuery($query, 0, 1);
        $id = $db->loadResult();
        return $id;
    }
/*
 * function is edit by yogendra to check the user visiting by Twitter login..
 */
    private function checkuservisting($twitterid, $provider)
    {
        // Initialise some variables
        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('a.user_id');
        $query->from('#__slogin_users as a');
        $query->where('a.slogin_id='.$db->quote($twitterid));
        $query->where('a.provider='.$db->quote($provider));
        $db->setQuery($query); 
        $userId = $db->loadResult(); 
        return $userId;
    }
/*
 * function is edit by yogendra to finding the user email corrosponding to userid.....
 */
    private function findUserEmail($userid)
    {
        // Initialise some variables
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('email'));
        $query->from($db->quoteName('#__users'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($userid));
        $db->setQuery($query, 0, 1);
        $userEmail = $db->loadResult();
        return $userEmail;
    	
    }
/*
 * generating the a verification code and email to the user trying to login..
 */    
    public function generateEmailCode()
    {
      $email = JRequest::getvar('email');
      $sloginid = JRequest::getvar('sloginid');
      jimport( 'joomla.user.helper' );
	  $password = JUserHelper::genRandomPassword(8);

	   		$mail =& JFactory::getMailer();
	 		$app		= JFactory::getApplication();  		
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$mail->setSubject(JText::_('COM_USERS_VERIFICATION_CODE'));
			$text =  JText::_('COM_USERS_VERIFICATION_CODE').' '.$password; 
			$mail->setBody($text);
			$mail->IsHTML(true);
			$mail->addRecipient($email);
			$mail->setSender($mailfrom, $fromname);
			$mail->Send(); 
	  
	 //saving the verification code in DB for verification..
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__slogin_usersverification'));
        $query->where($db->quoteName('sloginid') . ' = ' . $db->quote($sloginid));
        $db->setQuery($query, 0, 1);
        $verifiedId = $db->loadResult();
        
		$verifydata['id']= $verifiedId;
		$verifydata['sloginid']= $sloginid;
		$verifydata['verificationcode']= $password;
		
        $verifyTable =& JTable::getInstance('Slogin_usersverification','SloginTable'); 
		if (!$verifyTable->save($verifydata)) { 
			$this->setError(JText::sprintf('CODE_SAVE_FAILED', $verifyTable->getError()));
			return false;
		  }
       return  true;
    }
/*
 * close the popup..and redirect to email verification view....
 */    
    public function verifiedcodeview(){
    	$sloginid = JRequest::getVar('sloginid');
    	$oauth_verifier = JRequest::getVar('oauth_verifier');
    	$view = $this->getView('linking_user', 'html');
	    $view->setLayout('emailverification');
	    $view->assign('sloginid', $sloginid);
	    $view->assign('oauth_verifier', $oauth_verifier);
	    $view->emailverification();
    	
    }
 /*
  * function for checking the verification code is right..ajax call
  */   
    public function codeverification()
    {
    	$email = JRequest::getVar('email');
    	$sloginid = JRequest::getVar('sloginid');
    	$code = JRequest::getVar('code');
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__slogin_usersverification');
        $query->where('sloginid =' . $db->quote($sloginid));
        $query->where('verificationcode =' . $db->quote($code));
        $db->setQuery($query); 
        $id = $db->loadResult();
     //   $result['test'] = $id;
        echo json_encode($id);
    }
/*
 * function for saving the user login first time from twitter.......
 */
    public function emailverification()
    {   
    	$request = JFactory::getApplication()->getUserState('com_slogin.linking_user.data.request'); 
    	$email = JRequest::getVar('email_verification');
    	$request['email'] = $email;
    	$object = (object)$request;
    	$plugin = 'twitter';
    	$this->storeOrLogin($object->first_name, $object->last_name, $object->email, $object->id, $object->profile_image_url, $object->location, $plugin, true, $object->all_request);
    	
    }
/*
* 
* function for checking the email address is already exists.. 
*/
    public function emailcheckunique()
    {
    	$email = JRequest::getVar('email');
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__users');
        $query->where('email =' . $db->quote($email));
        $db->setQuery($query); 
        $id = $db->loadResult();
        echo json_encode($id);
    }
}

class SloginRequest {
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $id = 0;
    public $real_name = '';
    public $sex = '';
    public $display_name = '';
    public $birthday = '';
    public $avatar = '';
    public $location = '';
    public $profile_image_url = '';
    public $all_request = null;
}
