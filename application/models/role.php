<?php
class Role extends \Verify\Models\Role {
	
	private static $protected = array(1);

	public static function get_protected()
	{
		return self::$protected;
	}

	public static function all($safe = false)
	{
		if($safe)
		{
			$results = parent::all();
			foreach($results as $key => $result)
			{
				if(in_array($result->id, self::$protected))
				{
					unset($results[$key]);
				}
			}

			return $results;
		}

		return parent::all();
	}

	public static function sanitize_ids($ids)
	{
		foreach($ids as $key => $id)
		{
			if(in_array($id, self::$protected))
			{
				unset($ids[$key]);
			}
		}

		return $ids;
	}

}