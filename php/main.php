<?

require_once __DIR__.'/includes/base.php';

define('PATH_REGEX_PATTERN',"!\/([^\/]+)!");
@session_start();
//TODO: Need to handle if PATH_INFO doesnot exist
preg_match_all(PATH_REGEX_PATTERN, $_SERVER['PATH_INFO'], $regMatches);

$base = new Base();
$result = '';

//TODO: session_start placing for minimizing auto serializing errors, cuurently only placed in login constructor

if( isset($regMatches[1][0]) && ( !empty($regMatches[1][0]) ) ){
	switch ($regMatches[1][0]) {
		case 'register':
			if(sizeof($_POST) == 18){
				require_once __DIR__.'/includes/register.php';
				$register = new Register($_POST['userName'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phone'], $_POST['dob'], $_POST['gender'], $_POST['country'], $_POST['city'], $_POST['state'], $_POST['qualification'], $_POST['affiliation'], $_POST['areasOfExpertise'], $_POST['securityQuestionID'], $_POST['securityAnswer'], $_POST['reviewerKey'], $_POST['interests']);
				if($register->register()){
					$result = $register->toJson();
				}else{
					$result = json_encode( array('head' => array('status' => 500, 'message'=>''), 'body' => '') );	
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 18'), 'body' => '') );
			}
			break;
		
		case 'login':
			if(sizeof($_POST) == 2){
				require_once __DIR__.'/includes/login.php';
				$login = new Login($_POST['userName'], $_POST['password']);
				if($login->login()){
					$result = $login->toJson();
				}else{
					$result = json_encode( array('head' => array('status' => 500, 'message'=>''), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
			}
			break;

		case 'logout':
			if(sizeof($_POST) == 1){
				require_once __DIR__.'/includes/login.php';
				$login = new Login($_POST['userName']);
				if($login->logout()){
					$result = $login->toJson();
				}else{
					$result = json_encode( array('head' => array('status' => 500, 'message'=>''), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 1'), 'body' => '') );
			}
			break;

		case 'list':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				if($base->isLoggedIn()){
					if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) ){
						$type = $regMatches[1][2];
						switch($regMatches[1][1]) {
							case 'favourite':
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$list = $user->getFavListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							case 'watchLater':
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$list = $user->getWatchLaterListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							case 'history':
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$list = $user->getHistoryListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							default:
								$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such list'), 'body' => '') );
								break;
						}
					}elseif($regMatches[1][1] == 'subscriptionList'){
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$list = $user->getSubscriptionList();
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
					}else{
						$result = json_encode( array('head' => array('status' => 400, 'message'=>'No sorttype specified'), 'body' => '') );
					}
				}else{
					$result = json_encode( array('head' => array('status' => 401, 'message'=>''), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 400, 'message'=>'No list specified'), 'body' => '') );
			}
			break;
		
		default:
			$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call'), 'body' => '') );
			break;
	}
	exit($result);
}else{
	exit( json_encode(array('head' => array('status' => 404, 'message'=>''), 'body' => '')) );
}
?>