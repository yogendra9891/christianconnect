<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';
require_once('components/com_users/helpers/users.php');
/**
 * Profile controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerProfile extends UsersController
{
	/**
	 * Method to check out a user for editing and redirect to the edit form.
	 *
	 * @since	1.6
	 */
	public function edit()
	{
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$loginUserId	= (int) $user->get('id');

		// Get the previous user id (if any) and the current user id.
		$previousId = (int) $app->getUserState('com_users.edit.profile.id');
		$userId	= (int) JRequest::getInt('user_id', null, '', 'array');

		// Check if the user is trying to edit another users profile.
		if ($userId != $loginUserId) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_users.edit.profile.id', $userId);

		// Get the model.
		$model = $this->getModel('Profile', 'UsersModel');

		// Check out the user.
		if ($userId) {
			$model->checkout($userId);
		}

		// Check in the previous user.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save()
	{   
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('Profile', 'UsersModel');
		$user	= JFactory::getUser();
		$userId	= (int) $user->get('id');

		// Get the user data.
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$file = JRequest::getVar('changeprofileimage', null, 'files','array');
		//getting the data of the accesibility..edit by yogendra...
		$accessdata = JRequest::getVar('access', array(), 'post', 'array'); 
		$accessdata['userid'] = $userId;
		//getting the curch data....
		$localchurch = JRequest::getVar('localchurch', null);
		$otherchurch = JRequest::getVar('otherchurch', null);
		// Force the ID to this user.
		$data['id'] = $userId;

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.(we have commented this due to addition of new fields in profile edit form o/w it will not validate those.edit by yogendra.)
        //$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_users.edit.profile.data', $data);

			// Redirect back to the edit screen.
			$userId = (int) $app->getUserState('com_users.edit.profile.id');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$userId, false));
			return false;
		}

		// Attempt to save the data. 
		//second argumant passed for saving the accessibilty....in userprofileaccess table. edit by yogendra.
		//third parameter is for uplaoding the profile picture if edited.
		$return	= $model->save($data, $accessdata, $file, $localchurch, $otherchurch);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_users.edit.profile.data', $data);

			// Redirect back to the edit screen.
			$userId = (int)$app->getUserState('com_users.edit.profile.id');
			$this->setMessage(JText::sprintf('COM_USERS_PROFILE_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$userId, false));
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->getTask()) {
			case 'apply':
				// Check out the profile.
				$app->setUserState('com_users.edit.profile.id', $return);
				$model->checkout($return);

				// Redirect back to the edit screen.
				$this->setMessage(JText::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&view=profile&layout=edit&hidemainmenu=1', false));
				break;

			default:
				// Check in the profile.
				$userId = (int)$app->getUserState('com_users.edit.profile.id');
				if ($userId) {
					$model->checkin($userId);
				}

				// Clear the profile id from the session.
				$app->setUserState('com_users.edit.profile.id', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&view=profile&user_id='.$return, false));
				break;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.edit.profile.data', null);
	}
	/*
	 * Function for new church......
	 */
	public function newchurchform()
	{  
		$view = $this->getView('profile', 'html');
		$model = $this->getModel('Profile', 'UsersModel'); 
		$view->setLayout('newchurch');
    	$view->setModel($this->getModel('Profile', 'UsersModel')) ;
	    $view->newchurch();
	}
  /*
   * saving the church new church data and sending the notificationmail to admin from model..
   * 
   */	
	public function churchsave()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Initialise variables.
		$app	= JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$model	= $this->getModel('Profile', 'UsersModel');
		$user	= JFactory::getUser();
		// Get the user data.
		$churchdata = JRequest::getVar('jform', array(), 'post', 'array');
		$churchfiledata = $jinput->files->get('jform');
		$reslut = $model->savenewchurch($churchdata, $churchfiledata);
		// Redirect to the success screen.
		$this->setMessage(JText::_('COM_USERS_CHURCH_SAVE_SUCCESS'));
		$this->setRedirect(JRoute::_('index.php?option=com_users&task=profile.churchsuccess&tmpl=component', false));
	}
/*
 * success task of the new church adding..
 */	
	public function churchsuccess()
	{
		$view = $this->getView('profile', 'html');
		$model = $this->getModel('Profile', 'UsersModel'); 
		$view->setLayout('newchurchsuccess');
    	$view->setModel($this->getModel('Profile', 'UsersModel')) ;
	    $view->newchurchsuccess();
		
	}
}
