<?php
#Ian Church-Krafte - CS490101 - backend - RV version
$post = file_get_contents('php://input');
$json = json_decode($post, true);

$servername = 'sql1.njit.edu';
$username = 'iac22';
$password = ''; #sql password

$conn = new mysqli($servername, $username, $password, 'iac22');
if($conn->connect_error){
    echo json_encode("cant connect");
    die("Connection failed: " . $conn->connect_error);
}
$purpose = $json['requestType'];

switch($purpose){
    case "login":
        $user = $json['user'];
        $pass = $json['pass'];
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
    case "insertBank":
        $question = $json['question'];
        $testCases = $json['testcases'];
        $testCaseAns = $json['testcaseAns'];
        $difficulty = $json['difficulty'];
        $category = $json['category'];
        $constraints = $json['constraint'];

        $result = db_insertQuestion($question, $testCases, $testCaseAns, $difficulty, $category, $constraints, $conn);
        echo json_encode($result);
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
    case "showExamStudent": #showing the saved exam given the exam name to the student, questions
        $examName = $json['examName'];
        $result = db_showExamStudent($examName, $conn);
        #result is an array, each row being a row in the table
        echo json_encode($result);
        break;
    case "saveStudentExam":
        $examName = $json['examName'];
        $student = $json['student'];
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
    case "listAutoGradedExams":
        $result = listAutoGradedExams($conn);
        echo json_encode($result);
        break;
    case "saveAutoGrade":
        $examName = $json['examName'];
        $scores = $json['scores'];
        $student = $json['student'];
        $studentAns = $json['studentAns'];
        $testcasePoints = $json['testcasePoints'];
        $testcaseTotal = $json['testcaseTotal'];
        $actualFunctionName = $json['actualFunctionName'];
        $studentFunctionName = $json['studentFunctionName'];
        $functionScore = $json['functionScore'];
        $constraintScore = $json['constraintScore'];

        $result = saveAutoGrade($examName, $scores, $student, $studentAns, $testcasePoints, $actualFunctionName, $studentFunctionName, $functionScore, $constraintScore, $testcaseTotal,  $conn);
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
        $result = showTestCases($examName, $student, $conn);
        echo json_encode($result);
        break;
    case "listGradedExamsFinal":
        $student = $json['student'];
        $result = listGradedExamsFinal($student, $conn);
        echo json_encode($result);
        break;
    case "saveFinal":
        $examName = $json['examName'];
        $student = $json['student'];
        $bigArray = $json['bigArray'];
        $comments = $json['comments'];
        $result = saveFinal($examName, $student, $bigArray, $comments, $conn);
        echo json_encode($result);
        break;
    
    case "showFinal":
        $examName = $json['examName'];
        $student = $json['student'];
        $result = showFinal($examName, $student, $conn);
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

function db_insertQuestion($question, $testcases, $testcaseAns, $difficulty, $category, $constraint, $conn){
    $sql = "INSERT INTO questionBankNew (question, testcase1, testcase1Ans, testcase2, testcase2Ans, testcase3, testcase3Ans,
            testcase4, testcase4Ans, testcase5, testcase5Ans, difficulty, category, constraints) VALUES ('".
            $question."', '". $testcases[0]."', '".$testcaseAns[0]."', '".$testcases[1]."', '".$testcaseAns[1]."', '".
            $testcases[2]."', '".$testcaseAns[2]."', '".$testcases[3]."', '".$testcaseAns[3]."', '".$testcases[4]."', '".
            $testcaseAns[4]."', '".$difficulty."', '".$category."', '".$constraint."')";
    $result = $conn->query($sql);
    if ($result == FALSE){
        return "insert_failed";
    }
    return 'insert_successfull';
}


function db_showBank($conn){
    $sql = "SELECT * FROM `questionBankNew`";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    $i=0;
    if($n > 0){
        while($row = $result->fetch_assoc()){
            $bank[] = array("questionID" => $row["id"], "question" => $row["question"], "testcase1" => $row["testcase1"], "testcase1Ans" => $row["testcase1Ans"], 
                            "testcase2" => $row["testcase2"], "testcase2Ans" => $row["testcase2Ans"], "testcase3" => $row['testcase3'], "testcase3Ans" => $row["testcase3Ans"],
                            "testcase4" => $row["testcase4"], "testcase4Ans" =>$row["testcase4Ans"], "testcase5" => $row["testcase5"], "testcase5Ans" => $row["testcase5Ans"],
                            "difficulty" => $row["difficulty"], "category" => $row["category"], "constraint" => $row['constraints']);
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
    $conn->query("CREATE TABLE ". $examName ." (id int, question varchar(255), testcase1 varchar(255), testcase1Ans varchar(255), testcase2 varchar(255), testcase2Ans varchar(255), 
                                                testcase3 varchar(255), testcase3Ans varchar(255), testcase4 varchar(255), testcase4Ans varchar(255), testcase5 varchar(255), testcase5Ans varchar(255),
                                                difficulty varchar(255), category varchar(255), constraints varchar(255), points int)");
    for($i=0; $i<count($questionIDs); $i++){
        $sql = "SELECT * FROM questionBankNew WHERE id='". $questionIDs[$i] ."'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $exam[] = array("id" => $questionIDs[$i], "question" => $row["question"], "testcase1" => $row["testcase1"], "testcase1Ans" => $row["testcase1Ans"], 
                        "testcase2" => $row["testcase2"], "testcase2Ans" => $row["testcase2Ans"], "testcase3" => $row["testcase3"], "testcase3Ans" => $row["testcase3Ans"],
                        "testcase4" => $row['testcase4'], "testcase4Ans" => $row["testcase4Ans"], "testcase5" => $row["testcase5"], "testcase5Ans" => $row["testcase5Ans"],
                        "difficulty" => $row["difficulty"], "category" => $row["category"], "constraint" => $row['constraints']);
    }
    for($i=0; $i<count($questionIDs); $i++){
        $sql = "INSERT INTO ". $examName ." VALUES (".$questionIDs[$i].", '".$exam[$i]["question"]."', '".$exam[$i]["testcase1"]."', '".$exam[$i]['testcase1Ans']."', '".$exam[$i]["testcase2"]
                ."', '".$exam[$i]["testcase2Ans"]."', '".$exam[$i]["testcase3"]."', '".$exam[$i]["testcase3Ans"]."', '".$exam[$i]["testcase4"]."', '".$exam[$i]["testcase4Ans"]
                ."', '".$exam[$i]["testcase5"]."', '".$exam[$i]["testcase5Ans"]."', '".$exam[$i]["difficulty"]."', '".$exam[$i]["category"]."', '".$exam[$i]["constraint"]."', '".$points[$i]."')";
        $result = $conn->query($sql);
    }
    $sql = "SELECT examName FROM examList WHERE examName='".$examName."'";
    $result = $conn->query($sql);
    if($result->num_rows == 0){#NOT IN THE LIST OF EXAMS
        $sql = "INSERT INTO examList (examName) VALUES('".$examName."')";
        $conn->query($sql);
    }
    if ($result == FALSE){
        return "saveExam_failed";
    }

    return "saveExam_successfull";
}

function db_showExamStudent($examName, $conn){
    $sql = "SELECT question, points FROM ".$examName;
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $exam[] = array("question" => $row['question'], "points" => $row['points']);
    }
    return $exam;
}

function showExamList($conn){
    $sql = "SELECT examName FROM examList";
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
    $conn->query("DROP TABLE IF EXISTS ".$examName.$student);
    $sql = "CREATE TABLE ".$examName.$student." (question varchar(255), answer varchar(255))";
    $conn->query($sql);
    $result = $conn->query("SELECT question FROM ".$examName);
    $i =0;
    while($row = $result->fetch_assoc()){
        $sql = "INSERT INTO ".$examName.$student." VALUES('".$row['question']."', '".$answers[$i]."')";
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
    $conn->query($sql);
    $conn->query("CREATE TABLE ". $examName ."Results".$student."Final (question varchar(255), answer varchar(255), score varchar(255), comment varchar(255), student varchar(255))");
    
    $sql = "SELECT question, answer FROM ".$examName.$student;
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
    $result = $conn->query($sql);
    if($result->num_rows == 0){
        $sql = "INSERT INTO gradedExamsFinal VALUES('".$examName."', '".$student."')";
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
        return "empty_gradedExamsFinal_in_listGradedExamsFinal";
    }
    while($row = $result->fetch_assoc()){
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
    $list = showTestCases($examName, $student, $conn);

    $sql = "SELECT question, answer, score, comment FROM ". $examName ."Results". $student ."Final WHERE student='". $student ."'";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if ($n>0){
        while($row = $result->fetch_assoc()){
            $examResults[] = array("question" => $row['question'], "answer" => $row['answer'], "score" => $row['score'], "comment" => $row['comment']);
        }
    }
    else{
        return "showResults_failed";
    }
    for($i=0; $i<count($list); $i++){
        $results[] = array("question" => $examResults[i]['question'], "answer" => $examResults[i]['answer'], "questionScore" => $examResults[i]['score'], "comment" => $examResults[i]['comment'],
                           "testcase1" => $list[i]['testcase1'], "testcase1Ans" => $list[i]['testcase1Ans'], "testcase1Score" => $list[i]['testcase1Score'],
                           "testcase2" => $list[i]['testcase2'], "testcase2Ans" => $list[i]['testcase2Ans'], "testcase2Score" => $list[i]['testcase2Score'],
                           "testcase3" => $list[i]['testcase3'], "testcase3Ans" => $list[i]['testcase3Ans'], "testcase3Score" => $list[i]['testcase3Score'],
                           "testcase4" => $list[i]['testcase4'], "testcase4Ans" => $list[i]['testcase4Ans'], "testcase4Score" => $list[i]['testcase4Score'],
                           "testcase5" => $list[i]['testcase5'], "testcase5Ans" => $list[i]['testcase5Ans'], "testcase5Score" => $list[i]['testcase5Score']);
    }
    return $results;
}

function listSubmittedExams($conn){
    $sql = "SELECT * FROM submittedExams";

    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $list[]=array("student" => $row['student'], "examName" => $row['examName']);
    }
    return $list;
}

function showTestCases($examName, $student, $conn){ //it works
    $conn->query("DROP TABLE IF EXISTS testcasesAndAnswers".$examName);
    $conn->query("CREATE TABLE testcasesAndAnswers".$examName."(testcase1 varchar(255), testcase1Ans varchar(255), testcase2 varchar(255), testcase2Ans varchar(255), 
                 testcase3 varchar(255), testcase3Ans varchar(255), testcase4 varchar(255), tetscase4Ans varchar(255), testcase5 varchar(255), testcase5Ans varchar(255),
                 answer varchar(255), points varchar(255), constraints varchar(255))");
    
    $sql = "SELECT testcase1, testcase1Ans, testcase2, testcase2Ans, testcase3, testcase3Ans, 
            testcase4, testcase4Ans, testcase5, testcase5Ans, constraints FROM ".$examName;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_testcases";
    }
    while($row = $result->fetch_assoc()){
        $testcases[] = array("testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'], "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans'],
                             "testcase3" => $row['testcase3'], "testcase3Ans" => $row['testcase3Ans'], "testcase4" => $row['testcase4'], "testcase4Ans" => $row['testcase4Ans'],
                             "testcase5" => $row['testcase5'], "testcase5Ans" => $row['testcase5Ans'], "constraints" => $row['constraints']);
    }
    
    $sql = "SELECT answer FROM ".$examName.$student;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_showAnswers_showTestCases";
    }
    while($row = $result->fetch_assoc()){
        $answers[] = array("answer" => $row['answer']);
    }
    $sql = "SELECT points FROM ".$examName;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty_points_in_exam_showTestCase";
    }
    while($row = $result->fetch_assoc()){
        $points[] = array("points" => $row['points']);
    }
    for($i=0; $i<$n; $i++){
        $sql = "INSERT INTO testcasesAndAnswers".$examName." VALUES('".$testcases[$i]['testcase1']."', '".$testcases[$i]['testcase1Ans']."', '".$testcases[$i]['testcase2']."', '".$testcases[$i]['testcase2Ans']."', '".
                $testcases[$i]['testcase3']."', '".$testcases[$i]['testcase3Ans']."', '".$testcases[$i]['testcase4']."', '".$testcases[$i]['testcase4Ans']."', '".$testcases[$i]['testcase5']."', '".$testcases[$i]['testcase5Ans']."', '".
                $answers[$i]['answer']."', '".$points[$i]['points']."', '".$testcases[$i]['constraints']."')";
        $conn->query($sql);
    }
    
    $sql = "SELECT * FROM testcasesAndAnswers".$examName;
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $list[] = array("testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'], "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans'], 
                        "testcase3" => $row['testcase3'], "testcase3Ans" => $row['testcase3Ans'], "testcase4" => $row['testcase4'], "testcase4Ans" => $row['tetscase4Ans'], 
                        "testcase5" => $row['testcase5'], "testcase5Ans" => $row['testcase5Ans'],
                        "answer" => $row['answer'], "points" => $row['points'], "constraints" => $row['constraints']);
    }

    return $list;
}

