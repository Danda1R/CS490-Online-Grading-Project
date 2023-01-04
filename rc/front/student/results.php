<?php
require(__DIR__ . "/../nav.php");

// if ($role != "teacher") {
//     redirect("");
// }

// if (!isset($_GET["student"])) {
//     redirect("teacher/select_student.php");  
// }

$data = array("requestType" => "showFinal", "student" => $_GET["student"], "examName" => $_GET["examName"]);
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
                                </tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <tr>
                            <th scope="row">Function Name</th>
                            <td><?php echo $values->actualFunctionName; ?></td>
                            <td><?php echo $values->studentFunctionName; ?></td>
                            <td>5</td>
                            <td><?php echo $values->functionScore; ?></td>
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
                            <?php else : ?>
                                <td>None</td>
                                <td>None</td>
                                <td>0</td>
                                <td>0</td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th scope="row">Total</th>
                            <td colspan="2"></td>
                            <td><?php echo $values->totalPoints; ?></td>
                            <td><?php echo $values->score; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Comments</th>
                            <td colspan="5"><?php echo $values->comment; ?></td>      
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </form>
</div>


