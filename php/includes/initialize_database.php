<?php
require_once (__DIR__.'/zebra/Zebra_Database.php');

class Database{

	function __construct(){

	}
	function connectToDatabase(){
		$conn_error = 'could not connect to database';
		$mysql_host = 'localhost';
		$mysql_user = 'QuestionCorner';
		$mysql_pass = 'password';
		$mysql_db = 'QuestionCorner1';
		$db = new Zebra_Database();
		$db->debug=true;
		$db->connect($mysql_host,$mysql_user,$mysql_pass,$mysql_db);
		mysql_query('SET CHARACTER SET utf8');
		return $db;
	}

}
?>