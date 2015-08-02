<?php
/**
 * @version		$Id: index.php 19070 2011-10-09 13:59:50Z chdemko $
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app				= JFactory::getApplication();
$doc				= JFactory::getDocument();
$templateparams		= $app->getTemplate(true)->params;

$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery-1.7.1.min.js', 'text/javascript');
/*
 * script for autosuggest, fancybox functionality........
 */

$doc->addStyleSheet($this->baseurl.'/components/com_users/css/style.css');
$doc->addStyleSheet($this->baseurl.'/components/com_users/css/jquery.autocomplete.css');
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/jquery.fancybox.css');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.autocomplete.pack.js', 'text/javascript');
$doc->addScript($this->baseurl.'/components/com_users/js/script.js', 'text/javascript');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.fancybox.js', 'text/javascript');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<link rel="shortcut icon" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>images/favicon.ico" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/top-menu.css" type="text/css" />
<style type="text/css">
#sidebar1 > .moduletable, .moduletable_menu, .moduletable_text, .item-page img, .blog-featured img, #wrapper
{ behavior: url(<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/PIE.php);}</style>
<!--[if lte IE 7]>
<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/ie7.css" rel="stylesheet" type="text/css" />
<![endif]-->
  </head>
	<body id="body">
	  <div id="wrapper" style="z-index:1"> 
		  <!-- TOP DROP MENU-->
		  <div id="navBar">
				<jdoc:include type="modules" name="dropmenu" style="xhtml" />
		  </div>
	      <div class="clear"></div> 	
		  <div id="headerLogo" style="z-index:1">
				<div id="logo">
					<a href="index.php"><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/blank-lg.gif" alt="Home Page" /></a>
				</div>
				<div id="topRight">	
						<div id="breadcrumb"><jdoc:include type="modules" name="breadcrumbsload" /></div>
						<div class="clear"></div>
						<div id="search"><jdoc:include type="modules" name="position-0" /></div> <!--search module-->
				</div><!--END TOP RIGHT-->   
	      </div><!--END HEADER LOGO-->
		  <div class="clear"></div>
	
			<!--HOME PAGE RANDOM IMAGE MODULE-->
			<jdoc:include type="modules" name="random-images" style="xhtml" />
	       <div class="clear"></div>
			 
	     <div id="content">
			    
				   <div id="sidebar1">
				   	  <div id="loginmodulearea">
				   		<?php if ($this->countModules ('facebook')) { ?>
					 	  <jdoc:include type="modules" name="facebook" style="fx" />
					    <?php }?> 
					    <?php if ($this->countModules ('twitter')) { ?>
					 	  <jdoc:include type="modules" name="twitter" style="fx" />
					    <?php }?> 
					    <?php if ($this->countModules ('sidebar-1')) { ?>
					 	  <jdoc:include type="modules" name="sidebar-1" style="fx" />
					    <?php }?> 
					   </div>
				   </div>	
	
				   	<div id="mainRight">
						 <jdoc:include type="modules" name="user1" style="xhtml" />
					     <div class="clear"></div>
						<jdoc:include type="message" /><jdoc:include type="component" />
			       </div><!--END RIGHT CONTENT -->
		
			</div><!--END CONTENT-->	
		<div class="clear"></div>					
	   
		 <div id="copyright">
		     &copy; 2013 Church . All Rights Reserved.  <br /><br />
		 </div>
		 <!--END COPYRIGHT -->
		 <jdoc:include type="modules" name="user2" style="xhtml" />
		 <div class="clear"></div>
	 </div><!--END TEMPLATE WRAPPER-->
	<jdoc:include type="modules" name="debug" />			
	</body>
</html>