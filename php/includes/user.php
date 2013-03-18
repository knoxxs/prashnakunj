<?

require_once './initialize_database.php';
require_once './QuestionPromo.php';
define('MORE_SIZE', 10);
define('DEFAULT_TYPE', 'timestamp');

class User{ 
	private $userName, $firstname, $lastname, $reputation, $favList, $watchLaterList, $historyList, $subscriptionList, $db;

	/**
	 * [__construct description]
	 * @param [type] $userName [description]
	 */
	public function __construct($userName){
		$this->userName = $userName;
		$db = $this->getDb();
		$db->query("SELECT firstname, lastname,reputation FROM User WHERE userName=?",array($userName));
		$records = $db->fetch_assoc_all();
		$this->firstname = $records[0]['firstname'];
		$this->lastname = $records[0]['lastname'];
		$this->reputation = $records[0]['reputation'];
		
		$this->watchLaterList['type'] = DEFAULT_TYPE;
		$this->watchLaterList['list'] = array();
		$this->fetchWatchLaterList();
		
		$this->favList['type'] = DEFAULT_TYPE;
		$this->favList['list'] = array();
		$this->fetchFavList();

		$this->historyList['type'] = DEFAULT_TYPE;
		$this->historyList['list'] = array();
		$this->fetchHistoryList();

		$this->subscriptionList = array();
		$this->fetchSubscriptionList();
	}


	public function __destruct(){

	}

	/**
	 * object summary
	 * @return string [description]
	 */
	public function __toString(){
		return print_r($this);
	}

	/**
	 * check whether db is already connected. If not then makes a connection 
	 * @return DbObject Zebra databse object
	 */
	private function getDb(){
		if(!isset($this->db)){
			$this->db = (new Database())->connectToDatabase();
			return $this->db;
		}else{
			return $this->db;
		}
	}

	/**
	 * validate the type parameter
	 * @param  [type] $type [description]
	 * @return bool       [description]
	 */
	private function typeValidation($type){
		return ($type == 'timestamp') or ($type == 'popularity') or ($type == 'difficultyLevel');
	}

	public function getUsername(){
		return $this->userName;
	}
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getLastname(){
		return $this->lastname;
	}					

	public function getFavList(){
		return $this->favList;
	}

	public function getWatchLaterList(){
		return $this->watchLaterList;
	}

	public function getHistoryList(){
		return $this->historyList;
	}

	public function getSubscriptionList(){
		return $this->subscriptionList;
	}

	public function getReputation(){
		return $this->reputation;
	}
	
	public function setReputation($reputation){
		$this->reputation = $reputation;
		return ;
	}

	public function addFavItem($item){

	}

	public function addWatchLaterItem($item){
		
	}

	public function addHistoryItem($item){
		
	}

	public function addSubscriptionItem($item){
		
	}

	/**
	 * fetch watchLaterList more items based on sortType
	 * @param  string $type [description]
	 * @return int       reuqest status
	 */
	public function fetchWatchLaterList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->watchLaterList['type']){
				$len = count($this->watchLaterList);
			}else{
				$len = 0;
				$this->watchLaterList['type'] = $type;
				$this->watchLaterList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			//SELECT SUM(nature),COUNT(nature) FROM (SELECT qid,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Watch WHERE userName='uname2') as W ORDER BY timestamp LIMIT 0,5) as Q JOIN QuestionVotes as QV ON Q.QID=QV.QID GROUP BY Q.QID
			$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Watch WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->watchLaterList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel'], $this->userName) );
			}
			
			return 200;
		}else{
			return 400;//badRequest due to wrong type
		}
	}

	public function fetchFavList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->favList['type']){
				$len = count($this->favList);
			}else{
				$len = 0;
				$this->favList['type'] = $type;
				$this->favList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Favourites WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->favList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel'], $this->userName) );
			}
			
			return 200;
		}else{
			return 400;
		}
	}

	public function fetchHistoryList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->historyList['type']){
				$len = count($this->historyList);
			}else{
				$len = 0;
				$this->historyList['type'] = $type;
				$this->historyList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Views WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->historyList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel'], $this->userName) );
			}
			
			return 200;
		}else{
			return 400;
		}
	}

	public function fetchSubscriptionList(){
		$db = $this->getDb();
		$db->query('SELECT tagName FROM Subscribe WHERE userName=?',array($this->userName));
		$tagList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($tagList, $value['tagName']);
		}
		$this->subscriptionList = $tagList;

		return 200;
	}

	/**
	 * Returns the complete profile object for the user
	 * @return Profile Object A object which contain the whole profile of the user
	 */
	public function getProfile(){
		return (new Profile($this->userName));
	}

	public function json(){

	}

	//TODO: First need to create Register class , then recieve all parameters and just add to database
	public static function add(){

	}

	//TODO: Need to check password before
	/**
	 * Delete a user from the database
	 * Usage Example: echo User::del('uname');
	 * @param  [type] $userName userName of the user to delete
	 * @return [type]           True or False
	 */
	public static function del($userName){
		return (new Database())->connectToDatabase()->query('DELETE FROM user WHERE userName=?',array($userName));
	}
}

$user = new User('uname2');
print_r($user->getWatchLaterList());