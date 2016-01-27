<?php

$logFile = dirname(dirname(dirname(__FILE__))) . '/storage/logs/sits_import.log';

if(!file_exists($logFile)){
	// All is well
	echo "Everything worked fine.\r\n";
	exit(0);
}


$data = file_get_contents($logFile);

// Errors
if(strpos($data,'ERROR') !== false){
	echo "A critical error occured during the last run!\r\n";
	exit(2);
}

// Warnings
if(strpos($data,'WARN') !== false){
	echo "Warnings were raised in the last run.\r\n";
	exit(1);
}

// All is well
echo "Everything worked fine.\r\n";
exit(0);


// If by some fluke you get here - wut?
echo "Wut?\r\n";
exit(3);