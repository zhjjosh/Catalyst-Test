<?php
// src/ScriptHandler.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

require_once "vendor/autoload.php";

class ScriptHandler
{

	private $argList;
	private $entityManager;

	public function __construct($argv)
	{
		$this->argList = array(
			"filename" => "users.csv", 
			"create_table" => false, 
			"dry_run" => false, 
			"db_name" => 'postgres', 
			"db_user" => 'postgres', 
			"db_password" => 'password', 
			"db_host"=> 'localhost', 
			"help" => false
		);
		$this->setArgList($argv);
		$this->setEntityManager();
	}

	public function getArgList(){
		return $this->argList;
	}

	public function getEntityManager(){
		return $this->entityManager;
	}

	public function setEntityManager(){
		// Create a simple "default" Doctrine ORM configuration for Annotations
		$isDevMode = true;
		$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), $isDevMode);

		// database configuration parameters
		$conn = array(
			'dbname' => $this->argList['db_name'],
			'user' => $this->argList['db_user'],
			'password' => $this->argList['db_password'],
			'host' => $this->argList['db_host'],
			'driver' => 'pdo_pgsql',
		);

// obtaining the entity manager
		$this->entityManager =  EntityManager::create($conn, $config);
	}

	public function setArgList($argv){
		$size = sizeof($argv);

		foreach($argv as $key => $arg)
		{
			switch ($arg) {
				case '--file':
				if($key+1 < $size){
					$this->argList['filename'] = $argv[$key+1];
				}else{
					echo "You are trying to set filename, but filename is not given, the default filename users.csv will be used\n";
				}
				break;

				case '--create_table':
				$this->argList['create_table'] = True;
				break;

				case '--dry_run':
				$this->argList['dry_run'] = True;
				break;

				case '-d':
				if($key+1 < $size){
					$this->argList['db_name'] = $argv[$key+1];
				}else{
					die("You are trying to set database, but database is not given, the script has been stopped\n");
				}

				break;
				case '-u':
				if($key+1 < $size){
					$this->argList['db_user'] = $argv[$key+1];
				}else{
					die("You are trying to set username, but username is not given, the script has been stopped\n");
				}
				break;

				case '-p':
				if($key+1 < $size){
					$this->argList['db_password'] = $argv[$key+1];
				}else{
					die("You are trying to set password, but password is not given, the script has been stopped\n");
				}
				break;

				case '-h':
				if($key+1 < $size){
					$this->argList['db_host'] = $argv[$key+1];
				}else{
					die("You are trying to set host, but host is not given, the script has been stopped\n");
				}
				break;

				case '--help':
				$this->argList['help'] = True;
				break;

				default:

				break;
			}
		}
		$this->argList = $this->argList;
	}

	public function testConnection(){
		try {
			$this->entityManager->getConnection()->connect();
		} catch (\Exception $e) {
			die ("Cannot connect to the database, please check the database, username, password and host are correct\n");
		}
	}

}