<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_Websevice
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$controller	= JControllerLegacy::getInstance('webservice');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
