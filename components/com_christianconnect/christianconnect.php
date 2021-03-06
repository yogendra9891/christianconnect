<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access to this file
defined('_JEXEC') or die;

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Christianconnect
$controller	= JController::getInstance('Christianconnect');
// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));
// Redirect if set by the controller
$controller->redirect();

