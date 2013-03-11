<?php namespace Kent;
class Form extends \Laravel\Form {

	public static function labelIfPermitted($name, $value, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_read_{$name}") || $user->can("fields_write_{$name}")){
			return parent::label($name, $value, $attributes);
		}
	}


	public static function inputIfPermitted($type, $name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::input($type, $name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::input($type, $name, $value, $attributes);
		}

		return false;
	}


	public static function textIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::text($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::text($name, $value, $attributes);
		} 

		return false;
	}


	public static function passwordIfPermitted($name, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::password($name, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::password($name, $attributes);
		}

		return false;
	}


	public static function hiddenIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::hidden($name, $value = null, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::hidden($name, $value = null, $attributes);
		}  

		return false;
	}


	public static function searchIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::search($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::search($name, $value, $attributes);
		}

		return false;
	}


	public static function emailIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::email($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::email($name, $value, $attributes);
		}

		return false;
	}


	public static function telephoneIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::telephone($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::telephone($name, $value, $attributes);
		}

		return false;
	}


	public static function urlIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::url($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::url($name, $value, $attributes);
		}

		return false;
	}


	public static function numberIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::url($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::url($name, $value, $attributes);
		}

		return false;
	}


	public static function dateIfPermitted($name, $value = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::date($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::date($name, $value, $attributes);
		}

		return false;
	}


	public static function fileIfPermitted($name, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::file($name, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::file($name, $attributes);
		}

		return false;
	}


	public static function textareaIfPermitted($name, $value = '', $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::textarea($name, $value, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::textarea($name, $value, $attributes);
		}

		return false;
	}


	public static function selectIfPermitted($name, $options = array(), $selected = null, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::select($name, $options, $selected, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::select($name, $options, $selected, $attributes);
		}

		return false;
	}


	protected static function optgroupIfPermitted($options, $label, $selected){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::optgroup($options, $label, $selected);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::optgroup($options, $label, $selected);
		}

		return false;
	}


	protected static function optionIfPermitted($value, $display, $selected){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::option($value, $display, $selected);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::option($value, $display, $selected);
		}

		return false;
	}


	public static function checkboxIfPermitted($name, $value = 1, $checked = false, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::checkbox($name, $value, $checked, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::checkbox($name, $value, $checked, $attributes);
		}

		return false;
	}


	public static function radioIfPermitted($name, $value = null, $checked = false, $attributes = array()){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::radio($name, $value, $checked, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::radio($name, $value, $checked, $attributes);
		}

		return false;
	}


	protected static function checkableIfPermitted($type, $name, $value, $checked, $attributes){
		$user = \Laravel\Auth::user();

		if($user->can("fields_write_{$name}")){
			return parent::checkable($type, $name, $value, $checked, $attributes);
		} elseif($user->can("fields_read_{$name}")){
			$attributes = array_merge($attributes, array('readonly' => true));
			return parent::checkable($type, $name, $value, $checked, $attributes);
		}

		return false;
	}
}