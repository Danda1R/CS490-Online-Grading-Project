<?php
require(__DIR__ . "/../nav.php");

if ($role != "teacher") {
    redirect("");
}

if (!isset($_GET["student"])) {
    redirect("teacher/select_student.php");  
}

$data = array("requestType" => "showAutoGrade", "student" => $_GET["student"], "examName" => $_GET["examName"]);
$response = curlRequest($data);
$totalScore = $response[0]->totalScore;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1>Modify Grade</h1>
    <h2><?php echo $_GET["examName"]; ?></h2>
    <h3><?php echo $_GET["student"]; ?></h3>
    <h3><?php echo $totalScore; ?>/100</h3>
    <form method="POST">
        <?php foreach ($response as $questions => $values) : ?>
            <div class="mb-5">
                <p>Question: <?php echo $values->question; ?></p>
                <p>Student Function:</p>
                <pre><?php echo $values->answer; ?></pre>
                <p>Score: <?php echo $values->score; ?>/<?php echo $values->totalPoints; ?></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Test Case</th>
                            <th scope="col">Answer</th>
                            <th scope="col">Student Answer</th>  
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row"><?php echo $values->testcase1; ?></td>
                            <td><?php echo $values->testcase1Ans; ?></td>
                            <td><?php echo $values->studentAns1; ?></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php echo $values->testcase2; ?></td>
                            <td><?php echo $values->testcase2Ans; ?></td>
                            <td><?php echo $values->studentAns2; ?></td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <label for="comment">Comment</label>
                    <textarea class="form-control" name="comments[]"></textarea>
                </div>
                <div class="d-flex">
                    <h2>Score:</h2>
                    <input class="form-control" name="scores[]" type="text" placeholder="0" style="width:50px;">
                    <h2>/<?php echo $values->totalPoints; ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="btn btn-primary" name="submit" type="submit">Submit</button>
    </form>
</div>


<?php
if (isset($_POST["scores"])) {
    $scores = $_POST["scores"];

    for($i = 0; $i < count($scores); $i++) {
        if (empty($scores[$i])) {
            $sendScores[] = $response[$i]->score;
        }
        else {
            $sendScores[] = $scores[$i];
        }
    }
}

if (isset($_POST["comments"])) {
    $comments = $_POST["comments"];
    
    for($i = 0; $i < count($comments); $i++) {
        if (empty($comments[$i])) {
            $sendComments[] = "no comments";
        }
        else {
            $sendComments[] = $comments[$i];
        }
    }

    $data2 = array("requestType" => "saveResultsFinal", "examName" => $_GET["examName"], "student" => $_GET["student"], "scores" => $sendScores, "comments" => $sendComments);
    $response2 = curlRequest($data2);

    redirect("teacher/grade_tests.php");
}

?>
