<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
// import the Joomla modellist library
jimport('joomla.application.component.modelitem');

/**
 * Weblinks Component Model for a Weblink record
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since		1.5
 */
class WebserviceModelFindchurch extends JModelItem
{
	
			/*function to get churches
			 * @param mixed data
			 * @optional param keyword and lat long
			 * @return church object if successful
			 */
		 function getChurches()
		 {
		 	$querylocation='';
		 	$resultdata=new stdClass();
		 	$data=$this->getState('findchurch.churchsearch');
		 	$pageIndex=$data['pageIndex'];
		 	$pageSize=$data['pageSize'];
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	if(isset($data['userLocation']['lat']) && trim($data['userLocation']['lat'])!=='' && isset($data['userLocation']['long']) && trim($data['userLocation']['long'])!=='')
		 	{
		 		$querylocation=",( 6371 * acos( cos( radians(".$data['userLocation']['lat'].") ) * cos( radians(a.lat ) ) * cos( radians( a.lng ) - radians(".$data['userLocation']['long'].") ) + sin( radians(".$data['userLocation']['lat'].") ) * sin( radians( a.lat ) ) ) ) as distance";
		 	}	
		 	$query->select('a.id,a.cname'.$querylocation);
		 	$query->from('#__church AS a');
		 	$query->where('a.published=1');
		 	if(isset($data['keywords']) && trim($data['keywords'])!=='')
		 	{
		 		$search = $db->Quote('%'.$db->escape(strtolower($data['keywords']), true).'%');
		 		$query->where('(a.cname LIKE '.$search.')');
		 	}		
		 	
		 	$resultdata->churches=$this->_getList($query,$pageIndex,$pageSize);
		 	$resultdata->totalCount=$this->_getListCount($query);
		 	
		 	return $resultdata;
		   
		 }
		 
			/*function to search churches
			 * @param mixed data
			 * @optional param churchtype and lat long
			 * @return church object if successful
			 */
		 function searchChurch()
		 {
		 	$churchObject=new stdClass();
		 	$i=0;
		 	$querylocation='';
		 	$data=$this->getState('findchurch.churchsearch');
		 	$pageIndex=$data['pageIndex'];
		 	$pageSize=$data['pageSize'];
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	if(isset($data['userLocation']['lat']) && trim($data['userLocation']['lat'])!=='' && isset($data['userLocation']['long']) && trim($data['userLocation']['long'])!=='')
		 	{
		 		$querylocation=",( 6371 * acos( cos( radians(".$data['userLocation']['lat'].") ) * cos( radians(a.lat ) ) * cos( radians( a.lng ) - radians(".$data['userLocation']['long'].") ) + sin( radians(".$data['userLocation']['lat'].") ) * sin( radians( a.lat ) ) ) ) as distance";
		 	}	
		 	$query->select('a.id,a.cname,a.lat,a.lng'.$querylocation);
		 	$query->from('#__church AS a');
		 	$query->join('LEFT','#__church_category AS b ON b.id=a.category');
		 	$query->where('a.published=1');
		 	if(isset($data['churchType']) && trim($data['churchType'])!=='')
		 	{
		 		$query->where('a.category = '.$db->Quote($data['churchType']));
		 	}		
		 	$results=$this->_getList($query,$pageIndex,$pageSize);
		 	
		 	//formating data as required 
		 	foreach($results as $result)
		 	{
		 		$churchObject->churches->$i=new stdClass();
		 		$churchObject->churches->$i->churchId=$result->id;
		 		$churchObject->churches->$i->churchName=$result->cname;
		 		$churchObject->churches->$i->churchLocation->lat=$result->lat;
		 		$churchObject->churches->$i->churchLocation->long=$result->lng;
		 		$churchObject->churches->$i->distance=$result->distance;
		 		//increment in counter
		 		$i++;
		 	}
		 	$churchObject->totalCount=$this->_getListCount($query);
		 	
		 	return $churchObject;
		   
		 }
	
}
