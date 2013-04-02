<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/user.php';
require_once __DIR__.'/reviewer.php';

class Login extends Base
{ 
	private $userName, $password, $user, $isReviewer;

	function __construct($userName,$password='')
	{
		@session_start();
		parent::__construct();
		$this->userName = $userName;
		$this->password = md5($password);
		$user = null;
		$this->isReviewer = false;
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
					$this->result['head']['status'] = 404;
					$this->result['head']['message'] = 'Wrong Username';
				}
				else if($nrows==1){
					if($this->password == $records[0]['password']){
						$this->result['head']['status'] = 200;

						//checking isReviewer
						$db->query('SELECT userName From Reviewer WHERE userName=?',array($this->userName));
						if($db->returned_rows == 1){
							$this->isReviewer = true;
							$_SESSION['isReviewer'] = $this->isReviewer;
							$this->user = new Reviewer($this->userName);
							$_SESSION['user'] = serialize($this->user);

						}else{
							$this->isReviewer = false;
							$_SESSION['isReviewer'] = $this->isReviewer;
							$this->user = new User($this->userName);
							$_SESSION['user'] = serialize($this->user);
						}
					}
					else{
						$this->result['head']['status'] = 404;
						$this->result['head']['message'] = 'Wrong Password';
					}
				}else{
					//TODO:LOG this is a server/data fault
				}
			}else{
				if(unserialize($_SESSION['user'])->getUserName() == $this->userName){
					$this->result['head']['status'] = 200;
					$this->result['head']['message'] = 'Already Logged in';
				}else{
					$this->logout();
					$this->result['head']['status'] = 409;
					$this->result['head']['message'] = 'Already logged with another account, logging out';
				}
			}
		}else{
			$this->result['head']['status'] = 203;
		}
		return true;
	}

	public function logout(){
		if($this->validateVar($this->userName)){
			if($this->isLoggedIn()){
				if(unserialize($_SESSION['user'])->getUserName() == $this->userName){
					session_destroy();
					$this->result['head']['status'] = 200;
				}else{
					session_destroy();
					$this->result['head']['status'] = 409;
					$this->result['head']['message'] = 'Logged in with another account, logging out';
				}
			}else {
				$this->result['head']['status'] = 405;
			}
		}else{
			$this->result['head']['status'] = 203;			
		}
	
		return true;
	}

	public function toJson(){
		if($this->validateVar($_SESSION)){
			$this->result['body']['firstName'] = unserialize($_SESSION['user'])->getFirstName();
			$this->result['body']['lastName'] = unserialize($_SESSION['user'])->getLastName();
			$this->result['body']['reputation'] = unserialize($_SESSION['user'])->getReputation();
			$this->result['body']['isReviewer'] = $_SESSION['isReviewer'];
		}
		return json_encode($this->result);
	}

}

// $login = new Login('uname5', 'password');
// $login->login();
// echo $login->toJson();
?>