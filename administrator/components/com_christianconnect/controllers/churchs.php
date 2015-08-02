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

/**
 * Christianconnects list controller class.
 */
class ChristianconnectControllerChurchs extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'church', $prefix = 'ChristianconnectModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
/**
	 * publised/unpublish records of deals
	 * @access public
	 * @param value from form in post
	 * @return id if task 'apply'
	 * 
	 */
	function publish()
	{
			$values	= array('publish' => 1, 'unpublish' => 0);
			$task	= JRequest::getCmd('task');
			$value	= JArrayHelper::getValue($values, $task, 0, 'int');
			//get ids of records to be publish/upublish
			$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
			$model = $this->getModel('churchs');
		    if(!$model->updatelatlong($cids,$value)) {
		        $msg = JText::_( 'Error: One or More Deals Could not be '.$task );
		    } else {
		        $msg = JText::_( 'Deal(s) '.$task );
		    }
		$link = 'index.php?option=com_christianconnect&view=churchs'; 
		$this->setRedirect($link, $msg);
	}
	
}	