function listAutoGradedExams($conn){
    $sql = "SELECT * FROM autoGradeList";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return 'empty autoGradeList';
    }
    while($row = $result->fetch_assoc()){
        $list[] = array("examName" => $row['examName'], "student" => $row['student']);
    }
    return $list;
}

function saveAutoGrade($examName, $scores, $student, $studentAns, $testcasePoints, $actualFunctionName, $studentFunctionName, $functionScore, $constraintScore, $testcaseTotal, $conn){
    $sql = "SELECT examName, student FROM autoGradeList WHERE examName='".$examName."' AND student='".$student."'";
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        $sql = "INSERT INTO autoGradeList VALUES('".$examName."', '".$student."')";
        $conn->query($sql);
    } 
    
    $sql = "DROP TABLE IF EXISTS ".$examName."autoGrade".$student;
    $conn->query($sql);

    $sql = "CREATE TABLE ".$examName."autoGrade".$student." (question varchar(255), 
            studentAns1 varchar(255), testcase1Points varchar(255), studentAns2 varchar(255), testcase2Points varchar(255),
            studentAns3 varchar(255), testcase3Points varchar(255), studentAns4 varchar(255), testcase4Points varchar(255),
            studentAns5 varchar(255), testcase5Points varchar(255), funName varchar(255), studentFunName varchar(255), 
            functionScore varchar(255), constraintScore varchar(255), testcaseTotal varchar(255), score int)";
    $conn->query($sql);

    $sql = "SELECT question FROM ".$examName;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty exam in saveAutoGrade";
    }
    while($row = $result->fetch_assoc()){
        $questions[] = array("question" => $row['question']);
    }
    
    for($i=0; $i<count($questions); $i++){
        $sql = "INSERT INTO ".$examName."autoGrade".$student." VALUES('".$questions[$i]['question']."', '".$studentAns[$i][0]."', '".$testcasePoints[$i][0]."', '".
                $studentAns[$i][1]."', '".$testcasePoints[$i][1]."', '".$studentAns[$i][2]."', '".$testcasePoints[$i][2]."', '".
                $studentAns[$i][3]."', '".$testcasePoints[$i][3]."', '".$studentAns[$i][4]."', '".$testcasePoints[$i][4]."', '".
                $actualFunctionName[$i]."', '".$studentFunctionName[$i]."', '".$functionScore[$i]."', '".$constraintScore[$i]."', '".$testcaseTotal[$i]."', '".$scores[$i]."')";
        $conn->query($sql);
    }
    return "saveAutoGrade_successfull";
}

