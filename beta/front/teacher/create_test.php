<?php
require(__DIR__ . "/../nav.php");

if ($role != "teacher") {
    redirect("");
}
$data = array("requestType" => "showBank");
$response = curlRequest($data);

if (empty($response)) {
    $response = [];
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1>Create Test</h1>
    <form method="POST">
        <div class="mb-5">
            <h3>Test Name</h3>
            <input class="form-control" name="examName" type="text" placeholder="Test Name">
        </div>
        <?php foreach ($response as $question => $values) : ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value='<?php echo $values->questionID; ?>' name="questionIDs[]">
                <p><?php echo $values->question; ?></p>
                <p><?php echo $values->difficulty; ?></p>
                <p><?php echo $values->category; ?></p>
                <div class="d-flex">
                    <p>Points:</p>
                    <input class="form-control" name="points[]" type="text" placeholder="0" style="width:50px;">
                </div>
            </div>
        <?php endforeach; ?>
        <div>
            <button class="btn btn-primary" name="submit" type="submit">Submit</button>
        </div>
    </form>
</div>

<?php
if (isset($_POST["questionIDs"]) and isset($_POST["points"])) {
    $questionIDs = $_POST["questionIDs"];
    $p = $_POST["points"];
    $examName = $_POST["examName"];
    $acceptable = true;

    for ($i = 0; $i <= count($p); $i++) {
        if (!empty($p[$i])) {
            $points[] = $p[$i];
        }
    }

    for ($i = 0; $i <= count($points); $i++) {
        if ((int)$points[$i] > 100) {
            $acceptable = false;
        }
    }

    if ($acceptable) {
        $data = array("requestType" => "saveExam", "examName" => $examName, "questionIDs" => $questionIDs, "points" => $points);
        $response = curlRequest($data);
    }
    else {
        echo "unacceptable";
    }
}

?>