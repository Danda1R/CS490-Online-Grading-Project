<?php
#Ian Church-Krafte - CS490101 - backend - beta
$post = file_get_contents('php://input');
$json = json_decode($post, true);
#echo $json;
#echo json_encode("hi");

$servername = 'sql1.njit.edu';
$username = 'iac22';
$password = ''; #sql password

$conn = new mysqli($servername, $username, $password, 'iac22');
if($conn->connect_error){
    echo json_encode("cant connect");
    die("Connection failed: " . $conn->connect_error);
}
#echo "Connected successfully to mysql";
#$purpose = $json['requestType'];
$purpose = $json['requestType'];

switch($purpose){
    case "login":
        $user = $json['user'];
        $pass = $json['pass'];
        #echo json_encode('hi');
        $result = db_lookup($user, $pass, $conn);
        echo json_encode($result);
        break;
    case "showExamList":
        $result = showExamList($conn);
        echo json_encode($result);
        break;
    case "listGradedExams":
        $student = $json['student'];
        $result = listGradedExams($student, $conn);
        echo json_encode($result);
        break;
    case "insertBank": #question, answer, test case1, test case 2, difficulty
        $question = $json['question'];
        $testcase1 = $json['testcase1'];
        $testcase1Ans = $json['testcase1Ans'];
        $testcase2 = $json['testcase2'];
        $testcase2Ans = $json['testcase2Ans'];
        $difficulty = $json['difficulty'];
        $category = $json['category'];


        $result = db_insertQuestion($question, $testcase1, $testcase1Ans, $testcase2, $testcase2Ans, $difficulty, $category, $conn);
        echo json_encode($result);
        #echo json_encode("inserted");
        break;
    case "showBank": #every entry in bank, question, answer, test case 1, test case 2, difficulty
        $result = db_showBank($conn);
        echo json_encode($result);
        break;
    #EXAM NAME HAS TO BE ONE WORD, WILL BREAK SQL STATEMENTS
    case "saveExam": #saving the exam with the given name, questions, answers, test cases, question difficulty, points the question is worth
        $examName = $json['examName'];
        $questionIDs = $json['questionIDs'];
        $points = $json['points'];
        $result = db_saveExam($examName, $questionIDs, $points, $conn);
        echo json_encode($result);
        break;
    #NEED SHOW EXAM FOR STUDENT AND PROF(TEST CASES,)
    case "showExamStudent": #showing the saved exam given the exam name to the student, questions
        $examName = $json['examName'];
        #$student = $json['student'];
        #$questionIDs = $json['questionIDs'];
        #echo json_encode("im here");
        $result = db_showExamStudent($examName, $conn);
        #result is an array, each row being a row in the table
        echo json_encode($result);
        break;
    case "saveStudentExam":
        $examName = $json['examName'];
        $student = $json['student'];
        #$questionIDs = $json['questionIDs'];
        $answers = $json['answers'];

        $result = db_saveStudentExam($examName, $student, $answers, $conn);
        echo json_encode($result);
        break;
    case "showExamProf":
        $student = $json['student'];
        $examName = $json['examName'];

        $result = db_showExamProf($examName, $student, $conn);
        echo json_encode($result);
        break;

       //aslo needs a list of autograded exams 
    case "listAutoGradedExams":

        $result = listAutoGradedExams($conn);
        echo json_encode($result);
        break;
    case "saveAutoGrade":
        $examName = $json['examName'];
        $scores = $json['scores'];
        $student = $json['student'];
        $studentAns1 = $json['studentAns1'];
        $studentAns2 = $json['studentAns2'];

        $result = saveAutoGrade($examName, $scores, $student, $studentAns1, $studentAns2, $conn);
        echo json_encode($result);
        break;

    case "showAutoGrade":
        //question, testcases, testcaseAns, student answer, autograde score, total points
        $examName = $json['examName'];
        $student = $json['student'];

        $result = showAutoGrade($examName, $student, $conn);
        echo json_encode($result);
        break;

    case "saveResultsFinal": #question, answer, score, comment, studentid
        //modified score and comments
        $examName = $json['examName'];
        #$questions = $json['questions'];
        #$answers = $json['answers'];
        $scores = $json['scores'];
        $comments = $json['comments'];
        $student = $json['student'];
        
        $result = db_saveResultsFinal($examName, $scores, $comments, $student, $conn);
        echo json_encode($result);
        break;
    case "publishGrades":
        $examName = $json['examName'];
        $student = $json['student'];

        $result = publishGrades($examName, $student, $conn);
        echo json_encode($result);
        break;
    case "showResultsFinal": #questions, answer, score, comment
        $examName = $json['examName'];
        $student = $json['student'];
        #echo json_encode('hi');
        $result = db_showResultsFinal($examName, $student, $conn);
        echo json_encode($result);
        break;
    case "listSubmittedExams":
        $result = listSubmittedExams($conn);
        echo json_encode($result);
        break;
    case "showTestCases":
        $examName = $json['examName'];
        $student = $json['student'];
        #echo json_encode("hi");
        $result = showTestCases($examName, $student, $conn);
        echo json_encode($result);
        break;
    case "listGradedExamsFinal":
        $student = $json['student'];

        $result = listGradedExamsFinal($student, $conn);
        echo json_encode($result);
        break;
}


