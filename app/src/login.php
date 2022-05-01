<?php

function write_to_console($data) {
 $console = $data;
 if (is_array($console))
 $console = implode(',', $console);

 echo "<script>console.log('Console: " . $console . "' );</script>";
}

function verify_input($inputVal){
	$allowedCharacters = "@."; // list of allowed characters that are not numbers or alphabet characters
	for ($x = 0; $x < strlen($inputVal); $x++) {
		$passed = false;
		if(chr(47) < $inputVal[$x] and $inputVal[$x] < chr(58)){ // checks if number
			$passed = true;
		}
		if(chr(64) < $inputVal[$x] and $inputVal[$x] < chr(91)){ // checks if lower case
			$passed = true;
		}
		if(chr(96) < $inputVal[$x] and $inputVal[$x] < chr(123)){ // checks if lower case
			$passed = true;
		}
		
		for($y = 0; $y < strlen($allowedCharacters); $y++)
		{
			if($inputVal[$x] == $allowedCharacters[$y])
			{
				$passed = true;
			}
		}
		if($passed == false) // if there is not a passing check fail it
		{
			return false;
		}
	}
	return true;
}

try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    /*Get information from the post request*/
    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];
	//write_to_console($myusername[0]);
	print($myusername[0]);
	//write_to_console("Swag");
    //convert password to 80 byte hash using ripemd256 before comparing
    $hashpassword = hash('ripemd256', $mypassword);

    if($myusername==null)
    {throw new Exception("input did not exist");}


    $myusername = strtolower($myusername); //makes username noncase-sensitive
    global $acctype;

	// new input check code
	$isvalid = verify_input($myusername);
	$results = false; // makes it fail if not valid
	if($isvalid == true) // only run query if valid
	{
		//query for count
		$query = "SELECT COUNT(*) as count FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
		$count = $db->querySingle($query);

		//query for the row(s)
		$query = "SELECT * FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
		$results = $db->query($query);
	}

    
	
	
	
    if ($results !== false) //query failed check
    {
        if (($userinfo = $results->fetchArray()) !== (null || false)) //checks if rows exist
        {
            // users or user found
            $error = false;

            $acctype = $userinfo[2];
        } else {
            // user was not found
            $error = true;

        }
    } else {
        //query failed
        $error = true;

    }

    //determine if an account that met the credentials was found
    if ($count >= 1 && !$error) {
        //login success

        if (isset($_SESSION)) {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        } else {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        }
        //redirect
        header("Location: ../public/dashboard.php");
    } else {
        //login fail
        header("Location: ../public/index.php?login=fail");
    }
//note: since the database is not changed, it is not backed up
}
catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
    var_dump($e->getTraceAsString());
    echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}