function showAutoGrade($examName, $student, $conn){ //question, testcases, testcaseAns, student answer, autograde score, total points
    $sql = "SELECT question, testcase1, testcase1Ans, testcase2, testcase2Ans, testcase3, testcase3Ans, testcase4, testcase4Ans, testcase5, testcase5Ans FROM ".$examName;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return "empty Exam in showAutoGrade";
    }
    while($row = $result->fetch_assoc()){
        $examInfo[] = array("question" => $row['question'], "testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'],
                            "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans'], "testcase3" => $row['testcase3'],
                            "testcase3Ans" => $row['testcase3Ans'], "testcase4" => $row['testcase4'], "testcase4Ans" => $row['testcase4Ans'],
                            "testcase5" => $row['testcase5'], "testcase5Ans" => $row['testcase5Ans']);
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

    $sql = "SELECT score, studentAns1, testcase1Points, studentAns2, testcase2Points, 
                          studentAns3, testcase3Points, studentAns4, testcase4Points, 
                          studentAns5, testcase5Points, funName, studentFunName, constraintScore, testcaseTotal, functionScore FROM ".$examName."autoGrade".$student;
    $result = $conn->query($sql);
    $n = $result->num_rows;
    if($n == 0){
        return 'empty autoGradedExam in showAutoGrade';
    }
    while($row = $result->fetch_assoc()){
        $scores[] = array("score" => $row['score'], "studentAns1" => $row['studentAns1'], "testcase1Points" => $row['testcase1Points'], "studentAns2" => $row['studentAns2'], "testcase2Points" => $row['testcase2Points'],
                          "studentAns3" => $row['studentAns3'], "testcase3Points" => $row['testcase3Points'], "studentAns4" => $row['studentAns4'], "testcase4Points" => $row['testcase4Points'],
                          "studentAns5" => $row['studentAns5'], "testcase5Points" => $row['testcase5Points'], 
                          "funName" => $row['funName'], "studentFunName" => $row['studentFunName'], "functionScore" => $row['functionScore'], "constraintScore" => $row['constraintScore'], "testcaseTotal" => $row['testcaseTotal']);
    }
    $totalScore = 0;
    for($i=0; $i<count($scores); $i++){
        $totalScore += $scores[$i]['score'];
    }
    
    $sql = "SELECT points, constraints FROM ".$examName;
    $result = $conn->query($sql);
    if($n == 0){
        return "empty points in ".$examName;
    }
    while($row = $result->fetch_assoc()){
        $points[] = array("totalPoints" => $row['points'], "constraint" => $row['constraints']);
    }

    for($i=0; $i<count($answers); $i++){
        $info[] =  array("question" => $examInfo[$i]['question'], "testcase1" => $examInfo[$i]['testcase1'], "testcase1Ans" => $examInfo[$i]['testcase1Ans'], "testcase1Points" => $scores[$i]['testcase1Points'],
                         "testcase2" => $examInfo[$i]['testcase2'], "testcase2Ans" => $examInfo[$i]['testcase2Ans'], "testcase2Points" => $scores[$i]['testcase2Points'], 
                         "testcase3" => $examInfo[$i]['testcase3'], "testcase3Ans" => $examInfo[$i]['testcase3Ans'], "testcase3Points" => $scores[$i]['testcase3Points'],
                         "testcase4" => $examInfo[$i]['testcase4'], "testcase4Ans" => $examInfo[$i]['testcase4Ans'], "testcase4Points" => $scores[$i]['testcase4Points'],
                         "testcase5" => $examInfo[$i]['testcase5'], "testcase5Ans" => $examInfo[$i]['testcase5Ans'], "testcase5Points" => $scores[$i]['testcase5Points'],
                         "answer" => $answers[$i]['answer'], "score" => $scores[$i]['score'], "totalScore" => $totalScore, "totalPoints" => $points[$i]['totalPoints'], 
                         "studentAns1" => $scores[$i]['studentAns1'], "studentAns2" => $scores[$i]['studentAns2'], "studentAns3" => $scores[$i]['studentAns3'], 
                         "studentAns4" => $scores[$i]['studentAns4'], "studentAns5" => $scores[$i]['studentAns5'],
                         "actualFunctionName" => $scores[$i]['funName'], "studentFunctionName" => $scores[$i]['studentFunName'], 
                         "functionScore" => $scores[$i]['functionScore'], "constraintScore" => $scores[$i]['constraintScore'], "testcaseTotal" => $scores[$i]['testcaseTotal'], "constraint" => $points[$i]['constraint']);
    }
    return $info;
}

