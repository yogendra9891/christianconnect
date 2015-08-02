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
class WebserviceModelChristianLife extends JModelItem
{
			/*function to get christian life
			 * @param session id
			 * @return 
			 */
		 function getMyChristianLife()
		 {
		 	   $userId=$this->getState('christianlife.userId');   
		 	   $data="No christian life related data";
               return $data;
 
		 }
		 	
		 
		 
}
