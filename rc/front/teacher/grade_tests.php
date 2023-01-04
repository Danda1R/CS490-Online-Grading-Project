<?php
require(__DIR__ . "/../nav.php");

if ($role != "teacher") {
    redirect("");
}

$data = array("requestType" => "listSubmittedExams");
$response = curlRequest($data);

if (empty($response)) {
    $response = [];
}

$data2 = array("requestType" => "listAutoGradedExams");
$response2 = curlRequest($data2);

if (!empty($response2)) {
    for ($i = 0; $i < count($response2); $i++) {
        $examsAutoGraded[] = $response2[$i]->examName;
    }
}
else {
    $examsAutoGraded = [];
}

// $data5 = array("requestType" => "listGradedExamsFinal");
// $response5 = curlRequest($data5);

// if (!empty($response5)) {
//     for ($i = 0; $i < count($response5); $i++) {
//         $examsModified[] = $response5[$i]->examName;
//     }
// }
// else {
//     $examsModified = [];
// }

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1 class="my-3">Grade Tests</h1>
    <form method="POST">
        <?php foreach ($response as $tests => $values) : ?>
            <div class="d-flex flex-row justify-content-between align-items-center shadow py-4 px-5 mb-5 bg-body rounded">
                <div class="align-middle my-3">
                    <p style="margin-bottom:10px; font-weight:bold;"><?php echo $values->examName; ?></p>
                    <p style="margin:2px;"><?php echo $values->student; ?></p>
                </div>
                <div>
                    <?php if (!in_array($values->examName, $examsAutoGraded)) : ?>
                        <button type="submit" class="btn btn-primary btn-lg" name="test" value='<?php echo $values->examName; ?> <?php echo $values->student;?>'>Autograde Test</button>
                    <?php endif; ?>
                    <?php if (in_array($values->examName, $examsAutoGraded)) : ?>
                        <div>
                            <a href='https://afsaccess4.njit.edu/~jcb62/rc/teacher/modify_grade.php?examName=<?php echo $values->examName; ?>&student=<?php echo $values->student; ?>' style="text-decoration:none;">
                                <button type="button" class="btn btn-warning btn-lg">Modify Grades</button>
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" name="publish" value='<?php echo $values->examName; ?> <?php echo $values->student;?>'>Publish Grades</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</div>

<?php
    if (isset($_POST["test"])) {
        // test = [examName, student]
        $test = explode(" ", $_POST["test"]);
        $data3 = array("requestType" => "gradeExam", "examName" => $test[0], "student" => $test[1]);
        $response3 = curlRequest($data3);
        
        // print_r($response3);
        //correct_function_name, student_function_name, function_score, constraint_score, testcase1 return, testcase1 student score, testcase1 score worth, testcase2 return, testcase2 student score, testcase2 score worth, testcase3 return, testcase3 student score, testcase3 score worth, testcase4 return, testcase4 student score, testcase4 score worth, testcase5 return, testcase5 student score, testcase5 score worth
        //Array ( [0] => largest [1] => largest [2] => 5 [3] => [4] => 0 [5] => [6] => 0 [7] => [8] => 0 [9] => -1 [10] => -1 [11] => -1 [12] => -1 )
        
        $questionArray = array();
        $question = array();

        $scoreEarnedPerQuestion = array();
        $actualFunc = array();
        $studentFunc = array();
        $functionScore = array();
        $constraintScore = array();
        $testcaseTotal = array();
        $testcasePoints = array();
        $testcasePointsArray = array();
        $studentAns = array();
        $studentAnsArray = array();

        for ($i = 0; $i < count($response3); $i++) {
            if (($i + 1) % 19 == 0) {
                array_push($question, $response3[$i]);
                array_push($questionArray, $question);
                $question = array();
            }
            else {
                array_push($question, $response3[$i]);
            }
        }

        for ($i = 0; $i < count($questionArray); $i++) {
            array_push($actualFunc, $questionArray[$i][0]);
            array_push($studentFunc, $questionArray[$i][1]);
            array_push($functionScore, $questionArray[$i][2]);
            array_push($constraintScore, $questionArray[$i][3]);
            array_push($studentAns, $questionArray[$i][4], $questionArray[$i][7], $questionArray[$i][10], $questionArray[$i][13], $questionArray[$i][16]);
            array_push($testcasePoints, $questionArray[$i][5], $questionArray[$i][8], $questionArray[$i][11], $questionArray[$i][14], $questionArray[$i][17]);
            array_push($testcaseTotal, $questionArray[$i][6]);
            
            $tempTestcasePoints = array($questionArray[$i][5], $questionArray[$i][8], $questionArray[$i][11], $questionArray[$i][14], $questionArray[$i][17]);
            
            //-1 indicates test case wasnt created, make it zero for array_sum
            for ($j = 0; $j < count($tempTestcasePoints); $j++) {
                if ($tempTestcasePoints[$j] == -1) {
                    $tempTestcasePoints[$j] = 0;
                }
            }
            
            //empty constraint score indicates no constraint set, make it zero for score sum
            if (empty($questionArray[$i][3])) {
                $tempConstraintScore = 0;
            }
            else {
                if ($questionArray[$i][3] == -1) {
                    $tempConstraintScore = 0;
                }
                else {
                    $tempConstraintScore = $questionArray[$i][3];
                }
            }
           
            $score = $questionArray[$i][2] + $tempConstraintScore + array_sum($tempTestcasePoints);
            
            array_push($scoreEarnedPerQuestion, $score);
        }
        
        $tempArray = array();
        for ($i = 0; $i < count($studentAns); $i++) {
            if (($i + 1) % 5 == 0) {
                array_push($tempArray, $studentAns[$i]);
                array_push($studentAnsArray, $tempArray);
                $tempArray = array();
            }
            else {
                array_push($tempArray, $studentAns[$i]);
            }
        }

        $temptestArray = array();
        for ($i = 0; $i < count($testcasePoints); $i++) {
            if (($i + 1) % 5 == 0) {
                array_push($temptestArray, $testcasePoints[$i]);
                array_push($testcasePointsArray, $temptestArray);
                $temptestArray = array();
            }
            else {
                array_push($temptestArray, $testcasePoints[$i]);
            }
        }
        
        $data4 = array("requestType" => "saveAutoGrade", "examName" => $test[0], "student" => $test[1], "scores" => $scoreEarnedPerQuestion, "studentAns" => $studentAnsArray, "testcasePoints" => $testcasePointsArray, "testcaseTotal" => $testcaseTotal, "actualFunctionName" => $actualFunc, "studentFunctionName" => $studentFunc, "functionScore" => $functionScore, "constraintScore" => $constraintScore);
        $response4 = curlRequest($data4);
        redirect("teacher/grade_tests.php");
    }

    if (isset($_POST["publish"])) {
        // [examName, student]
        $publish = explode(" ", $_POST["publish"]);
        
        $data6 = array("requestType" => "publishGrades", "examName" => $publish[0], "student" => $publish[1]);
        $response6 = curlRequest($data6);
    }
    
?>
