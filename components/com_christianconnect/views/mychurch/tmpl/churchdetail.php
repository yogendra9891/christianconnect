<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
//echo "<pre>";var_dump($this->detailpagination); die;
//echo "<pre>";var_dump($this->churchdetails); die;
?>
<?php 
$count=count($this->navarray);
$i=0;
$current=array_search($this->churchdetails->id, $this->navarray);
$last_key = key( array_slice( $this->navarray, -1, 1, TRUE ));
if($current !=0)
{
$prev=$current-1;
}
if($current!=$last_key)
{
$next=$current+1;
}
?>
<div class="fullchurch">
<div><h3 class="churchhead"><?php echo $this->churchdetails->cname; ?></h3></div>
<div class="churchimage">
<?php if($this->churchdetails->profileimage1==''&& $this->churchdetails->subscription_status==0){?>
<div class="churchgoogleimg">
<?php 
$src="//maps.googleapis.com/maps/api/streetview?size=292x460&location=";
$src.=$this->churchdetails->lat.','.$this->churchdetails->lng;
$src.="&fov=90&heading=235&pitch=10&sensor=false"; 
?>
<img height="292px" width="460px" src="<?php echo $src;?>">
</div>
<?php }else{?>
<div class="churchprofileimg"><img height="292px" width="460px" src="<?php JURI::base(); ?>images/churchprofileimage/original/<?php echo $this->churchdetails->profileimage1; ?>" alt="<?php echo $this->churchdetails->cname; ?>"/></div>
<div class="churchshortimg"><img src="<?php JURI::base(); ?>images/churchprofileimage/thumbs/<?php echo $this->churchdetails->profileimage2; ?>" alt="<?php echo $this->churchdetails->cname; ?>"/></div>
<div  class="churchlog"><img src="<?php JURI::base(); ?>images/churchprofileimage/thumbs/<?php echo $this->churchdetails->logo; ?>" alt="<?php echo $this->churchdetails->cname; ?>"/></div>
<?php }?>
</div>
<div class="churchaddress  mar-b15">
<span class="address">address: <?php echo $this->churchdetails->address1; ?></span>
<span class="phone">Phone: <?php echo $this->churchdetails->phone; ?></span>
<span class="Website">Website: <?php echo $this->churchdetails->siteurl; ?></span>
</div>

<?php if(count($this->churchdetails->leaders)!==0){?>
<div>
<div class="leadhead">Church Leaders</div>

<!--display Leader-->
<ul class="lead">
<?php 
for($i=0;$i<2;$i++){
?>
<li class="leadchurch">
<img src="<?php echo $this->churchdetails->leaders[$i]->profileimage; ?>" alt="<?php echo JText::_('Profile Image'); ?>"></img>
<span class="leadname">
<?php if($this->churchdetails->leaders[$i]->fname==''){?>
<?php echo $this->churchdetails->leaders[$i]->email; ?>
<?php }else{?>
<?php echo $this->churchdetails->leaders[$i]->fname.' '.$this->churchdetails->leaders[$i]->lname; ?>
<?php }?>
</span>

<?php }?>
</li>
</ul>
<?php if(count($this->churchdetails->leaders)>2){?>
<span class="leadcount">
<?php  $remain= count($this->churchdetails->leaders)-(2); echo $remain." More Leader. Click Here for <a href='".JRoute::_('index.php?option=com_christianconnect&task=churchleaders.getChurchLeader&churchid='.$this->churchdetails->id)."'>Full List"; ?>
</span>
<?php }?>
</div>
<?php }?>
<?php 
if(count($this->churchdetails->friends)!==0){?>
<div>
<div class="frndhead">Friend</div>
<ul class="frndchurch">
<!--display friend-->
<?php
$i=0;
foreach($this->churchdetails->friends as $friend){
if($i<=2){
?>
<li class="frndchurch">
<img src="<?php echo $friend->profileimage; ?>" alt="<?php echo JText::_('Profile Image'); ?>"></img>
<span class="frndname">
<?php if($friend->fname==''){?>
<?php echo $friend->email; ?>
<?php }else{?>
<?php echo $friend->fname.' '.$friend->lname; ?>
<?php }?>
</li>
<?php $i++;} }?>
</ul>
<?php if(count($this->churchdetails->friends)>3){?>
<div class="frndcount">
<?php $remain= count($this->churchdetails->friends)-3; echo $remain." More Friend. Click Here for <a href='".JRoute::_('index.php?option=com_christianconnect&view=friendlists&churchid='.$this->churchdetails->id)."'>Full List"; ?>
<?php }?>
</div>
<?php }?>
</div>

<?php if($this->churchdetails->subscription_status==0){?>
<div class="subscibestatus">
Do you run this church</br>
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&view=subscription&churchid='.$this->churchdetails->id); ?>">Click here for subscription</a>
</div>
<?php }?>
<?php if($this->churchdetails->access){?>
<div class="linkedit">

<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&view=managechurch&churchid='.$this->churchdetails->id); ?>">Edit your Church</a>
</div>
<div  class="linkaddlead">
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&view=churchLeaders&churchid='.$this->churchdetails->id); ?>">Add Church Leader</a>
</div>
<!--<tr><td>-->
<!--<div>-->
<!--Edit your Bulletin Board</br>-->
<!--<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&view=bulletinboards&churchid='.$this->churchdetails->id); ?>">Edit your Bulletin Board</a>-->
<!--</div>-->
<!--</td></tr>-->
<?php }?>
<div  class="pagenavformat">
<?php 
if($current !=0)
{
?>
<a class="pagenav" href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$this->navarray[$prev]); ?>">Prev</a>
<?php
}
if($current!=$last_key)
{
?>
<a class="pagenav" href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$this->navarray[$next]); ?>">Next</a>
<?php 
}?>
</div>
</div>
