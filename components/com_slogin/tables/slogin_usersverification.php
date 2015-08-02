<?php
/**
 * Social Login
 *
 * @version 	1.0
 * @author		SmokerMan, Arkadiy, Joomline
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class SloginTableSlogin_usersverification extends JTable
{
    function __construct( &$_db )
    {
        parent::__construct('#__slogin_usersverification', 'id', $_db );
    }
}
?>