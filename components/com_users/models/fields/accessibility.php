<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
JFormHelper::loadFieldClass('list');
 
/**
 * Accessibility Form Field class for the users component
 */
class JFormFieldAccessibility extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Accessibility';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{   
//		$html = '';
//        $html .= '<select id="bed_id" class="bedselect" name="bed_id[]" multiple="multiple" size="6" required="required">';
//        $html .= '<option value="0">-- Select Bed--</option>';
//        for ($i=0, $n=count( $options ); $i < $n; $i++) 
//        {
//             $row = &$options[$i];
//             if(in_array($row->id,$expval))
//             echo '<option selected="selected" value="'. $row->id.'">'.$row->name.'</option>';
//             else
//             echo '<option value="'. $row->id.'">'.$row->name.'</option>';
//             $selected	= (in_array($row->id, $expval)) ? ' selected="selected"' : '';
//             $html .= '<option value="'. $row->id.'"'.$selected.'>'. $row->name.'</option>';
//        }
//       $html .= '</select>';
//       echo $html;
	}

}