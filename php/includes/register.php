<?

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
		$this->qualification = $qualification;
		$this->affiliation = $affiliation;
		$this->areasOfExpertise = $areasOfExpertise;
		$this->securityQuestionID = $securityQuestionID;
		$this->securityAnswer = $securityAnswer;
		$this->reviewerKey = $reviewerKey;
		$this->interests = $interests;
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

	public function toJson(){
		parent::toJson();
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

		//TODO:decide how to return
	}

}
?>