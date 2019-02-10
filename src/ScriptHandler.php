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

	public function insertIntoDB($fileArray){
		try {
			$out = fopen('php://stdout', 'w'); //output handler
			foreach ($fileArray as $key => $value) {
				$name= $value[0];
				$surname= $value[1];
				$email= $value[2];

				$user = new User();
				$user->setName(ucfirst(strtolower($name)));
				$user->setSurname(ucfirst(strtolower($surname)));
				$user->setEmail(strtolower($email));
				$this->entityManager->persist($user);
			}
			$this->entityManager->flush();
			fclose($out);
		}catch (\Exception $e){
			die ("Having Problem inserting the records into the table. No record is inserted. \nError message : ".$e->getMessage()."\n");
		}

	}

	public function createTables(){
		try {
	    	// Create tables in db
			$schemaTool = new SchemaTool($this->entityManager);
			$classes = $this->entityManager->getMetadataFactory()->getAllMetadata();
			$schemaTool->dropSchema($classes);
			$schemaTool->createSchema($classes);
		} catch (Exception $e) {
			die ("Having Problem creating the tables. No record is inserted. \nError message : ".$e->getMessage()."\n");
		}
	}


	public function readCsvFile($file_name)
	{
		try {
			$handle = fopen($file_name, "r");
		}catch (\Exception $e) {
			die ("Having problem reading the CSV file, please check the file and try again. No record is inserted. \n");
		}
		$out = fopen('php://stdout', 'w'); //output handler
		$outExist = false;
		$array  = [];
		$int = 0;
		while (($data   = fgetcsv($handle, 10000, ",")) !== FALSE) {
			if($int> 0 && isset($data[2])){
				$email = $this->cleanEmail($data[2]);
				if ($this->validateEmail($email)){
					$array[] = $data;
				}else{
					if(empty($email)){
						fputs($out, "Row $int: Email Cannot be blank\n"); 
					}else{
						fputs($out, "Row $int: $email is an invalid email\n");
					}
					$outExist = true;
				}
			}
			$int++;
		}
		
		fclose($handle);
		fclose($out);
		return $outExist ? [] : $array;
	}


	public function cleanEmail($email)
	{
		return strtolower(filter_var(trim($email), FILTER_SANITIZE_EMAIL));
	}

	public function validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public function getHelp(){
		$help = "--file [csv file name] – this is the name of the CSV to be parsed, default: users.csv\n";
		$help .= "--create_table – this will cause the PostgreSQL users table to be built (and no further action will be taken)\n";
		$help .= "--dry_run – this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.\n";
		$help .= "-u – PostgreSQL database name, default: postgres\n";
		$help .= "-u – PostgreSQL username, default: postgres\n";
		$help .= "-p – PostgreSQL password, default: password\n";
		$help .= "-h – PostgreSQL host, default: localhost\n";
		$help .= "--help – output the list of directives with details\n";
		return $help;
	} 
}