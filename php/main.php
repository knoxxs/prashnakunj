<?php

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

		case 'questions':
			if(sizeof($_GET) == 4){
				require_once __DIR__.'/includes/Question.php';
				if( $base->validateVar($_GET['type']) && $base->validateVar($_GET['number']) && $base->validateVar($_GET['latestQuestionTime']) && $base->validateVar($_GET['scroll']) ){
					$result = Question::getQuestions($_GET['type'], $_GET['number'], $_GET['latestQuestionTime'], $_GET['scroll']);
				}else{
					$result = json_encode( array('head' => array('status' => 206, 'message'=>'Incomplete field'), 'body' => '') );
				}
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_GET).' fields received, required 4'), 'body' => '') );
			}
			break;

		case 'question':
			if( isset($regMatches[1][1]) && ( !empty($regMatches[1][1]) ) ){
				if($base->isLoggedIn()){
					switch($regMatches[1][1]){
						case 'post':
							if(sizeof($_POST) == 3){
								require_once __DIR__.'/includes/Question.php';
								require_once __DIR__.'/includes/user.php';
								$assignQID = Question::generateQID();
								$user = unserialize($_SESSION['user']);
								$insertStatus = Question::addQuestion(array(
									'assignQID' => '$assignQID', 
									'newString' => '$_POST['question']['string']',
									'difficultyLevel' => '$_POST['question']['difficultyLevel']', 
									'user' => '$user'));

								$tags = $_POST['question']['tags'];
								foreach ($tags as $key => $value) {
									$check = Question::findTags($value);
									if($check){
										$tagAdded = Question::addQuestionTags($value, $assignQID);
										if($tagAdded == FALSE){
											$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
											break;
										}
									}
									else{
										$result = json_encode( array('head' => array('status' => 404, 'message'=>'Tag '.$value.' does not exist'), 'body' => '') );
										break;
									}
								}
								if ($insertStatus) {
									$result = json_encode( array('head' => array('status' => 200, 'message'=>'Question Added Successfully'), 'body' => '') );
								}
								else{
									$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
								}
								// ADD tags to encompass table for the question added
							}
							else{
								$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 3'), 'body' => '') );
							}

						case 'qomment':
							if( isset($regMatches[1][2]) && ( !empty($regMatches[1][2]) ) ){
								switch ($regMatches[1][2]) {
									case 'post':
										if(sizeof($_POST) == 2){
											require_once __DIR__.'/includes/QuestionComment.php';
											require_once __DIR__.'/includes/user.php';
											$user = unserialize($_SESSION['user']);
											$insertStatus = QuestionComment::addComment(array(
												'QID' => '$_POST['id']',
												'user' => '$user', 
												'string' => '$_POST['string']'));
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
											require_once __DIR__.'/includes/QuestionComment.php';
											require_once __DIR__.'/includes/user.php';
											$user = unserialize($_SESSION['user']);
											$voteStatus = QuestionComment::checkAlreadyVoted(array(
												'QID' => '$_GET['qid']',
												'userName' => '$user',
												'commentUserName' => '$_GET['commentUserName']',
												'commentTimeStamp' => '$_GET['commentTimeStamp']'));
											if($voteStatus == $user){
												$voteNature = QuestionComment::checkVoteNature(array(
												'QID' => '$_GET['qid']',
												'userName' => '$user',
												'commentUserName' => '$_GET['commentUserName']',
												'commentTimeStamp' => '$_GET['commentTimeStamp']'));
												if ($voteNature == $_GET['nature']) {
													$result = json_encode( array('head' => array('status' => 304, 'message'=>'Not Modified'), 'body' => '') );
												}
												else{
													$updateStatus = QuestionComment::updateVote($_GET['nature'], array(
													'QID' => '$_GET['qid']',
													'userName' => '$user',
													'commentUserName' => '$_GET['commentUserName']',
													'commentTimeStamp' => '$_GET['commentTimeStamp']'));
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
													'userName' => '$user',
													'QID' => '$_GET['qid']',
													'nature' => '$_GET['nature']',
													'commentUserName' => '$_GET['commentUserName']',
													'commentTimeStamp' => '$_GET['commentTimeStamp']'));
												if($voteStatus){
													$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
												}
												else{
													$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
												}
											}

										}
										else{
											$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 4'), 'body' => '') );
										}
										break;

									case 'modify':
										if(sizeof($_POST) == 3){
											require_once __DIR__.'/includes/QuestionComment.php';
											require_once __DIR__.'/includes/user.php';
											$user = unserialize($_SESSION['user']);
											$checkUser = QuestionComment::checkCommentUser(array(
												'QID' => '$_GET['qid']',
												'userName' => '$user',
												'timeStamp' => '$_GET['timeStamp']'));
											if ($checkUser) {
												$status = QuestionComment::modifyComment($_POST['modificationString'], array(
													'QID' => '$_GET['qid']',
													'userName' => '$user',
													'timeStamp' => '$_GET['timeStamp']'));
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
								require_once __DIR__.'/includes/Question.php';
								require_once __DIR__.'/includes/user.php';
								$user = unserialize($_SESSION['user']);
								$voteStatus = Question::checkAlreadyVoted($_GET['QID'], $user);
								if ($voteStatus == $user) {
									$voteNature = Question::checkVoteNature($_GET['QID'], $user);
									if ($voteNature == $_GET['nature']) {
										$result = json_encode( array('head' => array('status' => 304, 'message'=>'Vote Not Modified'), 'body' => '') );
									}
									else{
										$updateStatus = Question::updateVote($_GET['QID'], $user, $_GET['nature'])
										if($updateStatus){
											$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Modified Successfully'), 'body' => '') );
										}
										else{
											$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
										}
									}
								}
								else{
									$voteStatus = Question::addVote($_GET['QID'], $user, $_GET['nature'])
									if($voteStatus){
										$result = json_encode( array('head' => array('status' => 200, 'message'=>'Vote Added Successfully'), 'body' => '') );
									}
									else{
										$result = json_encode( array('head' => array('status' => 500, 'message'=>'Internal Error'), 'body' => '') );
									}
								}

							}
							else{
								$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 2'), 'body' => '') );
							}
								break;
								}
							}
							break;
					}				
				}
				break;
//  case question ends here and please make changes for the same.
		default:
			$result = json_encode( array('head' => array('status' => 400, 'message'=>'No such call'), 'body' => '') );
			break;
	}
	exit($result);
}else{
	exit( json_encode(array('head' => array('status' => 404, 'message'=>''), 'body' => '')) );
}
?>