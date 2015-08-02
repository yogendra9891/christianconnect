<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */
 
// No direct access
defined('_JEXEC') or die;

/**
 * Christianconnect Component Controller
 */
class ChristianconnectController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $safeurlparams=null);

		return $this;
		
	}
}


?>