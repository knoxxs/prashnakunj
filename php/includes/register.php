<?php

require_once __DIR__.'/base.php';

class Register extends Base{

	private $userName, $password, $lastName, $firstName, $email, $phone, $dob, $gender, $country, $city, $state, $qualification, $affiliation, $areasOfExpertise, $securityQuestionID, $securityAnswer, $reviewerKey, $interests, $reputation;

	function __construct($userName, $password, $firstName, $lastName, $email, $phone, $dob, $gender, $country, $city, $state, $qualification, $affiliation, $areasOfExpertise, $securityQuestionID, $securityAnswer, $reviewerKey, $interests)
	{
		parent::__construct();
		$this->userName = $userName;
		$this->password = md5($password);
		$this->lastName = $lastName;
		$this->firstName = $firstName;
		$this->email = $email;
		$this->phone = $phone;
		$this->dob = $dob;
		$this->gender = $gender;
		$this->country = $country;
		$this->city = $city;
		$this->state = $state;
		$this->qualification = serialize($qualification);
		$this->affiliation = $affiliation;
		$this->areasOfExpertise = $areasOfExpertise;
		$this->securityQuestionID = $securityQuestionID;
		$this->securityAnswer = $securityAnswer;
		$this->reviewerKey = $reviewerKey;
		$this->interests = serialize($interests);
		$this->reputation = 0;

	}

	private function validateData(){
		foreach ($this as $key => $value) {
		    //TODO:log all this information
		}
		
		return ($this->validateVar($this->userName) && $this->validateVar($this->password) && $this->validateVar($this->firstName) && $this->validateVar($this->lastName) && $this->validateVar($this->email) && $this->validateVar($this->phone) && $this->validateVar($this->dob) && $this->validateVar($this->gender) && $this->validateVar($this->country) && $this->validateVar($this->city) && $this->validateVar($this->state) && $this->validateVar($this->securityQuestionID) && $this->validateVar($this->securityAnswer) && $this->validateVar($this->reviewerKey) && $this->validateVar($this->areasOfExpertise) && $this->validateVar($this->interests));
	}

	private function isUserNameOrEmailCommon(){
		$db = $this->getDb();

		$db->query('SELECT userName FROM User WHERE userName=? OR email=?',array($this->userName, $this->email));
		$records = $db->fetch_assoc_all();
		if($db->returned_rows > 0){
			if( $records[0]['userName'] == $this->userName){
				return -1; //repeated username
			}else{
				return 0;//repeated email
			}
		}else{
			return 1;
		}
	}

	//TODO:Defianation of validateReviewerKey and change due to its return value in register
	private function validateReviewerKey(){
		return true;
	}

	private function addUser(){
		return $this->getDb()->insert('User',array(
			'userName' => $this->userName,
			'password' => $this->password,
			'firstName' => $this->firstName,
			'lastName' => $this->lastName,
			'email' => $this->email,
			'reputation' => $this->reputation,
			'phone' => $this->phone,
			'DOB' => $this->dob,
			'gender' => $this->gender,
			'qualification' => $this->qualification,
			'interests' => $this->interests,
			'country' => $this->country,
			'city' => $this->city,
			'state' => $this->state,
			'affiliation' => $this->affiliation,
			'securityQuestionID' => $this->securityQuestionID,
			'securityAnswer' => $this->securityAnswer,
			'areasOfExpertise' => $this->areasOfExpertise));
	}

	public function register(){
		if($this->validateData()){
			$temp = $this->isUserNameOrEmailCommon();
			if($temp == 1){
				if($this->reviewerKey > 0){
					if($this->validateReviewerKey()){
						$this->reputation = 500;
						if($this->addUser()){
							if ($this->getDb()->insert('Reviewer', array( 'reviewerKey' => $this->reviewerKey, 'userName' => $this->userName))){
								$this->head['status'] = 201;
							}else{
								$this->head['status'] = 500;
							}
						}else{
							$this->head['status'] = 500;
						}
					}else{
						$this->head['status'] = 203;
						$this->head['message'] = "Wrong reviewer key";
					}
				}elseif($this->reviewerKey == -1){
					if($this->addUser()){
						$this->head['status'] = 201;
					}else{
						$this->head['status'] = 500;
					}
				}
			}else{
				$this->head['status'] = 300;
				$this->head['message'] = $temp == 0?"Repeated Email":"Repeated Username";
			}
		}else{
			$this->head['status'] = 203;
		}

		$this->result = array("head" => $this->head , "body" => '');
		return true;
	}

	public static function userDetail($uname)
	{
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT * FROM User WHERE userName='$uname'");
		$records = $db->fetch_assoc_all()[0];
		$object = array();
		$object['userName'] = $records['userName'];
		$object['firstName'] = $records['firstName'];
		$object['lastName'] = $records['lastName'];
		$object['email'] = $records['email'];
		$object['reputation'] = $records['reputation'];
		$object['phone'] = $records['phone'];
		$object['DOB'] = $records['DOB'];
		$object['gender'] = $records['gender'];
		$object['qualification'] = $records['qualification'];
		$object['interests'] = $records['interests'];
		$object['country'] = $records['country'];
		$object['city'] = $records['city'];
		$object['state'] = $records['state'];
		$object['affiliation'] = $records['affiliation'];
		$object['securityQuestionID'] = $records['securityQuestionID'];
		$object['securityAnswer'] = $records['securityAnswer'];
		$object['areasOfExpertise'] = $records['areasOfExpertise'];

		return $object;
	}

	public static function modifyDetail($uname, array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE User SET firstName=?, lastName=?, email=?, phone=?, DOB=?,  gender=?, qualification=?, interests=?, country=?, city=?, state=?, affiliation=?, securityQuestionID=?, securityAnswer=?, areasOfExpertise=? WHERE userName='$uname'", $data);
		return $status;
	}
}
?>