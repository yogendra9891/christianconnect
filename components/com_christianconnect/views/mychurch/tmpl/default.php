<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$config=JFactory::getConfig(); 
?>
<form class="listchurch" action="<?php echo JRoute::_('index.php?option=com_christianconnect&view=mychurch'); ?>" method="post" name="adminForm">
<table height="100%" width="100%" align="center">
<?php foreach($this->items as $item){ ?>
<tr><td>
<div class="listmychurch">
<table height="100%" width="100%">
<tr><td>
<?php if($item->logo==''){ ?>
<?php 
$src="//maps.googleapis.com/maps/api/streetview?size=120x150&location=";
$src.=$item->lat.','.$item->lng;
$src.="&fov=90&heading=235&pitch=10&sensor=false"; 
?>
<img height="150px" width="150" src="<?php echo $src;?>">
<?php }else{?>
	<img src="<?php echo JURI::base(). $config->getValue('config.church_imagepath_thumbs').$item->logo;?>" width="120px" height="150px" alt="<?php echo $item->cname;?>"/>
<?php }?>

<h3 class="cname"><?php echo $item->cname;?></h3>
<span class="fullchurch">
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$item->id); ?>">Full Detail</a>
</span>
</td></tr>
</table>
</div>
</td></tr>
<?php }?>
<tr><td>
<?php /*  echo $this->pagination->getListFooter();*/ ?>
<?php echo $this->pagination->getPagesLinks(); ?>
</td></tr>
</table>

</form>