<?php
//Extended form call
class ExtForm extends Form{

	/**
	 * Create a HTML select element.
	 *
	 * <code>
	 *		// Create a HTML select element filled with options
	 *		echo Form::select('sizes', array('S' => 'Small', 'L' => 'Large'));
	 *
	 *		// Create a select element with a default selected value
	 *		echo Form::select('sizes', array('S' => 'Small', 'L' => 'Large'), 'L');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @param  string  $selected
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function multiselect($name, $options = array(), $selected = null, $attributes = array())
	{
		$attributes['id'] = static::id($name, $attributes);
		
		$attributes['name'] = $name;
		//Add attrs to add multiselect styles
		$attributes['class'] = 'multiselect';
		$attributes['multiple'] = "multiple";

		$html = array();
		$selected_html = array();
		//Add selected attributes in order at top to ensure order is preserved in UI.

		if (is_array($selected)){
			foreach ($selected as $val)
			{
				if($val != '' && $val != null){
					$selected_html[] = static::option($val, $options[$val], $val);
				}
			}

		}

		//Output the rest.
		foreach ($options as $value => $display)
		{

			if (!(is_array($selected) && in_array($value, $selected))){
				$html[] = static::option($value, $display, $selected);
			}
			
		}

		return '<select'.HTML::attributes($attributes).'>'.implode('', $selected_html).implode('', $html).'</select>';
	}

}