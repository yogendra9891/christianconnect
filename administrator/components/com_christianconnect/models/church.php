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

jimport('joomla.application.component.modeladmin');

/**
 * Christianconnect model.
 */
class ChristianconnectModelChurch extends JModelAdmin
{


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Church', $prefix = 'ChristianconnectTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_christianconnect.church', 'church', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_christianconnect.edit.church.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}

	


	/** override function save to add church lat& lng
	 * Method to save church data
	 * @param form data
	 * @return boolean
	 */
	public function save(){
		//get table object
		 $row=$this->getTable();
		 $config=JFactory::getConfig();
	
		 //get formdata
		 $data = JRequest::getVar('jform', array(), 'post', 'array');
		 $pfile = JRequest::getVar('jform', array(), 'files', 'array');
		 
         //get church location from church adrress
        $address = $data['address1']." ".$data['address2']." ".$data['postcode']." ".$data['city']." ".$data['state']." ".$data['country'];
	    $locationdata = $this->getLatLon($address);
	    if($locationdata!=false);
	    {
	    $data['lat']= $locationdata->lat;
	    $data['lng']= $locationdata->lng;
	    }
	    $data['subscriptionStatus']=0;		
	 
			
	    // save the church after complete image validation    
		if($row->save($data))
		{
			$this->setState('church.id', $row->id);	
			return true;
		}else{
		   $this->setError($row->getError());
			return false;
		}
		
	}
	
      
	/**
	 * Method to get longitude and latitude.
	 *
	 * @param	array		address data.
	 * @return	mixed		longitude and latitude.
	 */
		
	public function getLatLon($address) {
		//$address=mysql_real_escape_string("PO Box 8041,Urlich,Hamilton,Waikato- thames valley,NZ,3245");
		//$address="PO Box 44,Waihi Beach,Waikato - Thames Valley,3642,NZ"; 
		
		$request_url = "http://maps.googleapis.com/maps/api/geocode/json?&sensor=false&address={?}"; 
        $address = trim($address);
        $address = urlencode($address);  
	        $request_url = str_replace('{?}', $address, $request_url); 
		  	$json = file_get_contents($request_url, true);  //getting the file content 
		  	$decode = json_decode(utf8_encode($json), false); 
			$status = $decode->status;
         if($status=='OK'){
	        return $decode->results[0]->geometry->location;
	     }else{
	     return false;
	     }
    }
		
    
  
     
    

}