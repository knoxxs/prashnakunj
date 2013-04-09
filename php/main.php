<?php
require_once __DIR__.'/includes/base.php';

header('Access-Control-Allow-Origin: *');

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
				$result = json_encode( array('head' => array('status' => 200, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
			}
			break;

		case 'logout':
				require_once __DIR__.'/includes/login.php';
				if($base->validateVar($_SESSION) && $base->validateVar($_SESSION['user'])){
					$login = new Login(unserialize($_SESSION['user'])->getUserName());
					if($login->logout()){
						$result = $login->toJson();
					}else{
						$result = json_encode( array('head' => array('status' => 500, 'message'=>''), 'body' => '') );
					}
				}else{
					$result = json_encode( array('head' => array('status' => 406, 'message'=>'No user Logged In'), 'body' => '') );;
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
								require_once __DIR__.'/includes/reviewer.php';
								$user = unserialize($_SESSION['user']);
								if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) && ($regMatches[1][2] == 'more') ){
									$user->fetchFavListArray($type);
								}
								$list = $user->getFavListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							case 'watchLater':
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) && ($regMatches[1][2] == 'more') ){
									$user->getWatchLaterListArray($type);
								}
								$list = $user->getWatchLaterListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							case 'history':
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) && ($regMatches[1][2] == 'more') ){
									$user->getHistoryListArray($type);
								}
								$list = $user->getHistoryListArray($type);
								$base->result = $user->result;
								$base->result['body'] = $list;
								$result = json_encode($base->result);
								break;
							case 'reviewHistory':
								if($base->validateVar($_SESSION['isReviewer']) && $_SESSION['isReviewer']){
									require_once __DIR__.'/includes/reviewer.php';
									$user = unserialize($_SESSION['user']);
									if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) && ($regMatches[1][2] == 'more') ){
										$user->getReviewHistoryListArray();
									}
									$list = $user->getReviewHistoryListArray();
									$base->result = $user->result;
									$base->result['body'] = $list;
									$result = json_encode($base->result);
								}else{
									$result = json_encode( array('head' => array('status' => 401, 'message'=>"Don't have reviewer Access"), 'body' => '') );
								}
								break;
							case 'toBeReview':
								if($base->validateVar($_SESSION['isReviewer']) && $_SESSION['isReviewer']){
									require_once __DIR__.'/includes/reviewer.php';
									$user = unserialize($_SESSION['user']);
									if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) && ($regMatches[1][2] == 'more') ){
										$user->getToBeReviewListArray();
									}
									$list = $user->getToBeReviewListArray();
									$base->result = $user->result;
									$base->result['body'] = $list;
									$result = json_encode($base->result);
								}else{
									$result = json_encode( array('head' => array('status' => 401, 'message'=>"Don't have reviewer Access"), 'body' => '') );
								}
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

		case 'questions':
			if(sizeof($_GET) == 4){
				require_once __DIR__.'/includes/question.php';
				if( $base->validateVar($_GET['type']) && $base->validateVar($_GET['number']) && $base->validateVar($_GET['latestQuestionTime']) && $base->validateVar($_GET['scroll']) ){
					$result = Question::getQuestions($_GET['type'], $_GET['number'], $_GET['latestQuestionTime'], $_GET['scroll']);
					$result = json_encode($result);
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 4'), 'body' => '') );
			}
			break;

		case 'question':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				require_once __DIR__.'/includes/question.php';
				$id = $regMatches[1][1];
					$result = new Question($id);
					$result = json_encode($result->toArray());
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
			}
			break;

		case 'search':
			if( sizeof($_GET) == 1 ){
				require_once __DIR__.'/includes/question.php';
				if($base->validateVar($_GET['tag'])){
					$result = Question::searchTag($_GET['tag']);
					$result = json_encode($result);
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Received 0 fields expected 1'), 'body' => '') );
			}
			break;

		case 'reviewLock':
			if(sizeof($_GET) == 3){
				require_once __DIR__.'/includes/review.php';
				//if( $base->validateVar($_GET['QID']) && $base->validateVar($_GET['suggestionUserName']) && $base->validateVar($_GET['suggestionTimeStamp']) ){
				if( ($base->validateVar($_GET['QID']) && empty($_GET['suggestionUserName']) && empty($_GET['suggestionTimeStamp']) )  ||  ($base->validateVar($_GET['QID']) && $base->validateVar($_GET['suggestionUserName']) && $base->validateVar($_GET['suggestionTimeStamp']) )){
					if($base->validateVar($_SESSION['locked'])){
						//TODO
					}else{
						$review = new Review($_GET['QID'], $_GET['suggestionUserName'], $_GET['suggestionTimeStamp']);
						if($review->lockReview()){
							$_SESSION['locked'] = unserialize($review);

						}else{
							//TODO
						}
					}
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 3'), 'body' => '') );
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