function db_lookup($user, $pass, $conn){
    $sql = "SELECT role FROM alpha WHERE username='". $user. "' AND password='".$pass."'";
    $result = $conn->query($sql);
    #return $result;
    if($result->num_rows > 0){
        $role = $result->fetch_assoc();
        if($role['role'] == 'student'){
            $sql = "SELECT student FROM studentList WHERE student='".$student."'";
            $result = $conn->query($sql);
            if($result->num_rows == 0){
                $sql = "INSERT INTO studentList VALUES('".$student."'";
                $conn->query($sql);
            }
        }
        return $role["role"];
    } else {
        return 'denied';
    }
}

function db_insertQuestion($question, $testcase1, $testcase1Ans, $testcase2, $testcase2Ans, $difficulty, $category, $conn){
    $sql = "INSERT INTO questionBank (question, testcase1, testcase1Ans, testcase2, testcase2Ans, difficulty, category) VALUES ('".$question."', '".$testcase1."', '".$testcase1Ans."', '".$testcase2."', '".$testcase2Ans."', '".$difficulty."', '".$category."')";
    #return $sql;
    $result = $conn->query($sql);
    #return $result;
    if ($result == FALSE){
        return "insert_failed";
    }
    return 'insert_successfull';
}


function db_showBank($conn){
    $sql = "SELECT * FROM `questionBank`";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    #$question = $result->fetch_assoc()['question'];
    #return $question;
    $i=0;
    if($n > 0){
        while($row = $result->fetch_assoc()){
            $bank[] = array("questionID" => $row["id"], "question" => $row["question"], "testcase1" => $row["testcase1"], "testcase1Ans" => $row["testcase1Ans"], 
                            "testcase2" => $row["testcase2"], "testcase2Ans" => $row["testcase2Ans"],
                            "difficulty" => $row["difficulty"], "category" => $row["category"]);
        }
        return $bank;
    }
    else{
        return "empty_bank";
    }
}

