<?

require_once './initialize_database.php';

class Register{

	private $userName, $password, $lastName, $firstName, $email, $phone, $dob, $gender, $country, $city, $state, $qualification, $affiliation, $aresOfExpertise, $securityQuestionID, $securityAnswer, $reviewerKey;

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

}
?>