<?php
// user_upload.php
require_once "src/ScriptHandler.php";

$scriptHandler = new ScriptHandler($argv);
$argList = $scriptHandler->getArgList();

if ($argList['help']){
	echo $scriptHandler->getHelp();
}else{
	$scriptHandler->testConnection();
	if ($argList['create_table']){
		$scriptHandler->createTables();
	}else{
		$fileArray = $scriptHandler->readCsvFile($argList['filename']);
		if (empty($fileArray)){
			echo "No record is inserted. \n";
		}else{
			if (!$argList['dry_run']){
				$scriptHandler->insertIntoDB($fileArray);
				echo sizeof($fileArray)." records are inserted. \n";
			}
		}
	}
}




