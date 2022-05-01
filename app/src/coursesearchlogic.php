<?php

function verify_input($inputVal){
	if(strlen($inputVal) == 0){
		return true;
	}
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
		
		if($passed == false) // if there is not a passing check fail it
		{
			return false;
		}
	}
	return true;
}

function verify_course($inputVal){
	if(strlen($inputVal) == 0){
		return true;
	}
	
	for ($x = 0; $x < strlen($inputVal); $x++) {
		$passed = false;
		if(chr(47) < $inputVal[$x] and $inputVal[$x] < chr(58)){ // checks if number
			$passed = true;
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

    /*Get information from the search (post) request*/
    $courseid = $_POST['courseid'];
    $coursename = $_POST['coursename'];
    $semester = $_POST['semester'];
    $department = $_POST['department'];


	$course_valid = verify_course($courseid);
	$coursename_valid = verify_input($coursename);
	$semester_valid = verify_input($semester);
	$department_valid = verify_input($department);


	if($course_valid == true and $coursename_valid == true and $semester_valid == true and $department_valid == true){ // if all valid
		//set default values if blank
		if($courseid=="")
		{
			$courseid="defaultvalue!";
		}
		if($coursename=="")
		{
			$coursename="defaultvalue!";
		}
		if($semester=="")
		{
			$semester="defaultvalue!";
		}
		if($department=="")
		{
			$department="defaultvalue!";
		}
		$query = "	SELECT Section.CRN, Course.CourseName, Section.Year, Section.Semester, User.Email, Section.Location
				FROM Section
				CROSS JOIN Course ON Section.Course = Course.Code
				INNER JOIN User ON Section.Instructor = User.UserID
				WHERE (CRN LIKE '$courseid' OR '$courseid'='defaultvalue!') AND
						(Semester LIKE '$semester' OR '$semester'='defaultvalue!') AND
						(Course LIKE '$department' OR '$department'='defaultvalue!') AND
						(CourseName LIKE '$coursename' OR '$coursename' = 'defaultvalue!')";

		$results = $db->query($query);

		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
			$jsonArray[] = $row;
		}

		echo json_encode($jsonArray);
	}else{ // if not valid
		
	}
//note: since no changes happen to the database, it is not backed up on this page
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
?>