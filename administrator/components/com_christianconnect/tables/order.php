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
 * christianconnect Table class
 */
class ChristianconnectTableOrder extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__subscription_order', 'id', $db);
    }

 
    
    

}