function saveFinal($examName, $student, $bigArray, $comments, $conn){
    $sql = "DROP TABLE IF EXISTS ".$examName."ResultsFinal".$student;
    $conn->query($sql);

    $sql = "CREATE TABLE ".$examName."ResultsFinal".$student." (question varchar(255), answer varchar(255), 
            testcase1 varchar(255), testcase1Ans varchar(255), testcase1Points varchar(255), studentAns1 varchar(255),
            testcase2 varchar(255), testcase2Ans varchar(255), testcase2Points varchar(255), studentAns2 varchar(255),
            testcase3 varchar(255), testcase3Ans varchar(255), testcase3Points varchar(255), studentAns3 varchar(255),
            testcase4 varchar(255), testcase4Ans varchar(255), testcase4Points varchar(255), studentAns4 varchar(255),
            testcase5 varchar(255), testcase5Ans varchar(255), testcase5Points varchar(255), studentAns5 varchar(255),
            testcaseTotal varchar(255), actualFunctionName varchar(255), studentFunctionName varchar(255), functionScore varchar(255), 
            constraintScore varchar(255), constraints varchar(255), score varchar(255), totalScore varchar(255), totalPoints varchar(255), comment varchar(255))";

    $conn->query($sql);

    for($i=0; $i<count($bigArray); $i++){
        $sql = "INSERT INTO ".$examName."ResultsFinal".$student." VALUES ('".$bigArray[$i]['question']."', '".$bigArray[$i]['answer']."', '".
                $bigArray[$i]['testcase1']."', '".$bigArray[$i]['testcase1Ans']."', '".$bigArray[$i]['testcase1Points']."', '".$bigArray[$i]['studentAns1']."', '".
                $bigArray[$i]['testcase2']."', '".$bigArray[$i]['testcase2Ans']."', '".$bigArray[$i]['testcase2Points']."', '".$bigArray[$i]['studentAns2']."', '".
                $bigArray[$i]['testcase3']."', '".$bigArray[$i]['testcase3Ans']."', '".$bigArray[$i]['testcase3Points']."', '".$bigArray[$i]['studentAns3']."', '".
                $bigArray[$i]['testcase4']."', '".$bigArray[$i]['testcase4Ans']."', '".$bigArray[$i]['testcase4Points']."', '".$bigArray[$i]['studentAns4']."', '".
                $bigArray[$i]['testcase5']."', '".$bigArray[$i]['testcase5Ans']."', '".$bigArray[$i]['testcase5Points']."', '".$bigArray[$i]['studentAns5']."', '".
                $bigArray[$i]['testcaseTotal']."', '".$bigArray[$i]['actualFunctionName']."', '".$bigArray[$i]['studentFunctionName']."', '".$bigArray[$i]['functionScore']."', '".
                $bigArray[$i]['constraintScore']."', '".$bigArray[$i]['constraint']."', '".$bigArray[$i]['score']."', '".$bigArray[$i]['totalScore']."', '".
                $bigArray[$i]['totalPoints']."', '".$comments[$i]."')";

        $conn->query($sql);
    }
}

