<?php

	require_once($_SERVER['DOCUMENT_ROOT'] . "/../includes/getLogin.php");
	
	// Check Permissions records for user
	$userInfo = $_SESSION['userInfo'];

	if (!isset($userInfo['userPermissions']['tools']['ADMINSUPERVISOR']) || $getGUID == "") {
		$webpage = $_SERVER['PHP_SELF'];
		$hostName = $_SERVER['HTTP_HOST']; 			
		echo "<html>\n";
		echo "	<head>\n";
		echo "		<title>Error!</title>\n";
		echo "	</head>\n";
		echo "	<body>\n";
		echo "		<h3>Error!</h3>\n";
		echo "		instance: $databaseName<br />\n";				
		echo "		login_id:  $loginId<br />\n";
		echo "		permissions required: ADMINSUPERVISOR: [ADD], [EDIT], [ALL], [SUPER]<br />\n";
		echo "		module:  $webpage<br />\n";
		echo "		<pre>\n";
		if (isset($userInfo['userPermissions']['tools'])) {
			echo "		<pre>\n";
			var_dump($userInfo['userPermissions']['tools']);
			echo "		</pre>\n";
		} else {
			echo "No user permissions were loaded.<br />";
		}
		echo "		You may click <a href='http://$hostName/db_selector/'>HERE</a> to switch Databases if necessary.\n";
		echo "	</body>\n";
		echo "</html>\n";
		exit;
	}
	
	if (isset($_POST['supervisorGUID'])) {
		$supervisorGUID = $_POST['supervisorGUID'];
		if ($supervisorGUID != "") {
			$supervisorGUID=" OR lookupSupervisorInfo.GUID='" . $supervisorGUID . "'";
		} else {
			$supervisorGUID = "";
		}
	} else {
		$supervisorGUID="";
	}

	// Get list of active supervisors from lookupSupervisorInfo, generate JSON with GUID and LastName
	$sql = "SELECT * FROM lookupSupervisorInfo LEFT JOIN lookupPermissions ON lookupSupervisorInfo.GUID=lookupPermissions.GUID WHERE lookupPermissions.GUID IS NOT NULL ORDER BY lastName, firstName ASC";
	$results = $Database->Query($sql);

	while ($myrow = odbc_fetch_array($results)) {
		$guid = $myrow['GUID'];
		$firstName = $myrow['firstName'];
		$lastName = $myrow['lastName'];
		$attId = $myrow['attId'];
		$fullName = $lastName . ", " . $firstName;
		$displayName = $fullName . " [" . $attId . "]";
		$supervisorStatus = $myrow['supervisorStatus'];
		$selSupervisorId[] = array(
			'GUID' => $guid,
			'fullName' => $fullName,
			'attId' => $attId,
			'displayName' => $displayName,
			'supervisorStatus' => $supervisorStatus
		);
	}
	echo json_encode($selSupervisorId);

?>