#table saved is named what the exam name is
function db_saveExam($examName, $questionIDs, $points, $conn){
    $conn->query("DROP TABLE IF EXISTS ". $examName);
    #$conn->query("CREATE TABLE ". $examName ."()
    $conn->query("CREATE TABLE ". $examName ." (id int, question varchar(255), testcase1 varchar(255), testcase1Ans varchar(255), testcase2 varchar(255), testcase2Ans varchar(255), difficulty varchar(255), category varchar(255), points int)");
    for($i=0; $i<count($questionIDs); $i++){
        $sql = "SELECT * FROM questionBank WHERE id='". $questionIDs[$i] ."'";
        #return $sql;
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $exam[] = array("id" => $questionIDs[$i], "question" => $row["question"], "testcase1" => $row["testcase1"], "testcase1Ans" => $row["testcase1Ans"], 
                        "testcase2" => $row["testcase2"], "testcase2Ans" => $row["testcase2Ans"],
                        "difficulty" => $row["difficulty"], "category" => $row["category"]);
    }
    #return $exam[0]['question'];
    for($i=0; $i<count($questionIDs); $i++){
        #return ;
        $sql = "INSERT INTO ". $examName ." VALUES (".$questionIDs[$i].", '".$exam[$i]["question"]."', '".$exam[$i]["testcase1"]."', '".$exam[$i]['testcase1Ans']."', '".$exam[$i]["testcase2"]
                ."', '".$exam[$i]["testcase2Ans"]."', '".$exam[$i]["difficulty"]."', '".$exam[$i]["category"]."', '".$points[$i]."')";
        #return $sql;
        $result = $conn->query($sql);
    }
    $sql = "SELECT examName FROM examList WHERE examName='".$examName."'";
    #return $sql;
    $result = $conn->query($sql);
    #return $result->num_rows;
    if($result->num_rows == 0){#NOT IN THE LIST OF EXAMS
        $sql = "INSERT INTO examList (examName) VALUES('".$examName."')";
        #return $sql;
        $conn->query($sql);
        #return "got through if";
    }
    #$sql = ""
    if ($result == FALSE){
        return "saveExam_failed";
    }

    return "saveExam_successfull";
}

function db_showExamStudent($examName, $conn){
    $sql = "SELECT question, points FROM ".$examName;
    //return $sql;
    #return $sql;
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $exam[] = array("question" => $row['question'], "points" => $row['points']);
    }
    return $exam;
}

function showExamList($conn){
    $sql = "SELECT examName FROM examList";
    #return $sql;
    $result = $conn->query($sql);
    if($result->num_rows == 0){
        return "empty";
    }
    while($row = $result->fetch_assoc()){
        $examList[] = array("examName" => $row['examName']);
    }
    return $examList;
}
function db_saveStudentExam($examName, $student, $answers, $conn){
    $check = $conn->query("SELECT student, examName FROM submittedExams");
    $alreadyIn = 0;
    while($row = $check->fetch_assoc()){
        if($row['student'] == $student && $row['examName'] == $examName){
            $alreadyIn = 1;
        }
    }
    if($alreadyIn == 0){
        $sql = "INSERT INTO submittedExams VALUES('".$student."', '".$examName."')";
        $conn->query($sql);
    }
    #return $sql;
    $conn->query("DROP TABLE IF EXISTS ".$examName.$student);
    $sql = "CREATE TABLE ".$examName.$student." (question varchar(255), answer varchar(255))";
    $conn->query($sql);
    #return $sql;
    $result = $conn->query("SELECT question FROM ".$examName);
    $i =0;
    while($row = $result->fetch_assoc()){
        $sql = "INSERT INTO ".$examName.$student." VALUES('".$row['question']."', '".$answers[$i]."')";
        #return $sql;
        $ins = $conn->query($sql);
        if($ins == FALSE){
            return "saveStudentExam_Failed";
        }
        $i++;
    }
    
}
function db_showExamProf($examName, $student, $conn){
    $sql = "SELECT * FROM ".$examName.$student;
    $result = $conn->query($sql);

    $n = $result->num_rows;
    if ($n>0){
        while($row = $result->fetch_assoc()){
            $exam[]=array("question" => $row["question"], "answer" => $row["answer"]);
        }
        return $exam;
    }
    else{
        return "empty_showExamProf";
    }

}

function db_saveResultsFinal($examName, $scores, $comments, $student, $conn){
    $sql = "DROP TABLE IF EXISTS ". $examName ."Results". $student."Final"; #table name e.g. examNameResultsStudentID
    #return $sql;
    $conn->query($sql);
    $conn->query("CREATE TABLE ". $examName ."Results".$student."Final (question varchar(255), answer varchar(255), score varchar(255), comment varchar(255), student varchar(255))");
    
    $sql = "SELECT question, answer FROM ".$examName.$student;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty student exam in saveResultsFinal";
    }
    while($row = $result->fetch_assoc()){
        $qAndA[] = array("question" => $row['question'], "answer" => $row['answer']);
    }
    for($i=0; $i<count($scores); $i++){
        $sql = "INSERT INTO ".$examName."Results".$student."Final VALUES('".$qAndA[$i]['question']."', '".$qAndA[$i]['answer']."', '".$scores[$i]."', '".$comments[$i]."', '".$student."')";
        $result = $conn->query($sql);
        if($result == FALSE){
            return "badInsert_in_saveResultsFinal";
        }
    }
    
    return "saveResultsFinal_successfull";
}