function showFinal($examName, $student, $conn){
    $sql = "SELECT * FROM ".$examName."ResultsFinal".$student;
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()){
        $bigArray[] = array("question" => $row['question'], "answer" => $row['answer'],
                          "testcase1" => $row['testcase1'], "testcase1Ans" => $row['testcase1Ans'], "testcase1Points" => $row['testcase1Points'], "studentAns1" => $row['studentAns1'],
                          "testcase2" => $row['testcase2'], "testcase2Ans" => $row['testcase2Ans'], "testcase2Points" => $row['testcase2Points'], "studentAns2" => $row['studentAns2'],
                          "testcase3" => $row['testcase3'], "testcase3Ans" => $row['testcase3Ans'], "testcase3Points" => $row['testcase3Points'], "studentAns3" => $row['studentAns3'],
                          "testcase4" => $row['testcase4'], "testcase4Ans" => $row['testcase4Ans'], "testcase4Points" => $row['testcase4Points'], "studentAns4" => $row['studentAns4'],
                          "testcase5" => $row['testcase5'], "testcase5Ans" => $row['testcase5Ans'], "testcase5Points" => $row['testcase5Points'], "studentAns5" => $row['studentAns5'],
                          "testcaseTotal" => $row['testcaseTotal'], "actualFunctionName" => $row['actualFunctionName'], "studentFunctionName" => $row['studentFunctionName'],
                          "functionScore" => $row['functionScore'], "constraintScore" => $row['constraintScore'], "constraint" => $row['constraints'], "score" => $row['score'],
                          "totalScore" => $row['totalScore'], "totalPoints" => $row['totalPoints'], "comment" => $row['comment']);
    }
    return $bigArray;
}

?>
