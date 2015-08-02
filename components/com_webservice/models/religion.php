<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
define('LOGFILEPATH', getcwd().'/errorfile.txt'); 
/**
 * Webservice Component Model for a Webservice record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelReligion extends JModelItem
{
	//    const LOGFILEPATH= '/opt/lampp/htdocs/christianconnect/errorfile.txt';/*log file path*/
	
	/*
	 *function for getting the published religions details 
	 */
	    public function getReligions($data){
		 	$db=JFactory::getDBO();
		 	$resultdata = new stdClass();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id, a.name');
		 	$query->from('#__userreligion AS a');
		 	$query->where("a.published= 1");
		 	if(!empty($data['keywords']) && trim($data['keywords'])!==''){
		 	$search = $db->Quote('%'.$db->escape(strtolower($data['keywords']), true).'%');
		 	$query->where('a.name LIKE '.$search);}
		 	$db->setQuery($query); 
		 	$resultdata->religions = $db->loadObjectList();
		 	$resultdata->totalCount = count($resultdata->religions);
		 	return $resultdata;
	    }
	/*
	 *function for getting the published church category..
	 */
	    public function getchurchCategory($data){
		 	$db=JFactory::getDBO();
		 	$resultdata = new stdClass();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$query->select('a.id, a.title');
		 	$query->from('#__church_category AS a');
		 	$query->where("a.published= 1");
		 	$db->setQuery($query); 
		 	$resultdata->churchcategory = $db->loadObjectList();
		 	$resultdata->totalCount = count($resultdata->churchcategory);
		 	return $resultdata;
	    }
	    
	    /*function to response data
		 * @params errNo,msgString,result if any other wise set 0
		 * @return json string
		 */
   
	  private function responseData($errNo,$msgString,$result){
	        
	   		 	$this->response['resultObject']=$result;    
	  			$this->response['message']=$msgString;
	  			$this->response['statusCode']=$errNo;
    	        return json_encode($this->response); 
	    }
		
}
	    
