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
class WebserviceModelMychurch extends JModelItem
{
	
			/*function to get my churches
			 * @param userid
			 * @return church object if successful
			 */
		 function getMyChurches()
		 {
		 	$querylocation='';
			$churchObject=new stdClass();
		 	$churchObject->churchobject=array();
		 	//get user church
        	$userchurch=$this->getUserChurch();
        	
        	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	 //if no church related to user exist redirect it to profile
                if(trim($userchurch['localchurch'])=='' && trim($userchurch['otherchurch']==''))
                {
	              return $churchObject;
                }
                //creating some condition
                if($userchurch['localchurch']!='')
        		{
        			$where[]="a.id IN (".$userchurch['localchurch'].") AND a.published=1";
        		}
        		if($userchurch['otherchurch']!='')
        		{
        			$where[]=" a.id IN(".$userchurch['otherchurch'].") AND a.published=1";
        		}
        		if(isset($userchurch['lat']) && trim($userchurch['lat'])!=='' && isset($userchurch['lng']) && trim($userchurch['lng'])!=='')
		 		{
		 		$querylocation="( 6371 * acos( cos( radians(".$userchurch['lat'].") ) * cos( radians(a.lat ) ) * cos( radians( a.lng ) - radians(".$userchurch['lng'].") ) + sin( radians(".$userchurch['lat'].") ) * sin( radians( a.lat ) ) ) ) as distance";
		 		}	
        		
        		// Select some fields
                $query->select("a.id,a.cname,CONCAT_WS(',',a.address1,a.address2,a.city,a.state,b.country,a.postcode) as address,a.phone,a.profileimage1,a.profileimage2,a.logo,a.description,a.siteurl,a.subscription_status,a.lat,a.lng");
				if($querylocation !=''){
                $query->select($querylocation);
				}
                // From the tablename
                $query->from('#__church AS a ');
                $query->join('LEFT','#__countries AS b ON a.country=b.ccode');
                // Where condition
               
                $query->Where($where,'OR');
                
              	$i=0;
		 		$results=$this->_getList($query);
		 		
		 	//formating data as required 
		 	foreach($results as $result)
		 	{
		 		$churchObject->churchobject[$i]= array();
		 		$churchObject->churchobject[$i]['churchId']=$result->id;
		 		$churchObject->churchobject[$i]['churchName']=$result->cname;
		 		$churchObject->churchobject[$i]['address']=$result->address;
		 		$churchObject->churchobject[$i]['phone']=$result->phone;
		 		$churchObject->churchobject[$i]['website']=$result->siteurl;
		 		$profileimage1path='';
		 		$profileimage2path='';
		 		$logopath='';
		 		if($result->profileimage1 !='')
		 		{
		 		$profileimage1path=JURI::base().$result->profileimage1;
		 		}
		 		if($result->profileimage2 !='')
		 		{
		 		$profileimage2path=JURI::base().$result->profileimage2;
		 		}
		 		if($result->logo !='')
		 		{
		 		$logopath=JURI::base().$result->logo;
		 		}
		 		$churchObject->churchobject[$i]['churchPics']=new stdClass();
		 		$churchObject->churchobject[$i]['churchPics']->imageUrl[]=$profileimage1path;
		 		$churchObject->churchobject[$i]['churchPics']->imageUrl[]=$profileimage2path;
		 		$churchObject->churchobject[$i]['churchPics']->imageUrl[]=$logopath;
		 		$churchObject->churchobject[$i]['description']=$result->description;
		 		$churchObject->churchobject[$i]['isSubscribed']=$result->subscription_status;
		 		$churchObject->churchobject[$i]['churchLocation']= new StdClass();
		 		$churchObject->churchobject[$i]['churchLocation']->lat=$result->lat;
		 		$churchObject->churchobject[$i]['churchLocation']->long=$result->lng;
		 		if(isset($result->distance))
		 		{
		 		$churchObject->churchobject[$i]['distance']=$result->distance;
		 		}
		 		//increment in counter
		 		$i++;
		 	}
		 	
		 	return $churchObject;
		   
		 }
		 
		 
		 /* function deleteMyChurch
		  * @param churchid to be removed
		  * @return true
		  * */
		 
