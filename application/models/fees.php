<?php

class Fees {

	public static function getFeeInfoForPos($pos){
		$band = static::getFeeBand($pos);
		return static::getFeeInfo($band);
	}
	public static function getFeeBand($pos){
		return 'P1';
	}
	public static function getFeeInfo($feeband){
		return array('part-time' => '4000', 'full-time' => '9000', 'band'=> 'P1');
	}

}