function publishGrades($examName, $student, $conn){
    $sql = "SELECT examName FROM gradedExamsFinal WHERE student='".$student."' AND examName='".$examName."'";
    #return $sql;
    $result = $conn->query($sql);
    if($result->num_rows == 0){
        $sql = "INSERT INTO gradedExamsFinal VALUES('".$examName."', '".$student."')";
        #return $sql;
        $conn->query($sql);
        if ($result == FALSE){
            return "saveResultsFinal_failed";
        }
    }
    return "publishGrades_successfull";
}

function listGradedExamsFinal($student, $conn){
    $sql = "SELECT * FROM gradedExamsFinal WHERE student='".$student."'";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        #return array();
        #"empty_gradedExamsFinal_in_listGradedExamsFinal"
    }
    while($row = $result->fetch_assoc()){
        #$exams[] = array("examName" => $row['examName']);
        $exams[] = $row['examName'];
    }
    return $exams;
}

function listGradedExams($student, $conn){
    $sql = "SELECT * FROM gradedExams WHERE student='".$student."'";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $exams[]=array("examName" => $row['examName']);
    }
    return $exams;
}

function db_showResultsFinal($examName, $student, $conn){
    $sql = "SELECT question, answer, score, comment FROM ". $examName ."Results". $student ."Final WHERE student='". $student ."'";
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if ($n>0){
        while($row = $result->fetch_assoc()){
            $examResults[] = array("question" => $row['question'], "answer" => $row['answer'], "score" => $row['score'], "comment" => $row['comment']);
        }
        return $examResults;
    }
    else{
        return "showResults_failed";
    }
}

function listSubmittedExams($conn){
    $sql = "SELECT * FROM submittedExams";

    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $list[]=array("student" => $row['student'], "examName" => $row['examName']);
    }
    return $list;
}

function showTestCases($examName, $student, $conn){ //bad solution but it works
    $conn->query("DROP TABLE IF EXISTS testcasesAndAnswers".$examName);
    $conn->query("CREATE TABLE testcasesAndAnswers".$examName."(testcase1 varchar(255), testcase1Ans varchar(255), testcase2 varchar(255), testcase2Ans varchar(255), answer varchar(255), points varchar(255))");
    
    $sql = "SELECT testcase1, testcase1Ans, testcase2, testcase2Ans FROM ".$examName;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_testcases";
    }
    while($row = $result->fetch_assoc()){
        $testcases[] = array("testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'], "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans']);
    }
    
    $sql = "SELECT answer FROM ".$examName.$student;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_showAnswers_showTestCases";
    }
    while($row = $result->fetch_assoc()){
        $answers[] = array("answer" => $row['answer']);
    }
    $sql = "SELECT points FROM ".$examName;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_points_in_exam_showTestCase";
    }
    while($row = $result->fetch_assoc()){
        #return "im here";
        $points[] = array("points" => $row['points']);
    }
    #return $points;
    // $sql = "INSERT INTO testcasesAndAnswers".$examName." VALUES('".$testcases[0]['testcase1']."', '".$testcases[$i]['testcase1Ans']."', '".$testcases[$i]['testcase2']."', '".$testcases[i]['testcase2Ans']."', '".
    //             $answers[$i]['answer'];
    for($i=0; $i<$n; $i++){
        //return "hi";
        $sql = "INSERT INTO testcasesAndAnswers".$examName." VALUES('".$testcases[$i]['testcase1']."', '".$testcases[$i]['testcase1Ans']."', '".$testcases[$i]['testcase2']."', '".$testcases[$i]['testcase2Ans']."', '".
                $answers[$i]['answer']."', '".$points[$i]['points']."')";
        #return $sql;
        $conn->query($sql);
    }
    
    $sql = "SELECT * FROM testcasesAndAnswers".$examName;
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $list[] = array("testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'], "testcase2" => $row['testcase2'],
        "testcase2Ans" => $row['testcase2Ans'], "answer" => $row['answer'], "points" => $row['points']);
    }

    return $list;
    #return array_merge($testcases, $answers);
}

