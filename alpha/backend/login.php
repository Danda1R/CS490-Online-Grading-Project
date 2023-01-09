<?php
#Ian Church-Krafte - CS490101 - backend
$post = file_get_contents('php://input');
$json = json_decode($post, true);

$user = $json['user'];
$pass = $json['pass'];

$servername = 'sql1.njit.edu';
$username = 'iac22';
$password = ''; #sql password

$conn = new mysqli($servername, $username, $password, 'iac22');
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
#echo "Connected successfully to mysql";

$result = db_lookup($user, $pass, $conn);
echo json_encode($result);

function db_lookup($user, $pass, $conn){
    $sql = "SELECT role FROM alpha WHERE username='". $user. "' AND password='".$pass."'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $role = $result->fetch_assoc();
        return $role["role"];
    } else {
        return 'denied';
    }
}

?>
