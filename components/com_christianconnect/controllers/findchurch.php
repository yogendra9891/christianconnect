<?php
 
// No direct access.
defined('_JEXEC') or die;
 
// import Joomla controller library
jimport('joomla.application.component.controller');
JLoader::import( 'com_christianconnect.controller', JPATH_SITE.DS.'components' );
 
class ChristianconnectControllerFindchurch extends ChristianconnectController
{
/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_CHRITIANCONNECT_FINDCHURCH';

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
	public function getModel($name = 'findchurch', $prefix = 'ChristianconnectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
 }
?>
