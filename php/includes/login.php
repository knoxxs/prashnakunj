<?
/**
* Login class
*/

require_once './initialize_database.php';
class Login
{ 
	var $username;
	var $password;
	var 

	function __construct($username,$password)
	{
		# code...
		$this->username = $username;
		$this->password = md5($password);
	}
	
	function validateAndLogin()
	{ 
		$db = (new Database())->connectToDatabase();
		$db->select('password','User','user_name=?',array($this->username));
		$result = $db->fetch_assoc_all();
		$num_rows = $db->returned_rows;
		
		if($num_rows==0){
			return 404;		
		}
		else if($num_rows==1){
			if($this->password == $result[0]['password']){
				return 202;
			}
			else{
				return ;		
			}
		}

	}

}


$login = new Login("username","password");
$login -> validateAndLogin();
?>