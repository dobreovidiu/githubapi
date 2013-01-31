<?php

	// Management of GitHub users.
	
	
	// configuration
	$secret			= "4f7343dfad4w43f4343r434d7e34";			// application secret
	$adminUsername	= "ovidiutesthub";							// GitHub administrator account
	$adminPassword	= "Password1";								// GitHub administrator password
	$team			= "327325";									// GitHub team
	
	
	// globals
	$userSecret			= "";
	$gitUsername		= "";
	$addUserPerformed	= false;	
	$addUserResult		= false;
	$addUserMessage		= "";
	$userAgent			= "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1";
	
	
	
	// authenticate
	function authenticate()
	{
		global $userAgent;
		global $adminUsername;
		global $adminPassword;			
		
		// allocate curl
		$curl = curl_init( "https://api.github.com" );
		
		// initialize curl
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 		1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 		1);
		curl_setopt($curl, CURLOPT_USERPWD, 			$adminUsername . ":" . $adminPassword );
		curl_setopt($curl, CURLOPT_USERAGENT, 			$userAgent );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 		0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 		0);
			
		// get data
		$page = curl_exec($curl);
		
		// get HTTP code
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// close curl
		curl_close($curl);	
		
		//echo "AUTH RESULT: " . $page . "<br><br>AUTH HTTP CODE: " . $httpCode . "<br><br>";
	
		// must be 200
		$httpCode = intval( $httpCode );
		if( $httpCode == 200 )
			return true;
			
		return false;
	}	
	
	
	// add user
	function addUser( $team, $username )
	{
		global $userAgent;
		global $adminUsername;
		global $adminPassword;			
		
		// set request
		$url = "https://api.github.com/teams/" . $team . "/members/" . $username;
		
		// allocate curl
		$curl = curl_init( $url );
		
		// initialize curl
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 		1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 		1);
		curl_setopt($curl, CURLOPT_USERAGENT, 			$userAgent );
		curl_setopt($curl, CURLOPT_USERPWD, 			$adminUsername . ":" . $adminPassword );		
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 		"PUT");		
		curl_setopt($curl, CURLOPT_POST, 				1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 			"");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 		0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 		0);
			
		// get data
		$page = curl_exec($curl);
		
		// get HTTP code
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// close curl
		curl_close($curl);	
		
		//echo "ADD RESULT: " . $page . "<br><br>ADD HTTP CODE: " . $httpCode . "<br><br>";
	
		// must be 204
		$httpCode = intval( $httpCode );		
		if( $httpCode == 204 )
			return true;
			
		return false;
	}
	
	
	// process request
	function processRequest()
	{
		global $addUserPerformed;
		global $addUserResult;
		global $addUserMessage;
		global $gitUsername;
		global $team;
		
		// set performed
		$addUserPerformed = true;
		
		// authenticate
		if( !authenticate() )
		{
			$addUserMessage = "Failed to authenticate administrator on GitHub";
			return;
		}
			
		// add user
		if( addUser( $team, $gitUsername ) )
		{
			$addUserResult = true;
			$addUserMessage = "User " . $gitUsername . " added succesfully to repository team";
		}
		else
		{
			$addUserResult = false;
			$addUserMessage = "Failed to add user " . $gitUsername . " to repository team";		
		}		
	}
	

	// main function
	function main()
	{
		global $secret;
		global $userSecret;
		global $gitUsername;
		
		// get param
		if( isset( $_REQUEST["secret"] ) )
			$userSecret = $_REQUEST["secret"];
			
		if( isset( $_REQUEST["gitUsername"] ) )
			$gitUsername = $_REQUEST["gitUsername"];		
	
		// different secret
		if( $userSecret != $secret )
			die();		

		// process request
		if( $gitUsername != "" )
			processRequest();		
	}
		
	
	// main function
	main();
	
?>

<!-- Page Layout -->

<html>
<head>
	<title>GitHub Repository Access</title>
</head>

<body>

	<h2>GitHub Repository Access</h2>
	
	<?php 

		// operation status
		if( $addUserPerformed )
		{
			if( $addUserResult )
				echo "<p style='color: green'><b>" . $addUserMessage . "</b></p>";
			else
				echo "<p style='color: red'><b>" . $addUserMessage . "</b></p>";
		}		
	
	?>

	<form name="inputForm" action="add_collaborator.php?secret=<?php echo $userSecret; ?>" method="POST">
		
		Please write your GitHub username to get access to the code repository
		<br><br>
		<input type="text" size="32" id="gitUsername" name="gitUsername" value="<?php echo $gitUsername; ?>" />
		
		<br><br>
		<input type="submit" value="Submit" />
		
	</form>
	
</body>
</html>