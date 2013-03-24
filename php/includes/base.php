<?

require_once __DIR__.'/initialize_database.php';

class Base{

	protected $status, $statusMessage;
	protected $result = array("head" => array(), "body" => array());
	protected $head = array("status" => "", "message" => "" );
	protected $body = array();

	function __construct(){
		$this->result['head'] = &$this->head;
		$this->result['body'] = &$this->body;
	}

	protected function getDb(){
		if(!isset($this->db)){
			$this->db = (new Database())->connectToDatabase();
			return $this->db;
		}else{
			return $this->db;
		}
	}

	protected function validateVar($var){
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


}

?>