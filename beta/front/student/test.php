<?php
require_once(__DIR__ . "/../functions.php");
session_start();

if ($_SESSION["role"] != "student") {
    redirect("");
}

if (isset($_GET["examName"])) {
    $examName = $_GET["examName"];
}

$data = array("requestType" => "showExamStudent", "examName" => $examName);
$response = curlRequest($data);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container mt-5">
    <h1><?php echo $examName; ?></h1>
    <form method="POST">
        <?php foreach ($response as $questions => $values) : ?>
            <div class="mb-5">
                <p><?php echo $values->question; ?></p>
                <p>Total Points: <?php echo $values->points; ?></p>
                <textarea class="form-control" name="answers[]"></textarea>
            </div>
        <?php endforeach; ?>
        <div>
            <button class="btn btn-primary" name="submit" type="submit">Submit</button>
        </div>
    </form>
</div>

<?php
if (isset($_POST["answers"])) {
    $answers = $_POST["answers"];

    $data2 = array("requestType" => "saveStudentExam", "examName" => $examName, "student" => $_SESSION["user"], "answers" => $answers);
    $response2 = curlRequest($data2);
    redirect("student/student.php");
}
?>


