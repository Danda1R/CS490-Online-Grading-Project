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
// print_r($response);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <div class="d-flex flex-row justify-content-between my-4">
        <h1><?php echo $_GET["student"]; ?>'s <?php echo $_GET["examName"]; ?></h1>
        <h1><?php echo $totalScore; ?>/100</h1>
    </div>
    <form method="POST">
        <?php foreach ($response as $questions => $values) : ?>
            <div class="mb-5">
                <p><span style="font-weight:bold;">Question:</span><br><?php echo $values->question; ?></p>
                <p style="font-weight:bold;">Student Function:</p>
                <pre class="bg-dark text-white p-4 rounded"><?php echo $values->answer; ?></pre>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Test Case</th>
                            <th scope="col">Answer</th>
                            <th scope="col">Student Answer</th>
                            <th scope="col">Max Points</th>
                            <th scope="col">Earned Points</th>
                            <th scope="col">Modify Points</th>  
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i < 6; $i++) : ?>
                            <?php if (!empty($values->{'testcase' . $i})) : ?>
                                <tr>
                                    <td scope="row"><?php echo $values->{'testcase' . $i}; ?></td>
                                    <td><?php echo $values->{'testcase' . $i . 'Ans'}; ?></td>
                                    <td><?php echo $values->{'studentAns' . $i}; ?></td>
                                    <td><?php echo $values->testcaseTotal; ?></td>
                                    <td><?php echo $values->{'testcase' . $i . 'Points'}; ?></td>
                                    <td>
                                        <input class="form-control" name='<?php echo 'testcase' . $i . 'Points'; ?>[]' type="text">
                                    </td>
                                </tr>
                            <?php else : ?>
                                <input class="d-none" name='<?php echo 'testcase' . $i . 'Points'; ?>[]' type="text">
                            <?php endif; ?>
                        <?php endfor; ?>
                        <tr>
                            <th scope="row">Function Name</th>
                            <td><?php echo $values->actualFunctionName; ?></td>
                            <td><?php echo $values->studentFunctionName; ?></td>
                            <td>5</td>
                            <td><?php echo $values->functionScore; ?></td>
                            <td>
                                <input class="form-control" name="functionScores[]" type="text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Constraint</th>
                            <?php if ($values->constraintScore != -1) : ?>
                                <? if ($values->constraint == 'forLoops') : ?>
                                    <?php $con = 'for'; ?>
                                <?php elseif ($values->constraint == 'whileLoops') : ?>
                                    <?php $con = 'while'; ?>
                                <?php else: ?>
                                    <?php $con = 'recursion'; ?>
                                <?php endif; ?>
                                <td><?php echo $con ?></td>
                                <?php if ($values->constraintScore > 0) : ?>
                                    <td><?php echo $con ?></td>
                                <?php else : ?>
                                    <td>Not Found</td>
                                <?php endif; ?>
                                <td>5</td>
                                <td><?php echo $values->constraintScore; ?></td>
                                <td>
                                    <input class="form-control" name="constraintScores[]" type="text">
                                </td>
                            <?php else : ?>
                                <td>None</td>
                                <td>None</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <input class="d-none" name="constraintScores[]" type="text">
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th scope="row">Total</th>
                            <td colspan="2"></td>
                            <td><?php echo $values->totalPoints; ?></td>
                            <td><?php echo $values->score; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Comments</th>
                            <td colspan="5"><textarea class="form-control" name="comments[]"></textarea></td>      
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <button class="btn btn-primary" name="submit" type="submit">Submit</button>
    </form>
</div>


<?php

if (isset($_POST['testcase1Points']) or isset($_POST['testcase2Points']) or isset($_POST['testcase3Points']) or isset($_POST['testcase4Points']) or isset($_POST['testcase5Points'])) {
    $testcase1Points = $_POST['testcase1Points'];
    $testcase2Points = $_POST['testcase2Points'];
    $testcase3Points = $_POST['testcase3Points'];
    $testcase4Points = $_POST['testcase4Points'];
    $testcase5Points = $_POST['testcase5Points'];
    $functionScores = $_POST['functionScores'];
    $constraintScores = $_POST['constraintScores'];
    
    for ($i = 1; $i < 6; $i++) {
        for ($j = 0; $j < count(${'testcase' . $i . 'Points'}); $j++) {
            if (!empty(${'testcase' . $i . 'Points'}[$j]) or ${'testcase' . $i . 'Points'}[$j] === "0") {
                $response[$j]->{'testcase' . $i . 'Points'} = ${'testcase' . $i . 'Points'}[$j];
            }
        }
    }
    
    for ($i = 0; $i < count($constraintScores); $i++) {
        if (!empty($constraintScores[$i]) or $constraintScores[$i] === "0") {
            $response[$i]->constraintScore = $constraintScores[$i];
        }
    }
    
    for ($i = 0; $i < count($functionScores); $i++) {
        if (!empty($functionScores[$i]) or $functionScores[$i] === "0") {
            $response[$i]->functionScore = $functionScores[$i];
        }
    }

    //totalScore is grade
    //score is total earned points in question
    $totalGrade = 0;
    
    for ($i = 0; $i < count($response); $i++) {
        $scoreEarnedPerQuestion = 0;
        for ($j = 1; $j < 6; $j++) {
            if ($response[$i]->{'testcase' . $j . 'Points'} != -1) {
                $scoreEarnedPerQuestion += $response[$i]->{'testcase' . $j . 'Points'};
            }
        }

        $scoreEarnedPerQuestion += $response[$i]->functionScore;
        if ($response[$i]->constraintScore != -1) {
            $scoreEarnedPerQuestion += $response[$i]->constraintScore;
        }
        
        $totalGrade += $scoreEarnedPerQuestion;

        $response[$i]->score = $scoreEarnedPerQuestion;
    }

    for ($i = 0; $i < count($response); $i++) {
        $response[$i]->totalScore = $totalGrade;
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

    // print_r($response);
    $data2 = array('requestType' => 'saveFinal', 'examName' => $_GET['examName'], 'student' => $_GET['student'], 'bigArray' => $response, 'comments' => $sendComments);
    $response2 = curlRequest($data2);

    redirect("teacher/grade_tests.php");
}

?>
