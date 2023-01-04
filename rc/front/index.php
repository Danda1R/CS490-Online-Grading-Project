<?php
require(__DIR__ . "/nav.php");
session_start();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
<div class="container-fluid h-100 d-flex justify-content-center align-items-center">
    <form class="shadow p-5 mb-4 bg-body rounded" method="POST">
        <h1 class="mt-2 mb-3" style="text-align:center;">Login</h1>
        <div class="mb-3">
            <label class="form-label" for="user">Username</label>
            <input class="form-control" name="user" type="text">
        </div>
        <div class="mb-3">
            <label class="form-label" for="pass">Password</label>
            <input class="form-control" name="pass" type="password">
        </div>
        <div class="text-center">
            <button class="btn btn-primary" name="submit" type="submit">Login</button>
        </div>
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
        $data = array("requestType" => "login", "user" => $user, "pass" => $pass);
        $response = curlRequest($data);

        if ($response == "prof") {
            $_SESSION["role"] = "teacher";
            $_SESSION["user"] = $user;
            redirect("teacher/teacher.php");
        }
        elseif ($response == "student") {
            $_SESSION["role"] = "student";
            $_SESSION["user"] = $user;
            redirect("student/student.php");
        }
        else {
            print_r($response);
        }
    }
}
?>

