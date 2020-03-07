<?php
	// New php session management code -- KB4FXC 01/25/2018
	session_start([ 'cookie_lifetime' => 604800, 'name' => "allmon2" ]);
	if (!array_key_exists("loggedin", $_SESSION))
		     $_SESSION["loggedin"] = false;
?>
