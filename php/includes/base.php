<?php

require_once __DIR__.'/initialize_database.php';
define('MORE_SIZE', 10);
define('DEFAULT_TYPE', 'timestamp');
define('DEFAULT_TIME_DELAY_FOR_LOCK', 1800);
define('DEFAULT_SLEEP_TIME', 10);

class Base{

	protected $status, $statusMessage, $db, $lockOn;
	public $result = array("head" => array("status" => "", "message" => "" ), "body" => array());

	function __construct(){
		$this->db = null;
		$this->lockOn = false;
	}

	protected function getDb(){
		if(!isset($this->db)){
			$this->db = (new Database())->connectToDatabase();
			return $this->db;
		}else{
			return $this->db;
		}
	}

	public function validateVar($var){
		return (isset($var) && (!empty($var)) );
	}

	protected function __autoload($class_name) {
	    if(file_exists($class_name . '.php')) {
	        require_once($class_name . '.php');    
	    } else {
	        throw new Exception("Unable to load $class_name.");
	    }
	}

	public function __destruct(){

	}

	public function __toString(){
		return print_r($this);
	}

	public function toJson(){
		return json_encode($this->result);
	}

	public static function isLoggedIn(){
		return (isset($_SESSION['user']) && !empty($_SESSION['user']) );
	}

	/**
	 * validate the type parameter
	 * @param  [type] $type [description]
	 * @return bool       [description]
	 */
	protected function typeValidation($type){
		return ($type == 'timestamp') or ($type == 'popularity') or ($type == 'difficultyLevel');
	}

	public function isLockOn()
	{
	    return $this->lockOn;
	}
	
	public function lockOff(){
		$this->lockOn = false;
	}

	public function runMonitor(){
		
		$startTime = time();
    	while($this->isLockOn){
        	if(time() - $startTime > DEFAULT_TIME_DELAY_FOR_LOCK){
        		$review = unserialize($_SESSION['locked']);
        		return $review->unLockReview();
        	}
       		sleep(DEFAULT_SLEEP_TIME);
    	}
    	return true;
	}
}

?>