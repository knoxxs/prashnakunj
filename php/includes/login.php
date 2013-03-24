<?
require_once __DIR__.'/base.php';
require_once __DIR__.'/user.php';

class Login extends Base
{ 
	private $userName, $password, $user;

	function __construct($userName,$password='')
	{
		session_start();
		parent::__construct();
		$this->userName = $userName;
		$this->password = md5($password);
		$user = null;
	}
	
	public function validateData(){
		return ($this->validateVar($this->userName) && $this->validateVar($this->password));
	}

	public function login(){
		if($this->validateData()){
			if(!$this->isLoggedIn()){
				$db = $this->getDb();
				$db->query('SELECT password From User WHERE userName=?',array($this->userName));
				$records = $db->fetch_assoc_all();
				$nrows = $db->returned_rows;

				if($nrows==0){
					$this->head['status'] = 404;
					$this->head['message'] = 'Wrong Username';
				}
				else if($nrows==1){
					if($this->password == $records[0]['password']){
						$this->head['status'] = 200;

						$this->user = new User($this->userName);
						$_SESSION['user'] = $this->user;
					}
					else{
						$this->head['status'] = 404;
						$this->head['message'] = 'Wrong Password';
					}
				}else{
					//TODO:LOG this is a server/data fault
				}
			}else{
				if($_SESSION['user']->getUserName() == $this->userName){
					$this->head['status'] = 200;
					$this->head['message'] = 'Already Logged in';
				}else{
					$this->logout();
					$this->head['status'] = 409;
					$this->head['message'] = 'Already logged with another account, logging out';
				}
			}
		}else{
			$this->head['status'] = 203;
		}
		return true;
	}

	public function logout(){
		if($this->validateVar($this->userName)){
			if($this->isLoggedIn()){
				if($_SESSION['user']->getUserName() == $this->userName){
					session_destroy();
					$this->head['status'] = 200;
				}else{
					session_destroy();
					$this->head['status'] = 409;
					$this->head['message'] = 'Logged in with another account, logging out';
				}
			}else {
				$this->head['status'] = 405;
			}
		}else{
			$this->head['status'] = 203;			
		}
	
		return true;
	}

	public function toJson(){
		if($this->validateVar($_SESSION)){
			$this->body['firstName'] = $_SESSION['user']->getFirstName();
			$this->body['lastName'] = $_SESSION['user']->getLastName();
			$this->body['reputation'] = $_SESSION['user']->getReputation();
			$this->body['isReviewer'] = $_SESSION['user']->getIsReviewer();
		}
		return json_encode($this->result);
	}

}
?>