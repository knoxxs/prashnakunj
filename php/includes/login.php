<?

require_once './initialize_database.php';

//TODO: Make password MD5
class Login
{ 
	private $userName, $password;

	function __construct($userName,$password)
	{
		$this->userName = $userName;
		$this->password = ($password);
	}

	public function __destruct(){

	}

	public function __toString(){
		return print_r($this);
	}

	private function getDb(){
		if(!isset($this->db)){
			$this->db = (new Database())->connectToDatabase();
			return $this->db;
		}else{
			return $this->db;
		}
	}

	
	public function validateAndLogin(){ 
		$db = $this->getDb();
		$db->query('SELECT password From User WHERE userName=?',array($this->userName));
		$records = $db->fetch_assoc_all();
		$nrows = $db->returned_rows;
		
		if($nrows==0){
			return 404;		
		}
		else if($nrows==1){
			if($this->password == $result[0]['password']){
				return 200;
			}
			else{
				return 202;
			}
		}
	}

	public static function isLoggedIn(){
		if($_SESSION[])
	}
}


$login = new Login("uname2","123");
echo $login->validateAndLogin();
?>