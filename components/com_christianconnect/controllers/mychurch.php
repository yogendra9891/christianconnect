<?php
 
// No direct access.
defined('_JEXEC') or die;
 
// import Joomla controller library
jimport('joomla.application.component.controller');
JLoader::import( 'com_christianconnect.controller', JPATH_SITE.DS.'components' );
 
class ChristianconnectControllerMychurch extends ChristianconnectController
{

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'mychurch', $prefix = 'ChristianconnectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	
	/* function to get details of church
	 * @params churchid
	 * @return object of church
	 */
	
	public function getFullDetail()
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}	
		$churchid=JRequest::getVar('churchid'); 
		$model=$this->getModel();
		$model->setState('mychurch.churchid',$churchid);
		$navarray=$model->getNavigationArray();
		$churchdetail=$model->getFullDeatil();
		$view=$this->getView('Mychurch','html');
		$view->assign('churchdetails',$churchdetail);
		$view->assign('navarray',$navarray);
		$view->setLayout('churchdetail');
		$view->display();
	}
 }
?>
