<?php namespace Kent;
class Form extends \Laravel\Form {

	public static function if_permitted($name)
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_read_{$name}") || $user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return true;
		}
	}

	public static function label_if_permitted($name, $value, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_read_{$name}") || $user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::label($name, $value, $attributes);
		}
	}


	public static function input_if_permitted($type, $name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::input($type, $name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::input($type, $name, $value, $attributes);
		}

		return false;
	}


	public static function text_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::text($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::text($name, $value, $attributes);
		} 

		return false;
	}


	public static function password_if_permitted($name, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::password($name, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::password($name, $attributes);
		}

		return false;
	}


	public static function hidden_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::hidden($name, $value = null, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::hidden($name, $value = null, $attributes);
		}  

		return false;
	}


	public static function search_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::search($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::search($name, $value, $attributes);
		}

		return false;
	}


	public static function email_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::email($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::email($name, $value, $attributes);
		}

		return false;
	}


	public static function telephone_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::telephone($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::telephone($name, $value, $attributes);
		}

		return false;
	}


	public static function url_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::url($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::url($name, $value, $attributes);
		}

		return false;
	}


	public static function number_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::url($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::url($name, $value, $attributes);
		}

		return false;
	}


	public static function date_if_permitted($name, $value = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::date($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::date($name, $value, $attributes);
		}

		return false;
	}


	public static function file_if_permitted($name, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::file($name, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::file($name, $attributes);
		}

		return false;
	}


	public static function textarea_if_permitted($name, $value = '', $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::textarea($name, $value, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::textarea($name, $value, $attributes);
		}

		return false;
	}


	public static function select_if_permitted($name, $options = array(), $selected = null, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::select($name, $options, $selected, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::select($name, $options, $selected, $attributes);
		}

		return false;
	}


	protected static function optgroup_if_permitted($options, $label, $selected)
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::optgroup($options, $label, $selected);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::optgroup($options, $label, $selected);
		}

		return false;
	}


	protected static function option_if_permitted($value, $display, $selected)
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::option($value, $display, $selected);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::option($value, $display, $selected);
		}

		return false;
	}


	public static function checkbox_if_permitted($name, $value = 1, $checked = false, $attributes = array())
	{
		$user = \Laravel\Auth::user();
		
		// remove square brackets added to a checkbox group's name
		$name_for_permission = str_replace('[]', '', $name);

		if($user->can(\URLParams::get_type()."_fields_write_{$name_for_permission}"))
		{
			return parent::checkbox($name, $value, $checked, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name_for_permission}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true, 'disabled' => 'disabled'));
			return parent::checkbox($name, $value, $checked, $attributes);
		}

		return false;
	}


	public static function radio_if_permitted($name, $value = null, $checked = false, $attributes = array())
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::radio($name, $value, $checked, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::radio($name, $value, $checked, $attributes);
		}

		return false;
	}


	protected static function checkable_if_permitted($type, $name, $value, $checked, $attributes)
	{
		$user = \Laravel\Auth::user();

		if($user->can(\URLParams::get_type()."_fields_write_{$name}"))
		{
			return parent::checkable($type, $name, $value, $checked, $attributes);
		} 
		elseif($user->can(\URLParams::get_type()."_fields_read_{$name}"))
		{
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::checkable($type, $name, $value, $checked, $attributes);
		}

		return false;
	}

	public static function imagePicker($name, $value=null)
	{
		$image= null;
		$imgTag= '';
		if(!empty($value)){
			$image = \Image::find($value);
			$imgTag = '<img src="' . $image->thumb_url() . '" alt="' . $image->alt . '">';
		}
		return '<div class="img-picker"><textarea class="picker" rows="1" name="' . $name . '">' . $value .'</textarea><div class="img-wrapper">' . $imgTag . '</div><strong class="img-name">' . ($image? $image->name: '<em>Unassigned</em>') . '</strong></div>';
	}
}