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
    <nav class="d-flex justify-content-end mt-3" aria-label="...">
        <ul class="pagination">
            <?php for ($i = 0; $i < count($response); $i++): ?>
                <li class="page-item questionPage <?php if ($i == 0) echo 'active'; ?>"><a class="page-link" href="#"><?php echo $i + 1; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
    <form method="POST">
        <?php foreach ($response as $questions => $values) : ?>
            <div class="mb-5 examQuestions <?php if ($questions != 0) echo 'd-none';?>" id='q<?php echo $questions + 1; ?>'>
                <p><?php echo $values->question; ?></p>
                <p><span style="font-weight:bold;">Total Points: </span><?php echo $values->points; ?></p>
                <textarea class="form-control" name="answers[]" rows="15"></textarea>
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

<script>
    const questionPage = document.querySelectorAll('.questionPage')
    const examQuestions = document.querySelectorAll('.examQuestions')

    function changeQuestion(qNum) {
        examQuestions.forEach((item) => {
            if (item.id == `q${qNum}`) {
                item.classList.remove('d-none')
            }
            else {
                item.classList.add('d-none')
            }
        })

        questionPage.forEach((item) => {
            if (item.innerText == qNum) {
                item.classList.add('active')
            }
            else {
                item.classList.remove('active')
            }
        })
    }

    questionPage.forEach((item) => {
        item.addEventListener('click', () => {
            changeQuestion(item.innerText)
        })
    })

</script>


