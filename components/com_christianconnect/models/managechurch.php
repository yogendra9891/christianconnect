  <?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');
// add table as table is lying in com_users not accessable by getTable
require_once('components/com_users/tables/christianusers.php');
 
/**
 * UpdHelloWorld Model
 */
class ChristianconnectModelManageChurch extends JModelForm
{
        /**
         * @var object item
         */
        protected $data;
        
	
 
        /**
         * Get the data for a new qualification
         */
        public function getForm($data = array(), $loadData = true)
        {
 
        $app = JFactory::getApplication('site');
 
        // Get the form.
                $form = $this->loadForm('com_christianconnect.managechurch', 'churchprofile', array('control' => 'jform', 'load_data' => true));
                if (empty($form)) {
                        return false;
                }
                return $form;
 
        }

     /**
	 * Method to get the profile form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		// Initialise variables.
		$table = $this->getTable('church','ChristianconnectTable');
		$pk=JRequest::getVar('churchid');
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$data = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($data, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($data->params);
			$data->params = $registry->toArray();
		}

		return $data;	
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
		if (empty($data)) {
			$data = $this->getData();
        }

		return $data;
	}
 

	/** override function save to add church lat& lng
	 * Method to save church data edited by church owner
	 * @param form data
	 * @return boolean
	 */
	public function saveChurchProfile(){
		
		//get table object
		 $row=$this->getTable('Church','ChristianconnectTable');
		 
	
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

	   if(!empty($pfile['name']['profileimage1']) || !empty($pfile['name']['profileimage2']) || !empty($pfile['name']['logo']))
			{ 
				
				jimport('joomla.client.helper');
				JClientHelper::setCredentialsFromRequest('ftp');
				jimport('joomla.filesystem.file');
				
				$imagename=$this->getImage($data['id']);
				if(!empty($pfile['name']['profileimage1'])){
						$allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
					if (!in_array($pfile['type']['profileimage1'], $allowed)) //To check if the file are image file
						{
					   		return false;
				       		 exit;
						}
					//check for image with this id already exist if yes remove the image from folder
					if($imagename!==false)
							{
								$this->unlinkImage($imagename->profileimage1);
								
							}
					//Clean up filename to get rid of strange characters like spaces etc
				    $filename = JFile::makeSafe(time().$pfile['name']['profileimage1']);//Make the filename safe
				 	// function to upload image....
					$this->imageUpload($filename,$pfile['tmp_name']['profileimage1']);
					// function "createThumbnail" for creating the thumbnail of the uploaded image....
					$this->createThumbnail($filename);
				
					$data['profileimage1']=$filename;
				
				}
				if(!empty($pfile['name']['profileimage2'])){
					$allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
					if (!in_array($pfile['type']['profileimage2'], $allowed)) //To check if the file are image file
						{
					   		return false;
				        	exit;
						}
					//check for image with this id already exist if yes remove the image from folder
					if($imagename!==false)
							{
								$this->unlinkImage($imagename->profileimage2);
							}
					//Clean up filename to get rid of strange characters like spaces etc
					 $filename2 = JFile::makeSafe(time().$pfile['name']['profileimage2']);//Make the filename safe
				 	// function to upload image....
					$this->imageUpload($filename2,$pfile['tmp_name']['profileimage2']);
					// function "createThumbnail" for creating the thumbnail of the uploaded image....
					$this->createThumbnail($filename2);
				
					$data['profileimage2']=$filename2;
				}
			if(!empty($pfile['name']['logo'])){
				
					$allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
					if (!in_array($pfile['type']['logo'], $allowed)) //To check if the file are image file
						{
					   		return false;
				       		 exit;
						}
					//check for image with this id already exist if yes remove the image from folder
					if($imagename!==false)
							{
								$this->unlinkImage($imagename->logo);
							}
					//Clean up filename to get rid of strange characters like spaces etc
					 $logoname = JFile::makeSafe(time().$pfile['name']['logo']);//Make the filename safe
				 	// function to upload image....
					$this->imageUpload($logoname,$pfile['tmp_name']['logo']);
					// function "createThumbnail" for creating the thumbnail of the uploaded image....
					$this->createThumbnail($logoname);
					$data['logo']=$logoname;
				}
				
			}  
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
		 * function to create thumbnail
		 * @param filename
		 * @return boolean
		 */

		function createThumbnail($filename) {
			$config=JFactory::getConfig();
			$path_to_thumbs_directory = JPATH_SITE .$config->getValue('config.church_imagepath_thumbs');
			$path_to_image_directory = JPATH_SITE .$config->getValue('config.church_imagepath_original'); 
			$final_width_of_image = 150;
			if(preg_match('/[.](jpg)$/', $filename)) {
				$im = imagecreatefromjpeg($path_to_image_directory . $filename);
			} else if (preg_match('/[.](jpeg)$/', $filename)) {
				$im = imagecreatefromjpeg($path_to_image_directory . $filename);
			} else if (preg_match('/[.](gif)$/', $filename)) {
				$im = imagecreatefromgif($path_to_image_directory . $filename);
			} else if (preg_match('/[.](png)$/', $filename)) {
				$im = imagecreatefrompng($path_to_image_directory . $filename);
			}
			$ox = imagesx($im);
			$oy = imagesy($im);
			$ratio = $ox / $oy;
            $nx = $ny = min($final_width_of_image, max($ox, $oy));
               if ($ratio < 1) {
                   $nx = $ny * $ratio;
               } else {
          			$ny = $nx / $ratio;
               }
		
			$nm = imagecreatetruecolor($nx, $ny);
			imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
			if(!file_exists($path_to_thumbs_directory)) {
				if(!mkdir($path_to_thumbs_directory)) {
					return false;
				}
			}
			
			imagejpeg($nm, $path_to_thumbs_directory . $filename);
			return true;
		}
		
		
	/**
	 * Method to get longitude and latitude.
	 *
	 * @param	array		address data.
	 * @return	mixed		longitude and latitude.
	 */
		
	public function getLatLon($address) {
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
		
    
    /*function to get the image if already in database
     * @accept id of the row
     * @return image value if exits
     */
	
	function getImage($id)
	{
		if(!isset($id))
			{
			$id=0;
			}	
			 $row=$this->getTable('Church','ChristianconnectTable');
			if(!$row->load($id))
			 	{
			 		return false;
			 	}
				else{
					return $row;
				}
	}
	
	
	/* 
	 * void Method to unlink image if already exist
	 * @param imagename string
	 * 
	 */
	protected function unlinkImage($imagename)
		{
			$config=JFactory::getConfig();
			$unlinkpath = JPATH_ROOT.$config->getValue('config.church_imagepath_original').$imagename;
			if(file_exists($unlinkpath)){
				unlink($unlinkpath);
			}
			$unlinkpath = JPATH_ROOT.$config->getValue('config.church_imagepath_thumbs').$imagename;
				if(file_exists($unlinkpath)){
					unlink($unlinkpath);
				}
		}
						
	/* 
	 * Method to imageUpload image 
	 * @param filename and tempname string
	 * @return true 
	 * 
	 */	
		
	protected function imageUpload($filename,$tempname)
	{
		
				$config=JFactory::getConfig();
	 			$type=explode(".",$filename);
				/*getting image type other than application/octat data*/
				$file['type']="image/".$type[1];
				//Set up the source and destination of the file
				 $src = $tempname;
				 $dest = JPATH_ROOT.$config->getValue('config.church_imagepath_original').$filename;//specific path of the file
				if (file_exists($dest)) {
					  return false;
				} 
				if(!JFile::upload($src, $dest))
				{
				  return false;
				}
	}	
}