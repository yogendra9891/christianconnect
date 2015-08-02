<?php
 
// No direct access.
defined('_JEXEC') or die;
 
// import Joomla controller library
jimport('joomla.application.component.controller');
JLoader::import( 'com_christianconnect.controller', JPATH_SITE.DS.'components' );
 
class ChristianconnectControllerChurchLeaders extends ChristianconnectController
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
	public function getModel($name = 'ChurchLeaders', $prefix = 'ChristianconnectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/* function to add leader of church
	 * @params mixed data from list
	 * 
	 */
	
	function addLeaders()
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
		$model=$this->getModel();
		$churchid = JRequest::getVar('churchid');
		$model->setState('churchleaders.churchid',$churchid);
		if($model->addLeaders()){
			$msg="Add Leader Successfull";
		}else{
			$msg="Add Leader Failed";
		}
		$this->setRedirect('index.php?option=com_christianconnect&view=churchleaders&churchid='.$churchid,$msg);
	}
	
	/* function to add leader of church
	 * @params mixed data from list
	 * 
	 */
	
	function removeLeaders()
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
				}
		$model=$this->getModel();
		$churchid = JRequest::getVar('churchid');
		$leaderid = JRequest::getVar('leaderid');
		$model->setState('churchleaders.leaderid',$leaderid);
		if($model->removeLeaders()){
			$msg="Remove Leader Successfull";
		}else{
			$msg="Remove Leader Failed";
		}
		$this->setRedirect('index.php?option=com_christianconnect&view=churchleaders&churchid='.$churchid);
	}
	
	/* function to get list of leader of church
	 * @params mixed data from list
	 * @return church leader object
	 */
	
	function getChurchLeader()
	{
		//check for user must logged in
		if (JFactory::getUser()->id == 0){
				$errormessage = JText::_('LOGIN_FIRST');
				$app =& JFactory::getApplication();
				$app->redirect('index.php?option=com_users&view=login', $errormessage,'message');
		 }
		$churchid=JRequest::getVar('churchid'); 
		$model=$this->getModel();
		$model->setState('churchleaders.churchid',$churchid);
		$leaderpagination=$model->getPagination();
		$churchleaders=$model->getChurchLeader();
		$view=$this->getView('churchleaders','html');
		$view->assign('churchleaders',$churchleaders);
		$view->assign('leaderpagination',$leaderpagination);
		$view->setLayout('leaderslist');
		$view->display();
	}
	
	
 }
?>
