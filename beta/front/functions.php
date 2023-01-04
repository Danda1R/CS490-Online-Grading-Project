<?php

function redirect($dest) {
    echo "<script>window.location = \"https://afsaccess4.njit.edu/~jcb62/beta/" . $dest . "\";</script>";  
    die();
}

function curlRequest($postData) {
    $url = "https://afsaccess4.njit.edu/~rrd42/beta/login.php";
    $dataToJson = json_encode($postData);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToJson);

    $response = curl_exec($ch);
    curl_close($ch);
        
    $jsonToData = json_decode($response);

    return $jsonToData;
}

?>