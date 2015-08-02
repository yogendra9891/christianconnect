<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
$config=JFactory::getConfig(); 
?>
<script type="text/javascript">
	function setTask(task)
	{
		if (task == 'managechurch.cancel' || document.formvalidator.isValid(document.id('updatechurch'))) {
			Joomla.submitform(task, document.getElementById('updatechurch'));
		}
	}
</script>
    <h2>Edit church profile</h2>
    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="updatechurch" name="updatechurch" enctype='multipart/form-data'>
                <fieldset>
                	<dl>
                <dd></dd>
                	<dt><?php echo $this->form->getLabel('cname'); ?></dt>
					<dd><?php echo $this->form->getInput('cname'); ?></dd>
				<dd></dd>
				   
				   <dt><?php echo $this->form->getLabel('description'); ?></dt>
					<dd><?php echo $this->form->getInput('description'); ?></dd>
				<dd></dd>
				
				<dt><?php echo $this->form->getLabel('address1'); ?></dt>
				<dd><?php echo $this->form->getInput('address1'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('address2'); ?></dt>
				<dd><?php echo $this->form->getInput('address2'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('city'); ?></dt>
				<dd><?php echo $this->form->getInput('city'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('state'); ?></dt>
				<dd><?php echo $this->form->getInput('state'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('country'); ?></dt>
				<dd><?php echo $this->form->getInput('country'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('postcode'); ?></dt>
				<dd><?php echo $this->form->getInput('postcode'); ?></dd>

				<dd></dd>
				<dt><?php echo $this->form->getLabel('phone'); ?></dt>
				<dd><?php echo $this->form->getInput('phone'); ?></dd>

			    <dd></dd>
			    <dt><?php echo $this->form->getLabel('siteurl'); ?></dt>
				<dd><?php echo $this->form->getInput('siteurl'); ?></dd>

				<dd></dd>
				<dt><?php echo $this->form->getLabel('profileimage1'); ?></dt>
				<dd><?php echo $this->form->getInput('profileimage1'); ?></dd>
				<dd>
				<?php if($this->item->profileimage1!=''){?>
				
				 
				<img src="<?php echo JURI::base(). $config->getValue('config.church_imagepath_thumbs').$this->item->profileimage1;?>" width="60" height="60" alt=""/>
				<?php  }else{
					echo JText::_('No profile image in database');
				}?>
				</dd>
				<dd></dd>
				<dt><?php echo $this->form->getLabel('profileimage2'); ?></dt>
				<dd><?php echo $this->form->getInput('profileimage2'); ?></dd>
				<dd>
				<?php if($this->item->profileimage2!=''){?>
				<img src="<?php echo JURI::base(). $config->getValue('config.church_imagepath_thumbs').$this->item->profileimage2;?>" width="60" height="60" alt=""/>
				<?php  }else{
					echo JText::_('No profile image in database');
				}?>
				</dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('logo'); ?></dt>
				<dd><?php echo $this->form->getInput('logo'); ?></dd>
				<dd>
				<?php if($this->item->logo!=''){?>
				<img src="<?php echo JURI::base(). $config->getValue('config.church_imagepath_thumbs').$this->item->logo;?>" width="60" height="60" alt=""/>
				<?php  }else{
					echo JText::_('No profile image in database');
				}?>
				</dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('category'); ?></dt>
				<dd><?php echo $this->form->getInput('category'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('id'); ?></dt>
				<dd><?php echo $this->form->getInput('id'); ?></dd>

			    <dd></dd>
				
                	<dd></dd>
                <dt><button type="submit"  onClick="setTask('managechurch.saveChurchProfile');"><?php echo JText::_('Save Now!'); ?></button></dt>
                 <dt><button type="submit" onClick="setTask('managechurch.cancel');"><?php echo JText::_('Cancel'); ?></button></dt>
                 </dl>
        </fieldset>        
                	<input type="hidden" name="option" value="com_christianconnect" />
                	<input type="hidden" name="task" id="task" value="" />
               
                                        <?php echo JHtml::_('form.token'); ?>
                
    </form>
    <div class="clr"></div>