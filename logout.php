<?php
	// New php session management code -- KB4FXC 01/25/2018
	include_once ("session.inc");

	session_unset();
	$_SESSION['loggedin'] = false;

	//if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();

		echo "path = " . $params["path"] . ", domain = " . $params["domain"] . ", secure = " . $params["secure"] . ", httponly = " . $params["httponly"] . "<br><br>\n";

		//setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	//}

	//session_destroy();	// Delete all session info....

?>
