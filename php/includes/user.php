<?

require_once './initialize_database.php';


class User
{ 
	private $username, $firstname, $lastname, $reputation, $favList, $watchLaterList, $historyList, $subscriptionList, $db;

	/**
	 * [__construct description]
	 * @param [type] $username [description]
	 */
	public function __construct($username)
	{
		$this->username = $username;
		$db = $this->getDb();
		$db->query("SELECT * FROM user WHERE username=?",array($username));
		$records = $db->fetch_assoc_all();
		$this->firstname = $records[0]['firstname'];
		$this->lastname = $records[0]['lastname'];
		// $this->reputation = $records[0]['reputation'];
		// $this->watchLaterList = $records[0]['watchLaterList'];
		// $this->favList = $records[0]['favList'];
		// $this->historyList = $records[0]['historyList'];
		// $this->subscriptionList = $records[0]['subscriptionList'];

	}


	public function __destruct()
	{

	}

	/**
	 * object summary
	 * @return string [description]
	 */
	public function __toString()
	{
		return print_r($this);
	}

	/**
	 * check whether db is already connected. If not then makes a connection 
	 * @return DbObject Zebra databse object
	 */
	private function getDb()
	{
		if(!isset($db)){
			return (new Database())->connectToDatabase();
		}else{
			return $this->$db;
		}
	}

	public function getUsername()
	{
		return $this->username;
	}
	
	public function getFirstname()
	{
		return $this->firstname;
	}
	
	public function getLastname()
	{
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

	public function getReputation()
	{
		return $this->reputation;
	}
	
	public function setReputation($reputation)
	{
		$this->reputation = $reputation;
		return ;
	}

	public function addFavItem($item)
	{

	}

	public function addWatchLaterItem($item)
	{
		
	}

	public function addHistoryItem($item)
	{
		
	}

	public function addSubscriptionItem($item)
	{
		
	}

	/**
	 * Returns the complete profile object for the user
	 * @return Profile Object A object which contain the whole profile of the user
	 */
	public function getProfile()
	{
		return (new Profile($this->username));
	}

	public function json()
	{

	}

	//TODO: First need to create Register class , then recieve all parameters and just add to database
	public static function add()
	{

	}

	//TODO: Need to check password before
	/**
	 * Delete a user from the database
	 * Usage Example: echo User::del('uname');
	 * @param  [type] $username username of the user to delete
	 * @return [type]           True or False
	 */
	public static function del($username)
	{
		return (new Database())->connectToDatabase()->query('DELETE FROM user WHERE username=?',array($username));
	}
}

(new User('uname2'));