<?

require_once './initialize_database.php';
class Login
{ 
public function getLogin()
{
    return $this->Login;
}

public function setLogin($Login)
{
    $this->Login = $Login;
    return $this;
}
