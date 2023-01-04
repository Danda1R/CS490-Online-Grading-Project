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
    <h1>Grade Tests</h1>
    <form method="POST">
        <?php foreach ($response as $tests => $values) : ?>
            <div class="mb-5">
                <p><?php echo $values->examName; ?></p>
                <p><?php echo $values->student; ?></p>
                <?php if (!in_array($values->examName, $examsAutoGraded)) : ?>
                    <button type="submit" class="btn btn-primary" name="test" value='<?php echo $values->examName; ?> <?php echo $values->student;?>'>Autograde Test</button>
                <?php endif; ?>
                <?php if (in_array($values->examName, $examsAutoGraded)) : ?>
                    <a href='https://afsaccess4.njit.edu/~jcb62/beta/teacher/modify_grade.php?examName=<?php echo $values->examName; ?>&student=<?php echo $values->student; ?>' style="text-decoration:none;">
                        <button type="button" class="btn btn-secondary">Modify Grades</button>
                    </a>
                    <button type="submit" class="btn btn-success" name="publish" value='<?php echo $values->examName; ?> <?php echo $values->student;?>'>Publish Grades</button>
                <?php endif; ?>
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

        for ($i = 0; $i < count($response3); $i++) {
            if (($i + 1) % 3 == 0) {
                $scores[] = $response3[$i];
            }
            else {
                $studentAns[] = $response3[$i];
            }
        }
        
        for ($i = 0; $i < count($studentAns); $i++) {
            if (($i == 0) or ($i % 2 == 0)) {
                $studentAns1[] = $studentAns[$i];
            }
            else {
                $studentAns2[] = $studentAns[$i];
            }
        }

        $data4 = array("requestType" => "saveAutoGrade", "examName" => $test[0], "student" => $test[1], "scores" => $scores, "studentAns1" => $studentAns1, "studentAns2" => $studentAns2);
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
