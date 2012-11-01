<?php

	if ($this instanceof Behat\Mink\Behat\Context\BaseMinkContext) {
		switch(true) {
			case $path=='the login page':
				$path = "login";
				break;
			case strpos($path,"http://")===0 || strpos($path,"/")===0:
				return $path;
			default:
				throw new Exception("Path '$path' not defined in 'features/bootstrap/paths.php'");
				return;
		}

		return $path;

	}

