<?

require_once __DIR__.'/includes/initialize_database.php';

define('PATH_REGEX_PATTERN',"!\/([^\/]+)!");
define('PATH',$_SERVER['PATH_INFO']);

preg_match_all(PATH_REGEX_PATTERN, PATH, $regMatches);

$result = '';

if( isset($regMatches[1][0]) && ( !empty($regMatches[1][0]) ) ){
	switch ($regMatches[1][0]) {
		case 'register':
			if(sizeof($_POST) == 18){
				require_once __DIR__.'/includes/register.php';
				$register = new Register($_POST['userName'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phone'], $_POST['dob'], $_POST['gender'], $_POST['country'], $_POST['city'], $_POST['state'], $_POST['qualification'], $_POST['affiliation'], $_POST['areasOfExpertise'], $_POST['securityQuestionID'], $_POST['securityAnswer'], $_POST['reviewerKey'], $_POST['interests']);
				$result = $register->register();
			}else{
				$result = json_encode( array('head' => array('status' => 206, 'message'=>'Only '.sizeof($_POST).' fields received, required 18'), 'body' => '') );
			}
			break;
		
		case 'question':
			break;
		
		default:
			break;
	}
	exit($result);
}else{
	exit( json_encode(array('head' => array('status' => 404, 'message'=>''), 'body' => '')) );
}
?>