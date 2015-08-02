<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');

/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelEmailAdmin extends JModelItem
{
			/*function to send email to admin
			 * @param session id
			 * @return 
			 */
		 function emailAdmin()
		 {
		 	   $userId=$this->getState('emailadmin.userId');   
		 	   $user = JFactory::getUser($userId);
		 	   
               $mail =& JFactory::getMailer();
               $app                = JFactory::getApplication();                  
               $mailfrom        = $app->getCfg('mailfrom');
               $fromname        = $app->getCfg('fromname');
               $mail->setSubject(JText::_('COM_WEBSERVICE_EMAIL_CHANGED'));
               $text = $user->name.' '. JText::_('COM_WEBSERVICE_EMAIL_ADMIN_MAIL_MSG') ; 
               $mail->setBody($text);
               $mail->IsHTML(true);
               $joomla_config = new JConfig();
               $mail->addRecipient($mailfrom);
               $mail->setSender($user->email, $user->name);
               return $mail->Send(); 
 
		 }
		 	
		 
		 
}
