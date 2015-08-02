<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
 
?>
    <h3 class="subscribhead">Subscription</h3>
 
    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="updhelloworld" name="updhelloworld">
                <fieldset>
                <dl>
                <dd></dd>
                   <div class="churchaddress"><h4><?php echo $this->churchdetail->cname; ?></h4>
                    <dt><p><?php echo $this->churchdetail->address1; ?></p>
                    <p><?php echo $this->churchdetail->address2; ?></p>
                    <p><?php echo $this->churchdetail->city; ?></p>
                    <p><?php echo $this->churchdetail->state; ?></p></dt>
                    <dt><?php echo $this->churchdetail->country; ?></dt></div>
                <dt></dt>
                
                 <dd></dd>
                   <dt><?php echo JText::_('COM_CHRISTIAN_PRICE_LABEL'); ?></dt>
                    <dd>RS.<?php echo $this->churchdetail->subscription_price; ?></dd>
                <dt></dt>
                
                <dd></dd>
                    <dt><?php echo $this->form->getLabel('fname'); ?></dt>
                    <dd><?php echo $this->form->getInput('fname'); ?></dd>
                <dt></dt>
                <dd></dd>
                <dt><?php echo $this->form->getLabel('lname'); ?></dt>
                <dd><?php echo $this->form->getInput('lname'); ?></dd>
                <dd></dd>
                <dt><?php echo $this->form->getLabel('email'); ?></dt>
                <dd><?php echo $this->form->getInput('email'); ?></dd>
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
                <dt><?php echo $this->form->getLabel('subscription_price'); ?></dt>
                <dd><?php echo $this->form->getInput('subscription_price'); ?></dd>
                
                <dt><?php echo $this->form->getLabel('churchid'); ?></dt>
                <dd><?php echo $this->form->getInput('churchid','',$this->churchdetail->id); ?></dd>
                <dd></dd>
                <dt><?php echo $this->form->getLabel('userid'); ?></dt>
                <dd><?php echo $this->form->getInput('userid'); ?></dd>
                <dd></dd>
                <dt></dt>
                
                
                <dd><input type="hidden" name="price" value="<?php echo $this->churchdetail->subscription_price; ?>" />
                	<input type="hidden" name="option" value="com_christianconnect" />
                	<input type="hidden" name="task" value="subscription.saveorder" />
                </dd>
                <dt></dt>
                <dd><button type="submit" class="validate"><span><span><?php echo JText::_('Subscribe Now!'); ?></span></span></button>
                                        <?php echo JHtml::_('form.token'); ?>
                </dd>
                </dl>
        </fieldset>
    </form>
    <div class="clr"></div>