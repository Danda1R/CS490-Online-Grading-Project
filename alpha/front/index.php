<div class="form-container">
    <form method="POST">
        <div>
            <label for="user">Username</label>
            <input name="user" type="text">
        </div>
        <div>
            <label for="pass">Password</label>
            <input name="pass" type="password">
        </div>
        <input name="submit" type="submit">
    </form>
</div>

<?php

if (isset($_POST["user"]) && isset($_POST["pass"])) {
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    if (empty($user) || empty($pass)) {
        echo "Bad Credentials";
    }
    else {
        $postData = array("user" => $user, "pass" => $pass);
        $encodedData = json_encode($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://afsaccess4.njit.edu/~rrd42/login.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        $response = curl_exec($ch);
        curl_close($ch);
        
	$decode = json_decode($response);

	if ($decode == "\"prof\"") {
		echo "<script>window.location = \"https://afsaccess4.njit.edu/~jcb62/teacher.php\";</script>";
	}
	else if ($decode == "\"student\"") {
		echo "<script>window.location = \"https://afsaccess4.njit.edu/~jcb62/student.php\";</script>";
    }
	else {
		echo "Bad credentials";
	}
}
}
?>