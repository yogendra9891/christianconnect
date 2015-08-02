<?php
/**
 * @version     1.0.0
 * @package     com_myfriend
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      yogendra <yogendra.singh@daffodilsw.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Myfriends controller class.
 */
class MyfriendControllerMyfriends extends JControllerForm
{

    function __construct() {
        $this->view_list = 'myfriendss';
        parent::__construct();
    }

}