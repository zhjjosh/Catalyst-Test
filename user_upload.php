<?php
// user_upload.php
require_once "src/ScriptHandler.php";

$scriptHandler = new ScriptHandler($argv);
$argList = $scriptHandler->getArgList();


$scriptHandler->testConnection();
if ($argList['create_table']){
	$scriptHandler->createTables();
}




