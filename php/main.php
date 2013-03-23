<?

require_once './includes/initialize_database.php';

define('PATH_REGEX_PATTERN',"!\/([^\/]+)!");
define('PATH',$_SERVER['PATH_INFO']);

preg_match_all(PATH_REGEX_PATTERN, PATH, $regMatches);

switch ($regMatches[1][0]) {
	case 'register':
		require_once './includes/register.php';
		break;
	
	case 'question':
		break;
	
	default:
		break;
}
?>