		 function deleteMyChurch()
		 {
			//creating an array used by array_diff function
		 	$churchId['churchId']=$this->getState('mychurch.churchId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	$churchIds=$this->getUserChurch();
		 	if(count($churchIds)!=0){
			 	//localchurchids are coming in comma separated value explode them
			 	$localchurch = explode(",", $churchIds['localchurch']);
			 	//removing the input id match from localchurch
			 	$localchurch= array_diff($localchurch, $churchId);
			 	//recreate localchurch array to store in table
			 	$localchurch = implode(",", $localchurch);
	
			 	//otherchurchids are coming in comma separated value explode them
			 	$otherchurch = explode(",", $churchIds['otherchurch']);
			 	//removing the input id match from  otherchurch
			 	$otherchurch= array_diff($otherchurch, $churchId);
			 	//recreate otherchurch array to store in table
			 	$otherchurch = implode(",", $otherchurch);
			 	
			 	//creating update condition
			 	$setquery[]='a.localchurch='.$db->quote($localchurch);
			 	$setquery[]='a.otherchurch='.$db->quote($otherchurch);
			 	
			 	//update table userchurch with new localchurch and otherchurch value
			 	$query->update('#__userchurch AS a');
			 	$query->set($setquery,',');
			 	$query->where('a.id='.$db->quote($churchIds['id']));
			 	$db->setQuery($query);
			 	if($db->execute())
			 	{
			 	 return true;
			 	}else{
			 	return false;	
			 	}
		 	}
		 
		 }
		 
		 
		 
		/*function to get church by id
		  * @param churchid
		  * @return church object 
		  */
	
		 function getChurchDetails()
		 {
			$i=0;
		 	$churchId=$this->getState('mychurch.churchId');
			$churchObject=new stdClass();
		 	$churchObject->churchobject=array();
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select("a.id,a.cname,CONCAT_WS(',',a.address1,a.address2,a.city,a.state,b.country,a.postcode) as address,a.phone,a.profileimage1,a.profileimage2,a.logo,a.description,a.siteurl,a.subscription_status,a.lat,a.lng");
		 	$query->from('#__church AS a');
		 	$query->join('LEFT','#__countries AS b ON a.country=b.ccode');
		 	$query->where('a.id='.$db->quote($churchId));
		 	$query->where('a.published=1');
		 	$db->setQuery($query);
			$results=$this->_getList($query);
		 //formating data as required 
		 	foreach($results as $result)
		 	{
		 		$churchObject->churchobject[$i]=new stdClass();
		 		$churchObject->churchobject[$i]->churchId=$result->id;
		 		$churchObject->churchobject[$i]->churchName=$result->cname;
		 		$churchObject->churchobject[$i]->address=$result->address;
		 		$churchObject->churchobject[$i]->phone=$result->phone;
		 		$churchObject->churchobject[$i]->website=$result->siteurl;
		 		$profileimage1path='';
		 		$profileimage2path='';
		 		$logo='';
		 		if($result->profileimage1 !='')
		 		{
		 		$profileimage1path=JURI::base().$result->profileimage1;
		 		}
		 		if($result->profileimage2 !='')
		 		{
		 		$profileimage2path=JURI::base().$result->profileimage2;
		 		}
		 		if($result->logo !='')
		 		{
		 		$logopath=JURI::base().$result->logo;
		 		}
		 		$churchObject->churchobject[$i]->churchPics->imageurl[]=$profileimage1path;
		 		$churchObject->churchobject[$i]->churchPics->imageurl[]=$profileimage2path;
		 		$churchObject->churchobject[$i]->churchPics->imageurl[]=$logopath;
		 		$churchObject->churchobject[$i]->description=$result->description;
		 		$churchObject->churchobject[$i]->isSubscribed=$result->subscription_status;
		 		$churchObject->churchobject[$i]->churchLocation->lat=$result->lat;
		 		$churchObject->churchobject[$i]->churchLocation->long=$result->lng;
		 		//increment in counter
		 		$i++;
		 	}
		 	
		 	return $churchObject;
		 }
		 
		
		 
		 /*function to get church by userid
		  * @param userid
		  * @return churchuser object 
		  */
	
		/* function getChurchIdByUserId()
		 {
		 	$userId=$this->getState('mychurch.userId');
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	
		 	$query->select('a.*');
		 	$query->from('#__userchurch AS a');
		 	$query->where('a.userid='.$db->quote($userId));
		 	$db->setQuery($query);
			$churchIds = $db->loadAssoc();
			if(count($churchIds)!==0)
				{
					return $churchIds;
				}else{
					return false;
				}
		 	
		 }*/
		 
		/*function to getuser church
         * @params user id from user object
         * @return object of userchurch
         */
       public function getUserChurch()
        {
        	$userId=$this->getState('mychurch.userId');
        	$db=JFactory::getDBO();
        	$query = $db->getQuery(true);
        	$query->clear();
            // Select some fields
            $query->select('a.*,b.lat,b.lng');
            // From the tablename
            $query->from('#__userchurch AS a ');
            // left join to get user information 
            $query->join('LEFT','#__christianusers AS b ON b.userid=a.userid');
            // left join to get user information 
            $query->join('LEFT','#__users AS c ON c.id=b.userid');
            // Where condition
            $query->Where('a.userid =' .(int)$userId);
            
            $db->setQuery($query);
            return $db->loadAssoc();
        }
        
		/*function to get church by id
		  * @param churchid
		  * @return church object 
		  */
	
		 function getChurchLeaders()
		 {
			$churchId=$this->getState('mychurch.churchId');
		 	//creating some object
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	//ceating query
		 	$query->select("b.id, CONCAT_WS(' ',a.fname,a.lname) as name,a.profileimage");
		 	$query->from('#__christianusers AS a');
		 	$query->join('LEFT','#__users AS b ON b.id=a.userid');
		 	$query->join('INNER','#__church_leaders AS c ON c.leaderid=a.userid');
		 	$query->where('c.churchid='.$db->quote($churchId));
                        $query->where('b.block=0');
		 	$db->setQuery($query);
		 	
			$results=$this->_getList($query);
		 	
		 	return $results;
		 }
		 
		 
		/*function to get ChurchFriends
		  * @param churchid,pageindex,pagesize
		  * @return church object 
		  */
	
		 function getChurchFriends()
		 {
			$i=0;
			$friendObject=new stdClass();
		 	$data=$this->getState('mychurch.churchfrienddata');
			$churchId=$data['churchId'];
			$userId=$this->getState('mychurch.userId');
		 	$pageIndex=$data['pageIndex'];
		 	$pageSize=$data['pageSize'];
		 	//creating some object
		 	$db=JFactory::getDBO();
		 	$query=$db->getquery(true);
		 	$query->clear();
		 	//ceating query
		 	$query->select("b.id, CONCAT_WS(' ',a.fname,a.lname) as name,a.profileimage");
		 	$query->from('#__christianusers AS a');
		 	$query->join('LEFT','#__users AS b ON b.id=a.userid');
		 	$query->join('INNER','#__userchurch AS d ON d.userid=a.userid');
		 	$query->where('(b.id IN(SELECT DISTINCT c.connectfrom FROM #__christianconnection AS c WHERE c.connectto='.$userId.' AND c.status=1) OR b.id IN(SELECT DISTINCT cc.connectto FROM #__christianconnection AS cc WHERE cc.connectfrom='.$userId.' AND cc.status=1))');
		 	$query->where('(FIND_IN_SET('.$db->quote($churchId).',d.localchurch) OR FIND_IN_SET('.$db->quote($churchId).',d.otherchurch) ) AND a.userid !='.$userId);
		 	$query->where('b.block=0');
		 	$results=$this->_getList($query,$pageIndex,$pageSize);
		 	
		 		//formating data as required 
		 	foreach($results as $result)
		 	{
		 		$friendObject->friends[$i]=new stdClass();
		 		$friendObject->friends[$i]->id=$result->id;
		 		$friendObject->friends[$i]->name=$result->name;
		 		$friendObject->friends[$i]->image=$result->profileimage;
		 		//increment in counter
		 		$i++;
		 	}
		 	
		 	$friendObject->totalCount=$this->_getListCount($query);
		 	return $friendObject;
		 	
		 }
}
        
?>
