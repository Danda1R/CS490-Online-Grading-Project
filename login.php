<?php

    //Rishik Danda - Alpha Middle_end

    $login_json = file_get_contents('php://input');
    $response = json_decode($login_json, true);

    $user = $response['user'];
    $pass = $response['pass'];

    $login_role=curl_backend($user,md5($pass));
    
    echo json_encode($login_role);

    function curl_backend($user,$pass){
        $login = array('user' => $user,'pass' =>$pass);
        $url = "https://afsaccess4.njit.edu/~iac22/login.php";
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($login));
        $response = curl_exec($curl);
        curl_close ($curl);

	    return $response;
    }
?>
