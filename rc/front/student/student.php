<?php
require(__DIR__ . "/../nav.php");

if ($_SESSION["role"] != "student") {
    redirect("");
}

$data = array("requestType" => "showExamList");
$response = curlRequest($data);

$data2 = array("requestType" => "listSubmittedExams");
$response2 = curlRequest($data2);

if (!empty($response2)) {
    for ($i = 0; $i < count($response2); $i++) {
        if ($response2[$i]->student == $_SESSION["user"]) {
            $examsTaken[] = $response2[$i]->examName;
        }
    }
}
else {
    $examsTaken = [];
}

$data3 = array("requestType" => "listGradedExamsFinal", "student" => $_SESSION["user"]);
$response3 = curlRequest($data3);

if (empty($response3)) {
    $response3 = [];
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1 class="my-3">Welcome Student</h1>
    <?php foreach ($response as $tests => $values) : ?>
        <div class="d-flex flex-row justify-content-between shadow py-4 px-5 mb-5 bg-body rounded align-items-center">
            <p class="my-3" style="font-weight:bold;"><?php echo $values->examName; ?></p>
            <div>
                <? if (!in_array($values->examName, $examsTaken)) : ?>
                    <a href='https://afsaccess4.njit.edu/~jcb62/rc/student/test.php?examName=<?php echo $values->examName; ?>' style="text-decoration:none;">
                        <button type="button" class="btn btn-primary btn-lg" id="takeTest">Take Test</button>
                    </a>
                <? elseif (in_array($values->examName, $response3)) : ?>
                    <a href='https://afsaccess4.njit.edu/~jcb62/rc/student/results.php?examName=<?php echo $values->examName; ?>&student=<?php echo $_SESSION['user']; ?>' style="text-decoration:none;">
                        <button type="button" class="btn btn-secondary btn-lg" id="reviewTest">Review Test</button>
                    </a>
                <? else : ?>
                    <h2>Not Graded</h2>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>