<?php
require_once(__DIR__ . "/functions.php");
session_start();

if (isset($_SESSION["role"])) {
    $role = $_SESSION["role"];

    if ($role == "teacher") {
      $homeUrl = "https://afsaccess4.njit.edu/~jcb62/beta/teacher/teacher.php";
    }

    if ($role == "student") {
      $homeUrl = "https://afsaccess4.njit.edu/~jcb62/beta/student/student.php";
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href='<?php echo $homeUrl; ?>'><?php echo "Hello " . ucfirst($role); ?></a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <?php if ($role == "teacher") : ?>
            <li class="nav-item">
                <a class="nav-link" href="https://afsaccess4.njit.edu/~jcb62/beta/teacher/create_question.php">Create Question</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://afsaccess4.njit.edu/~jcb62/beta/teacher/create_test.php">Create Test</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://afsaccess4.njit.edu/~jcb62/beta/teacher/grade_tests.php">Grade Tests</a>
            </li>
        <?php endif; ?>
        <?php if (isset($role)) : ?>
          <li class="nav-item">
              <a class="nav-link" href="https://afsaccess4.njit.edu/~jcb62/beta/logout.php">Log Out</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
