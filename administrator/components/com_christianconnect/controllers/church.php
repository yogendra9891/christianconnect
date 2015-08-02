<?php
/**
 * @version     1.0.0
 * @package     com_christianconnect
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      abhishek <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');

/**
 * Christianconnects list controller class.
 */
class ChristianconnectControllerChurch extends JControllerForm
{
/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
    function __construct() {
        $this->view_list = 'churchs';
        parent::__construct();
    }
	
    public function save($key = null, $urlVar = null)
	{
	    $data = JRequest::getVar('jform', array(), 'post', 'array');
		return parent::save();
	}
}