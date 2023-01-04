<?php

    //Rishik Danda - Group 7 - Middle End - RC

    $login_json = file_get_contents('php://input');
    $response = json_decode($login_json, true);
    $backendurl = "https://afsaccess4.njit.edu/~iac22/login.php";
    $requestID = $response['requestType'];
    switch ($requestID) {
        case 'login':
            $user = $response['user'];
            $pass = $response['pass'];
            $json_response = array('requestType' => $requestID, 'user' => $user, 'pass' => md5($pass));
            $login_response = curlurl($json_response, $backendurl);
            echo $login_response;
            break;
        
        case 'gradeExam':
            $ucid = $response['student'];
            $examId = $response['examName'];
            $json_response = array('requestType' => 'showTestCases', 'examName' => $examId, 'student' => $ucid);
            $grade_json = curlurl($json_response, $backendurl);
            $grade_response = json_decode($grade_json);
            $total_points_and_answers = array();
            for ($a = 0; $a < count($grade_response); $a++) {
                $testcase1 = $grade_response[$a]->testcase1;
                $testcase1Ans = $grade_response[$a]->testcase1Ans;
                $testcase2 = $grade_response[$a]->testcase2;
                $testcase2Ans = $grade_response[$a]->testcase2Ans;
                $testcase3 = $grade_response[$a]->testcase3;
                $testcase3Ans = $grade_response[$a]->testcase3Ans;
                $testcase4 = $grade_response[$a]->testcase4;
                $testcase4Ans = $grade_response[$a]->testcase4Ans;
                $testcase5 = $grade_response[$a]->testcase5;
                $testcase5Ans = $grade_response[$a]->testcase5Ans;
                $testcases = array($testcase1, $testcase2, $testcase3, $testcase4, $testcase5);
                $testcaseAns = array($testcase1Ans, $testcase2Ans, $testcase3Ans, $testcase4Ans, $testcase5Ans);
                $answer = $grade_response[$a]->answer;
                $point = $grade_response[$a]->points;
                $constraints = $grade_response[$a]->constraints;
                $converted_code = ltrim($testcase1);
                $correct_function_name = get_function_name($converted_code);
                $student_function_name = check_function_name($answer);
                array_push($total_points_and_answers, $correct_function_name);
                array_push($total_points_and_answers, $student_function_name);
                if ($student_function_name == $correct_function_name) {
                    array_push($total_points_and_answers, 5);
                } else {
                    array_push($total_points_and_answers, 0);
                }
                $point = $point - 5;
                switch ($constraints) {
                    case ('forLoops'):
                        $constraint = 'for';
                        if (strpos($answer, $constraint) !== false) {
                            array_push($total_points_and_answers, 5);
                        } else {
                            array_push($total_points_and_answers, 0);
                        }
                        $point = $point - 5;
                        break;
                    case ('whileLoops'):
                        $constraint = 'while';
                        if (strpos($answer, $constraint) !== false) {
                            array_push($total_points_and_answers, 5);
                        } else {
                            array_push($total_points_and_answers, 0);
                        }
                        $point = $point - 5;
                        break;
                    case ('recursion'):
                        $constraint = $student_function_name;
                        if (substr_count($answer, $constraint) > 1) {
                            array_push($total_points_and_answers, 5);
                        } else {
                            array_push($total_points_and_answers, 0);
                        }
                        $point = $point - 5;
                        break;
                    default:
                        array_push($total_points_and_answers, -1);
                }
                $numoftestcases = 0;
                for ($i = 0; $i < 5; $i++) {
                    if (!empty($testcases[$i])) {
                        $numoftestcases = $numoftestcases + 1;
                    }
                }
                for ($i = 0; $i < 5; $i++) {
                    $testcase = $testcases[$i];
                    $testcaseAnswer = $testcaseAns[$i];
                    if (!empty($testcase)) {
                        $output = grade_question($answer, get_parameter(ltrim($testcase)));
                        array_push($total_points_and_answers, $output);
                        if (compare_answer($output, $testcaseAnswer) == 'True') {
                            array_push($total_points_and_answers, round($point / $numoftestcases, 2));
                            //array_push($total_points_and_answers, $numoftestcases);
                        } else {
                            array_push($total_points_and_answers, 0);
                        }
                        array_push($total_points_and_answers, round($point / $numoftestcases, 2));
                    } else {
                        array_push($total_points_and_answers, -1);
                        array_push($total_points_and_answers, -1);
                        array_push($total_points_and_answers, -1);
                    }
                }
            }
            echo json_encode($total_points_and_answers);
            break;
        default:
            $response1 = curlurl($response, $backendurl);
            echo $response1;
            break;
    }

    function curlurl($arr, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arr));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function get_function_name($answer)
    {
        $split = preg_split("/\s+|\(|:/", $answer);
        $correct_function_name = $split[0];
        return $correct_function_name;
    }

    function get_parameter($answer)
    {
        $split_left = explode(")", $answer);
        $temp = $split_left[0];
        $split_right = explode("(", $temp);
        $answer_parameter = $split_right[1];
        $parameters = preg_replace("/\s/", "", $answer_parameter);
        return $parameters;
    }

    function check_function_name($answer)
    {
        $answer = ltrim($answer);
        $split = preg_split("/\s+|\(|:/", $answer);
        $answer_function_name = $split[1];
        return $answer_function_name;
    }

    function grade_question($answer, $testcase1)
    {
        $answer = ltrim($answer);
        $split = preg_split("/\s+|\(|:/", $answer);
        $answer_function_name = $split[1];
        $file = "test.py";
        file_put_contents($file, $answer . "\n" . 
    "print($answer_function_name($testcase1))");
        $runpython = exec("python test.py");
        return $runpython;
    }

    function compare_answer($student_answer, $correct_answer)
    {
        if ($student_answer == $correct_answer) {
            return "True";
        } else {
            if ($student_answer == "") {
                return "False Empty";
            } else {
                return "False";
            }
        }
    }
?>