function listAutoGradedExams($conn){
    $sql = "SELECT * FROM autoGradeList";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        #return 'empty autoGradeList';
    }
    while($row = $result->fetch_assoc()){
        $list[] = array("examName" => $row['examName'], "student" => $row['student']);
    }
    return $list;
}

function saveAutoGrade($examName, $scores, $student, $studentAns1, $studentAns2, $conn){
    $sql = "SELECT examName, student FROM autoGradeList WHERE examName='".$examName."' AND student='".$student."'";
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        $sql = "INSERT INTO autoGradeList VALUES('".$examName."', '".$student."')";
        $conn->query($sql);
    } 
    
    $sql = "DROP TABLE IF EXISTS ".$examName."autoGrade".$student;
    $conn->query($sql);

    $sql = "CREATE TABLE ".$examName."autoGrade".$student." (question varchar(255), studentAns1 varchar(255), studentAns2 varchar(255), score int)";
    #return $sql;
    $conn->query($sql);

    $sql = "SELECT question FROM ".$examName;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty exam in saveAutoGrade";
    }
    while($row = $result->fetch_assoc()){
        $questions[] = array("question" => $row['question']);
    }

    for($i=0; $i<count($questions); $i++){
        $sql = "INSERT INTO ".$examName."autoGrade".$student." VALUES('".$questions[$i]['question']."', '".$studentAns1[$i]."', '".$studentAns2[$i]."', '".$scores[$i]."')";
        $conn->query($sql);
    }
    return "saveAutoGrade_successfull";
}

function showAutoGrade($examName, $student, $conn){ //question, testcases, testcaseAns, student answer, autograde score, total points
    $sql = "SELECT question, testcase1, testcase1Ans, testcase2, testcase2Ans FROM ".$examName;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty Exam in showAutoGrade";
    }
    while($row = $result->fetch_assoc()){
        $examInfo[] = array("question" => $row['question'], "testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'],
                            "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans']);
    }

    $sql = "SELECT answer FROM ".$examName.$student;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty student exam in showAutoGrade";
    }
    while($row = $result->fetch_assoc()){
        $answers[] = array("answer" => $row['answer']);
    }

    $sql = "SELECT score, studentAns1, studentAns2 FROM ".$examName."autoGrade".$student;
    #return $sql;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return 'empty autoGradedExam in showAutoGrade';
    }
    while($row = $result->fetch_assoc()){
        $scores[] = array("score" => $row['score'], "studentAns1" => $row['studentAns1'], "studentAns2" => $row['studentAns2']);
    }
    #return $scores;
    $totalScore = 0;
    for($i=0; $i<count($scores); $i++){
        $totalScore += $scores[$i]['score'];
    }
    
    $sql = "SELECT points FROM ".$examName;
    $result = $conn->query($sql);
    if($n == 0){
        return "empty points in ".$examName;
    }
    while($row = $result->fetch_assoc()){
        $points[] = array("totalPoints" => $row['points']);
    }

    for($i=0; $i<count($answers); $i++){
        $info[] =  array("question" => $examInfo[$i]['question'], "testcase1" => $examInfo[$i]['testcase1'], "testcase1Ans" => $examInfo[$i]['testcase1Ans'],
                         "testcase2" => $examInfo[$i]['testcase2'], "testcase2Ans" => $examInfo[$i]['testcase2Ans'], "answer" => $answers[$i]['answer'],
                         "score" => $scores[$i]['score'], "totalScore" => $totalScore, "totalPoints" => $points[$i]['totalPoints'], "studentAns1" => $scores[$i]['studentAns1'], "studentAns2" => $scores[$i]['studentAns2']);
    }
    return $info;
}

?>
