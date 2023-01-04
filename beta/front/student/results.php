<?php
require(__DIR__ . "/../nav.php");

if ($_SESSION["role"] != "student") {
    redirect("");
}

$data = array("requestType" => "showResultsFinal", "student" => $_SESSION["user"], "examName" => $_GET["examName"]);
$response = curlRequest($data);

$total = 0;
for ($i = 0; $i < count($response); $i++) {
    $total += (int)$response[$i]->score;
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1><?php echo $_GET["examName"]; ?></h1>
    <h2>Grade: <?php echo $total; ?>/100</h2>
    <div>
        <?php foreach($response as $results => $values) : ?>
            <div class="mb-5">
                <p>Question: <?php echo $values->question; ?></p>
                <p>Student Answer:</p>
                <pre><?php echo $values->answer; ?></pre>
                <p>Teacher Comments: <?php echo $values->comment; ?></p>
                <p>Your Score: <?php echo $values->score; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>