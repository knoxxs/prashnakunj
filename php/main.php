<?php
require_once __DIR__.'/includes/base.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Max-Age: 186400');

set_time_limit(1900);

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

		case 'userdetail':
			if($base->isLoggedIn()){
				require_once __DIR__.'/includes/register.php';
				require_once __DIR__.'/includes/user.php';
				$user = unserialize($_SESSION['user']);
				$uname = $user->getUsername();
				$object = Register::userDetail($uname);
				$result = json_encode( array('head' => array('status' => 200, 'message'=>'User Details'), 'body' => $object) );
			}
			else{
				$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
			}
			break;

		case 'modifydetail':
			if($base->isLoggedIn()){
				if (sizeof($_POST) == 15) {
					require_once __DIR__.'/includes/register.php';
					require_once __DIR__.'/includes/user.php';
					$user = unserialize($_SESSION['user']);
					$uname = $user->getUsername();
					$status = Register::modifyDetail($uname, array(
												'firstName' => $_POST['firstName'], 
												'lastName' => $_POST['lastName'],
												'email' => $_POST['email'],
												'phone' => $_POST['phone'],
												'DOB' => $_POST['DOB'],
												'gender' => $_POST['gender'],
												'qualification' => $_POST['qualification'],
												'interests' => $_POST['interests'],
												'country' => $_POST['country'],
												'city' => $_POST['city'],
												'state' => $_POST['state'],
												'affiliation' => $_POST['affiliation'],
												'securityQuestionID' => $_POST['securityQuestionID'],
												'securityAnswer' => $_POST['securityAnswer'],
												'areasOfExpertise' => $_POST['areasOfExpertise']));
					if ($status) {
						$result = json_encode( array('head' => array('status' => 200, 'message'=>'User Details Modified Successfully'), 'body' => '') );
					}
					else{
						$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Server Error'), 'body' => '') );
					}
				}
				else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 15'), 'body' => '') );
				}
			}
			else{
				$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
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

		case 'search':
			if( sizeof($_GET) == 1 ){
				require_once __DIR__.'/includes/question.php';
				if($base->validateVar($_GET['tag'])){
					$result = Question::searchTag($_GET['tag']);
					$result = array("head" => array('status' => 200, 'message'=>'Search Result'), 'body' => $result);
					$result = json_encode($result);
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Received 0 fields expected 1'), 'body' => '') );
			}
			break;

		case 'forgotpwd':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				switch ($regMatches[1][1]) {
					case 'checkuname':
						if (sizeof($_POST) == 1) {
							require_once __DIR__.'/includes/user.php';
							$check = User::unameExists($_POST['userName']);
							if ($check == $_POST['userName']) {
								$SID = User::securityQuestionNumber($check);
								if ($SID != NULL) {
									$result = json_encode( array('head' => array('status' => 200, 'message'=>'UserName exists in database'), 'body' => $SID) );
								}
								else{
									$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error no security question found'), 'body' > '') );
								}	
							}
							else{
								$result = json_encode( array('head' => array('status' => 404, 'message'=>'UserName Not Found'), 'body' => '') );
							}
						}
						else{
							$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 1'), 'body' => '') );
						}
						break;

					case 'updatepwd':
						if (sizeof($_POST) == 4) {
							require_once __DIR__.'/includes/user.php';
							$answer = User::securityAnswer($_POST['userName']);
							if ($answer != NULL) {
								$SID = User::securityQuestionNumber($_POST['userName']);
								if ($answer == $_POST['securityAnswer'] && $SID == $_POST['securityQuestionNumber']) {
									$update = User::updatePwd($_POST['userName'], $_POST['newPassword']);
									if ($update) {
										$result = json_encode( array('head' => array('status' => 200, 'message'=>'Password Updated Successfully'), 'body' => '') );
									}
									else{
										$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error - Password Update Unsuccessfull'), 'body' => '') );
									}
								}
								else{
									$result = json_encode( array('head' => array('status' => 401, 'message'=>'Authorization Failed'), 'body' => '') );
								}
							}
							else{
								$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error - No security Answer Found'), 'body' => '') );
							}
						}
						else{
							$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
						}
						break;
						
					default:
						$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call for this url'), 'body' => '') );
						break;
				}
			}
			else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'URL Incomplete'), 'body' => '') );
			}
			break;

		case 'tag':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				if($base->isLoggedIn()){
					switch($regMatches[1][1]){
						case 'create':
							if (sizeof($_POST) == 2) {
								require_once __DIR__.'/includes/question.php';
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$uname = $user->getUsername();
								$condition = TRUE;
								$tag = $_POST['newTag'];
								$tag = strtolower($tag);
								$check = Question::findTags($tag);
								if ($check) {
									$result = json_encode( array('head' => array('status' => 409, 'message'=>'Tag '.$tag.' already exists'), 'body' => '') );
								}
								else{
									$parents = $_POST['parents'];
									foreach ($parents as $key => $value) {
										$checkparent = Question::findTags($value);
										if (!$checkparent) {
											$condition = FALSE;
											break;
										}
									}
									if ($condition) {
										$status = Question::createTag($tag, $uname);
										if ($status) {
											$ifstat = TRUE;
											foreach ($parents as $key => $value) {
												$parentstatus = Question::addParent($tag, $value);
												if (!$parentstatus) {
													$result = json_encode( array('head' => array('status' => 500, 'message'=>'Tag Added Successfully but error adding parents - Internal Server Error'), 'body' => '') );
													$ifstat = FALSE;
													break;
												}
											}
											if ($ifstat) {
												$result = json_encode( array("head" => array('status' => 200, 'message'=>'New Tag - '.$tag.' Created Successfully'), 'body' => '') );
											}
										}
										else
										{
											$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Server Error - Error Creating New Tag'), 'body' => '') );
										}
									}
									else{
										$result = json_encode( array('head' => array('status' => 406, 'message'=>'Please change parents - One or more you are posting does not exists'), 'body' => '') );
									}
								}
							}
							else{
								$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
							}
							break;

						default :
							$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call for this url'), 'body' => '') );
							break;
					}
				}
				else{
					$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
				}
			}
			else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete URL'), 'body' => '') );
			}
			break;

		case 'topten':
			if(sizeof($_GET == 1)){
				require_once __DIR__.'/includes/question.php';
				$object = Question::topTenTags();
				$result = json_encode( array("head" => array('status' => 200, 'message'=>'Top Ten Tags'), 'body' => $object) );
			}
			else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 1'), 'body' => '') );
			}
			break;

		case 'reviewLock':
			if(sizeof($_GET) == 3){
				require_once __DIR__.'/includes/reviewer.php';
				//if( $base->validateVar($_GET['QID']) && $base->validateVar($_GET['suggestionUserName']) && $base->validateVar($_GET['suggestionTimeStamp']) ){
				if( ($base->validateVar($_GET['QID']) && empty($_GET['suggestionUserName']) && empty($_GET['suggestionTimeStamp']) )  ||  ($base->validateVar($_GET['QID']) && $base->validateVar($_GET['suggestionUserName']) && $base->validateVar($_GET['suggestionTimeStamp']) )){
					if($base->isLoggedIn()){
						if($_SESSION['isReviewer']){
							$suggestionUserName = $_GET['suggestionUserName'] == "null" ?NULL :$_GET['suggestionUserName'];
							$suggestionTimeStamp = $_GET['suggestionTimeStamp'] == "null" ?NULL :$_GET['suggestionTimeStamp'];
							$ret = unserialize($_SESSION['user'])->setLock($_GET['QID'], $suggestionUserName, $suggestionTimeStamp);
							if($ret['head']['status'] == 200){
								$_SESSION['LAST_ACTIVITY'] = time();
								
								while(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < DEFAULT_SLEEP_TIME) && isset($_SESSION['locked'])) {
									session_write_close();
									sleep(10);
									session_start();
								}
								if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] < DEFAULT_SLEEP_TIME){
									unserialize($_SESSION['user'])->removeLock();
									$result = unserialize($_SESSION['user'])->result;
									$result['body'] = '';
									$result = json_encode($result);
								}else{
									$result = json_encode( array('head' => array('status' => 408, 'message'=>'Review submitted'), 'body' => '') );
								}
							}else{
								$result = json_encode( array('head' => $ret, 'body' => '') );
							}
						}else{
							$result = json_encode( array('head' => array('status' => 409, 'message'=>'Not have rights'), 'body' => '') );
						}
					}else{
						$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
					}
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 3'), 'body' => '') );
			}
			break;

		case 'reviewUnlock':
			require_once __DIR__.'/includes/reviewer.php';
			//if( $base->validateVar($_GET['QID']) && $base->validateVar($_GET['suggestionUserName']) && $base->validateVar($_GET['suggestionTimeStamp']) ){
			if($base->isLoggedIn()){
				if($_SESSION['isReviewer']){
					if(unserialize($_SESSION['user'])->removeLock()){
						$result = unserialize($_SESSION['user'])->result;
					}else{
						$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Server Error'), 'body' => '') );
					}
					$result = json_encode($result);
				}else{
					$result = json_encode( array('head' => array('status' => 409, 'message'=>'Not have rights'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
			}
			break;

		case 'submitReviewedSuggestion':
			if(sizeof($_POST) == 4){
				require_once __DIR__.'/includes/suggestion.php';
				require_once __DIR__.'/includes/review.php';
				//if( $base->validateVar($_POST['QID']) && $base->validateVar($_POST['suggestionUserName']) && $base->validateVar($_POST['suggestionTimeStamp']) ){
				if($base->validateVar($_POST['QID']) && $base->validateVar($_POST['suggestionUserName']) && $base->validateVar($_POST['suggestionTimeStamp']) && $base->validateVar($_POST['string'])  ){
					if($base->isLoggedIn()){
						if($_SESSION['isReviewer']){
							if($base->validateVar($_SESSION['LAST_ACTIVITY'])){
								$review = unserialize($_SESSION['locked']);
								
								if($review->getQID() == $_POST['QID'] && $review->getSuggestionUserName() == $_POST['suggestionUserName'] && $review->getSuggestionTimeStamp() == $_POST['suggestionTimeStamp'] ){
									if(Suggestion::reviewSuggestion($_POST['QID'], $_POST['suggestionUserName'], $_POST['suggestionTimeStamp'], $_POST['string'])){
										unset($_SESSION['locked']);
										unset($_SESSION['LAST_ACTIVITY']);
										$result = json_encode(array('head' => array('status' => 200, 'message'=>''), 'body' => '') );
									}else{
										$result = json_encode( array('head' => array('status' => 304, 'message'=>'Unable to save the review'), 'body' => '') );
									}
								}else{
									$result = json_encode( array('head' => array('status' => 403, 'message'=>'Lock a different review'), 'body' => '') );	
								}
							}else{
								$result = json_encode( array('head' => array('status' => 406, 'message'=>'Lock not acquired'), 'body' => '') );
							}
						}else{
							$result = json_encode( array('head' => array('status' => 409, 'message'=>'Not have rights'), 'body' => '') );
						}
					}else{
						$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
					}
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
			}
			break;

		case 'answer':
			switch($regMatches[1][1]){
				case 'post':
					if(sizeof($_POST) == 2){
						require_once __DIR__.'/includes/answer.php';
						//if( $base->validateVar($_POST['QID']) && $base->validateVar($_POST['suggestionUserName']) && $base->validateVar($_POST['suggestionTimeStamp']) ){
						if($base->validateVar($_POST['QID']) && $base->validateVar($_POST['string']) ){
							if($base->isLoggedIn()){
								if($_SESSION['isReviewer']){
									if(Answer::addAnswer($_POST['QID'], $_POST['string'])){
										$result = json_encode(array('head' => array('status' => 200, 'message'=>''), 'body' => '') );
									}else{
										$result = json_encode( array('head' => array('status' => 304, 'message'=>'Unable to add the answer'), 'body' => '') );
									}
								}else{
									$result = json_encode( array('head' => array('status' => 409, 'message'=>'Not have rights'), 'body' => '') );
								}
							}else{
								$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
							}
						}else{
							$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
						}
					}else{
						$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
					}
					break;
				case 'comment':
					break;
				default:
					$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call for this url'), 'body' => '') );
					break;	
			}
			break;

		case 'submitReviewedQuestion':
			if(sizeof($_POST) == 4){
				require_once __DIR__.'/includes/question.php';
				//if( $base->validateVar($_POST['QID']) && $base->validateVar($_POST['suggestionUserName']) && $base->validateVar($_POST['suggestionTimeStamp']) ){
				if($base->validateVar($_POST['QID']) && $base->validateVar($_POST['tags']) && $base->validateVar($_POST['difficultyLevel']) && $base->validateVar($_POST['string']) ){
					if($base->isLoggedIn()){
						if($_SESSION['isReviewer']){
							if(isset($_SESSION['LAST_ACTIVITY']) && $base->validateVar($_SESSION['LAST_ACTIVITY'])){
								$review = unserialize($_SESSION['locked']);
								$result = null;
								if($review->getQID() == $_POST['QID'] && is_null($review->getSuggestionUserName())){
									$result = Question::reviewQuestion($_POST['QID'], $_POST['string'], $_POST['tags'], $_POST['difficultyLevel']);
									if($result['head']['status'] == 200){
										unset($_SESSION['locked']);
										unset($_SESSION['LAST_ACTIVITY']);
										$result = json_encode(array('head' => array('status' => 200, 'message'=>''), 'body' => '') );
									}else{
										$result = json_encode($result);
									}
								}else{
									$result = json_encode( array('head' => array('status' => 403, 'message'=>'Lock a different review'), 'body' => '') );	
								}
							}else{
								$result = json_encode( array('head' => array('status' => 406, 'message'=>'Lock not acquired'), 'body' => '') );
							}
						}else{
							$result = json_encode( array('head' => array('status' => 409, 'message'=>'Not have rights'), 'body' => '') );
						}
					}else{
						$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
					}
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
			}
			break;

		case 'question':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				if(intval($regMatches[1][1]) > 0){
					require_once __DIR__.'/includes/question.php';
					$id = $regMatches[1][1];
						$result = new Question($id);
						$result = json_encode($result->toArray());
				}
				elseif($base->isLoggedIn()){
					switch($regMatches[1][1]){
						case 'post':
							if(sizeof($_POST) == 3){
								require_once __DIR__.'/includes/question.php';
								require_once __DIR__.'/includes/user.php';
								$assignQID = Question::generateQID();
								$uname = unserialize($_SESSION['user']);
								$user = $uname->getUsername();
								$tags = $_POST['tags'];
								$insertStatus = Question::addQuestion(array(
									'assignQID' => $assignQID, 
									'newString' => $_POST['questionString'],
									'difficultyLevel' => $_POST['difficultyLevel'], 
									'user' => $user));
								$count = 0;
								$objects = array();
								foreach ($tags as $key => $value) {
									$check = Question::findTags($value);
									if($check == 'match'){
										$tagAdded = Question::addQuestionTags($value, $assignQID);
										if(is_null($tagAdded)){
											$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error - Tag not added '), 'body' => '') );
										}
									}
									else{
										array_push($objects, $value);
										$count++;
									}
								}
								if ($count == 0) {
									if ($insertStatus) {
										$result = json_encode( array('head' => array('status' => 200, 'message'=>'Question Added Successfully'), 'body' => '') );
									}
									else{
										$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error - No Insert Status'), 'body' => '') );
									}
								}
								else{
									Question::removeQuestionTags($assignQID);
									Question::removeQuestion($assignQID);
									$result = json_encode( array('head' => array('status' => 409, 'message'=>'Tags does not exist. Unable to add question. Use existing tag or First define a tag then use it.'), 'body' => $objects) );
								}
							}
							else{
								$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 3'), 'body' => '') );
							}
							break;

						case 'qcomment':
							if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) ){
								switch ($regMatches[1][2]) {
									case 'post':
										if(sizeof($_POST) == 2){
											require_once __DIR__.'/includes/questionComment.php';
											require_once __DIR__.'/includes/user.php';
											$uname = unserialize($_SESSION['user']);
											$user = $uname->getUsername();
											$insertStatus = QuestionComment::addComment(array(
												'QID' => $_POST['id'],
												'user' => $user, 
												'string' => $_POST['string']));
											if ($insertStatus) {
												$result = json_encode( array('head' => array('status' => 200, 'message'=>'Comment Added Successfully'), 'body' => '') );
											}
											else{
												$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
											}
										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
										}
										break;
									
									case 'vote':
										if(sizeof($_GET) == 4){
											require_once __DIR__.'/includes/questionComment.php';
											require_once __DIR__.'/includes/user.php';
											$uname = unserialize($_SESSION['user']);
											$user = $uname->getUsername();
											$voteStatus = QuestionComment::checkAlreadyVoted(array(
												'QID' => $_GET['qid'],
												'userName' => $user,
												'commentUserName' => $_GET['commentUserName'],
												'commentTimeStamp' => $_GET['commentTimeStamp']));
											if($voteStatus == $user){
												$voteNature = QuestionComment::checkVoteNature(array(
												'QID' => $_GET['qid'],
												'userName' => $user,
												'commentUserName' => $_GET['commentUserName'],
												'commentTimeStamp' => $_GET['commentTimeStamp']));
												if ($voteNature == $_GET['nature']) {
													$result = json_encode( array('head' => array('status' => 304, 'message'=>'Not Modified'), 'body' => '') );
												}
												else{
													$updateStatus = QuestionComment::updateVote($_GET['nature'], array(
													'QID' => $_GET['qid'],
													'userName' => $user,
													'commentUserName' => $_GET['commentUserName'],
													'commentTimeStamp' => $_GET['commentTimeStamp']));
													if($updateStatus){
														$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Modified Successfully'), 'body' => '') );
													}
													else{
														$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
													}
												}
											}
											else{
												$voteStatus = QuestionComment::addVote(array(
													'userName' => $user,
													'QID' => $_GET['qid'],
													'nature' => $_GET['nature'],
													'commentUserName' => $_GET['commentUserName'],
													'commentTimeStamp' => $_GET['commentTimeStamp']));
												if($voteStatus){
													$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
												}
												else{
													$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
												}
											}

										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 4'), 'body' => '') );
										}
										break;

									case 'modify':
										if(sizeof($_POST) == 3){
											require_once __DIR__.'/includes/questionComment.php';
											require_once __DIR__.'/includes/user.php';
											$uname = unserialize($_SESSION['user']);
											$user = $uname->getUsername();
											$checkUser = QuestionComment::checkCommentUser(array(
												'QID' => $_POST['qid'],
												'userName' => $user,
												'timeStamp' => $_POST['timeStamp']));
											if ($checkUser) {
												$status = QuestionComment::modifyComment($_POST['modificationString'], array(
													'QID' => $_POST['qid'],
													'userName' => $user,
													'timeStamp' => $_POST['timeStamp']));
												if($status){
													$result = json_encode( array('head' => array('status' => 200, 'message'=>'Comment Modified Successfully'), 'body' => '') );
												}
												else{
													$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
												}
											}
											else{
												$result = json_encode( array('head' => array('status' => 405, 'message'=>'User -> '.$user.' cannot modify other users comment' ), 'body' => '') );
											}
										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 3'), 'body' => '') );
										}
										break;

									default:
										$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call for this url'), 'body' => '') );
										break;	
								}
							}
							break;
						case 'vote':
							if(sizeof($_GET) == 2){
								require_once __DIR__.'/includes/question.php';
								require_once __DIR__.'/includes/user.php';
								$uname = unserialize($_SESSION['user']);
								$user = $uname->getUsername();
								$voteStatus = Question::checkAlreadyVoted($_GET['QID'], $user);
								if ($voteStatus == $user) {
									$voteNature = Question::checkVoteNature($_GET['QID'], $user);
									if ($voteNature == $_GET['nature']) {
										$result = json_encode( array('head' => array('status' => 304, 'message'=>'Vote Not Modified'), 'body' => '') );
									}
									else{
										$updateStatus = Question::updateVote($_GET['QID'], $user, $_GET['nature']);
										if($updateStatus){
											$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Modified Successfully'), 'body' => '') );
										}
										else{
											$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
										}
									}
								}
								else{
									$voteStatus = Question::addVote($_GET['QID'], $user, $_GET['nature']);
									if($voteStatus){
										$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
									}
									else{
										$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
									}
								}

							}
							else{
								$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 2'), 'body' => '') );
							}
							break;

						case 'suggestion':
							if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) ){
								switch ($regMatches[1][2]){
									case 'post':
										if(sizeof($_POST) == 2){
											require_once __DIR__.'/includes/suggestion.php';
											require_once __DIR__.'/includes/user.php';
											$uname = unserialize($_SESSION['user']);
											$user = $uname->getUsername();
											$status = Suggestion::addSuggestion($_POST['QID'], $user, $_POST['suggestionString']);
											if($status){
												$result = json_encode( array('head' => array('status' => 200, 'message'=>'Suggestion Added Successfully'), 'body' => '') );
											}
											else{
												$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
											}
										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
										}
										break;

									case 'vote':
										if (sizeof($_GET == 4)) {
											require_once __DIR__.'/includes/suggestion.php';
											require_once __DIR__.'/includes/user.php';
											$uname = unserialize($_SESSION['user']);
											$user = $uname->getUsername();
											$voteStatus = Suggestion::checkAlreadyVoted(array(
												'QID' => $_GET['QID'],
												'suggestionUserName' => $_GET['suggestionUserName'],
												'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
												'userName' => $user));
											if ($voteStatus == $user) {
												$voteNature = Suggestion::checkVoteNature(array(
													'QID' => $_GET['QID'],
													'suggestionUserName' => $_GET['suggestionUserName'],
													'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
													'userName' => $user));
												if ($voteNature == $_GET['nature']) {
													$result = json_encode( array('head' => array('status' => 304, 'message'=>'Vote Not Modified'), 'body' => '') );
												}
												else{
													$updateStatus = Suggestion::updateVote($_GET['nature'], array(
														'QID' => $_GET['QID'],
														'suggestionUserName' => $_GET['suggestionUserName'],
														'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
														'userName' => $user));
													if($updateStatus){
														$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Modified Successfully'), 'body' => '') );
													}
													else{
														$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
													}
												}
											}
											else{
												$voteStatus = Suggestion::addVote(array(
													'QID' => $_GET['QID'],
													'suggestionUserName' => $_GET['suggestionUserName'],
													'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
													'userName' => $user,
													'nature' => $_GET['nature']));
												if($voteStatus){
													$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
												}
												else{
													$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
												}
											}
										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 4'), 'body' => '') );
										}
										break;

									case 'scomment':
										if( isset($regMatches[1][3]) && ( !empty($regMatches[1][3]) ) ){
											switch ($regMatches[1][3]){
												case 'post':
													if(sizeof($_POST) == 4){
														require_once __DIR__.'/includes/suggestionComment.php';
														require_once __DIR__.'/includes/user.php';
														$uname = unserialize($_SESSION['user']);
														$user = $uname->getUsername();
														$insertStatus = SuggestionComment::addComment(array(
															'QID' => $_POST['QID'],
															'suggestionUserName' => $_POST['suggestionUserName'],
															'suggestionTimeStamp' => $_POST['suggestionTimeStamp'],
															'user' => $user, 
															'commentString' => $_POST['commentString']));
														if ($insertStatus) {
															$result = json_encode( array('head' => array('status' => 200, 'message'=>'Comment Added Successfully'), 'body' => '') );
														}
														else{
															$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
														}
													}
													else{
														$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
													}
													break;

												case 'vote':
													if (sizeof($_GET) == 6) {
														require_once __DIR__.'/includes/suggestionComment.php';
														require_once __DIR__.'/includes/user.php';
														$uname = unserialize($_SESSION['user']);
														$user = $uname->getUsername();
														$voteStatus = SuggestionComment::checkAlreadyVoted(array(
															'QID' => $_GET['QID'],
															'suggestionUserName' => $_GET['suggestionUserName'],
															'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
															'suggestionCommmentUserName' => $_GET['suggestionCommmentUserName'],
															'suggestionCommmentTimeStamp' => $_GET['suggestionCommmentTimeStamp'],
															'userName' => $user));
														if ($voteStatus == $user) {
															$voteNature = SuggestionComment::checkVoteNature(array(
																'QID' => $_GET['QID'],
																'suggestionUserName' => $_GET['suggestionUserName'],
																'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
																'suggestionCommmentUserName' => $_GET['suggestionCommmentUserName'],
																'suggestionCommmentTimeStamp' => $_GET['suggestionCommmentTimeStamp'],
																'userName' => $user));
															if ($voteNature == $_GET['nature']) {
																$result = json_encode( array('head' => array('status' => 304, 'message'=>'Vote Not Modified'), 'body' => '') );
															}
															else{
																$updateStatus = SuggestionComment::updateVote($_GET['nature'], array(
																'QID' => $_GET['QID'],
																'suggestionUserName' => $_GET['suggestionUserName'],
																'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
																'suggestionCommmentUserName' => $_GET['suggestionCommmentUserName'],
																'suggestionCommmentTimeStamp' => $_GET['suggestionCommmentTimeStamp'],
																'userName' => '$user'));
																if($updateStatus){
																	$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Modified Successfully'), 'body' => '') );
																}
																else{
																	$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
																}
															}
														}
														else{
															$voteStatus = SuggestionComment::addVote(array(
																'QID' => $_GET['QID'],
																'suggestionUserName' => $_GET['suggestionUserName'],
																'suggestionTimeStamp' => $_GET['suggestionTimeStamp'],
																'suggestionCommmentUserName' => $_GET['suggestionCommmentUserName'],
																'suggestionCommmentTimeStamp' => $_GET['suggestionCommmentTimeStamp'],
																'userName' => $user,
																'nature' => $_GET['nature']));
															if($voteStatus){
																$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
															}
															else{
																$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
															}
														}
													}
													else{
														$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 6'), 'body' => '') );
													}
													break;

												case 'modify':
													if(sizeof($_POST) == 5){
														require_once __DIR__.'/includes/suggestionComment.php';
														require_once __DIR__.'/includes/user.php';
														$uname = unserialize($_SESSION['user']);
														$user = $uname->getUsername();
														$checkUser = SuggestionComment::checkCommentUser(array(
														'QID' => $_POST['QID'],
														'suggestionUserName' => $_POST['suggestionUserName'],
														'suggestionTimeStamp' => $_POST['suggestionTimeStamp'],
														'userName' => $user,
														'timeStamp' => $_POST['timeStamp']));
														if ($checkUser) {
															$status = SuggestionComment::modifyComment($_POST['modificationString'], array(
																'QID' => $_POST['QID'],
																'suggestionUserName' => $_POST['suggestionUserName'],
																'suggestionTimeStamp' => $_POST['suggestionTimeStamp'],
																'userName' => $user,
																'timeStamp' => $_POST['timeStamp']));
															if($status){
																$result = json_encode( array('head' => array('status' => 200, 'message'=>'Comment Modified Successfully'), 'body' => '') );
															}
															else{
																$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
															}
														}
														else{
															$result = json_encode( array('head' => array('status' => 405, 'message'=>'User -> '.$user.' cannot modify other users comment' ), 'body' => '') );
														}
													}
													else{
														$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 5'), 'body' => '') );
													}
													break;

												default:
													$result = json_encode( array('head' => array('status' => 400, 'message'=>'saaaaa  No such call'), 'body' => '') );
													break;
											}
										}
										break;

									default:
										$result = json_encode( array('head' => array('status' => 400, 'message'=>'sdhjskd No such call'), 'body' => '') );
										break;
								}
							}
							break;
						
						default:
							$result = json_encode( array('head' => array('status' => 400, 'message'=>'Hello No such call'), 'body' => '') );
							break;

					}
					break;
				}
				else
				{
					$result = json_encode( array('head' => array('status' => 401, 'message'=>'Not Logged In'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
			}
			break;	

		default:
			$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call'), 'body' => '') );
			break;			
	}
	exit($result);
}
else{
	exit( json_encode(array('head' => array('status' => 404, 'message'=>''), 'body' => '')) );
}
?>