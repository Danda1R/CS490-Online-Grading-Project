<?php
require(__DIR__ . "/../nav.php");

if ($role != "teacher") {
    redirect("");
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container">
    <h1>Create Question</h1>
    <form method="POST">
        <div>
            <label for="question">Question</label>
            <textarea class="form-control" name="question" id="question"></textarea>
        </div>
        <label>Test Cases</label>
        <div class="form-group d-flex flex-row">
            <div>
                <input type="text" class="form-control" name="input1" id="input1" placeholder="input">
                <input type="text" class="form-control" name="input2" id="input2" placeholder="input">
            </div>
            <div>
                <input type="text" class="form-control" name="output1" id="output1" placeholder="output">
                <input type="text" class="form-control" name="output2" id="output1" placeholder="output">
            </div>
        </div>
        <label>Difficulty</label>
        <div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="difficulty" id="easy" value="easy">
                <label class="form-check-label" for="easy">
                    Easy
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="difficulty" id="medium" value="medium">
                <label class="form-check-label" for="medium">
                    Medium
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="difficulty" id="hard" value="hard">
                <label class="form-check-label" for="hard">
                    Hard
                </label>
            </div>
        </div>
        <label>Category</label>
        <div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="variables" value="variables">
                <label class="form-check-label" for="variables">
                    Variables
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="conditionals" value="conditionals">
                <label class="form-check-label" for="conditionals">
                    Conditionals
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="forLoops" value="forLoops">
                <label class="form-check-label" for="forLoops">
                    For Loops
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="whileLoops" value="whileLoops">
                <label class="form-check-label" for="whileLoops">
                    While Loops
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="lists" value="lists">
                <label class="form-check-label" for="lists">
                    Lists
                </label>
            </div>
        </div>
        <div>
            <button class="btn btn-primary" name="submit" type="submit">Submit</button>
        </div>
    </form>
</div>
<!-- 255 character limit -->
<?php
if (isset($_POST["question"]) and isset($_POST["input1"]) and isset($_POST["output1"]) and isset($_POST["input2"]) and isset($_POST["output2"]) and isset($_POST["difficulty"]) and isset($_POST["category"])){
    $question = $_POST["question"];
    $input1 = $_POST["input1"];
    $output1 = $_POST["output1"];
    $input2 = $_POST["input2"];
    $output2 = $_POST["output2"];
    $difficulty = $_POST["difficulty"];
    $category = $_POST["category"];

    $data = array("requestType" => "insertBank", "question" => $question, "testcase1" => $input1, "testcase1Ans" => $output1, "testcase2" => $input2, "testcase2Ans" => $output2, "difficulty" => $difficulty, "category" => $category);

    $response = curlRequest($data);
}
?>