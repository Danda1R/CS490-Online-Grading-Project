<?php

    //Rishik Danda - Group 7 - Middle End - Beta

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
            $grade_response=json_decode($grade_json);
            $total_points_and_answers = array();
            for($i=0;$i<count($grade_response);$i++){
                $testcase1=$grade_response[$i]->testcase1;
                $testcase1Ans=$grade_response[$i]->testcase1Ans;
                $testcase2=$grade_response[$i]->testcase2;
                $testcase2Ans=$grade_response[$i]->testcase2Ans;
                $answer=$grade_response[$i]->answer;
                $point=$grade_response[$i]->points;
                $converted_code = ltrim($testcase1);
                $correct_function_name=get_function_name($converted_code);
                $parameter1=get_parameter($converted_code);
                $converted_code = ltrim($testcase2);
                $parameter2=get_parameter($converted_code);
                if(check_function_name($correct_function_name, $answer)=='False'){
                    if($point>5){
                        $point=$point-5;
                    }
                    else{
                        $point=0;
                    }
                }
                $output1=grade_question($answer, $parameter1);
                $output2=grade_question($answer, $parameter2);
                array_push($total_points_and_answers, $output1);
                array_push($total_points_and_answers, $output2);
                if(compare_answer($output1, $testcase1Ans)=='True' && 
                   compare_answer($output2, $testcase2Ans)=='True'){

                    array_push($total_points_and_answers, $point);
                }
                else{
                    array_push($total_points_and_answers, 0);
                }

            }
            echo json_encode($total_points_and_answers);
            break;
        
        default:
            $response1 = curlurl($response, $backendurl);
            echo $response1;
            break;
        
    }

    function curlurl($arr, $url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arr));
        $response = curl_exec($curl);
        curl_close ($curl);
        return $response;
    }

    function get_function_name($answer){

        $split = preg_split("/\s+|\(|:/", $answer);
        $correct_function_name = $split[0];
        return $correct_function_name;
    }

    function get_parameter($answer){
        $split_left = explode(")", $answer);
        $temp = $split_left[0];
        $split_right = explode("(", $temp);
        $answer_parameter = $split_right[1];
        $parameters = preg_replace("/\s/","", $answer_parameter);
        return $parameters;
    }

    function check_function_name($correct_function_name, $answer){
        $answer = ltrim($answer);
        $split = preg_split("/\s+|\(|:/", $answer);
        $answer_function_name = $split[1];
        if($answer_function_name == $correct_function_name){
             return 'True';
        }
        else{
            return 'False';
        }
    }

    function grade_question($answer, $testcase1){
        $answer = ltrim($answer);
        $split = preg_split("/\s+|\(|:/", $answer);
        $answer_function_name = $split[1];
        $file = "test.py";
        file_put_contents($file, $answer . "\n" . "print($answer_function_name($testcase1))");
        $runpython = exec("python test.py");
        return $runpython;
    }

    function compare_answer($student_answer, $correct_answer){
        if ($student_answer == $correct_answer){
            return "True";
       }
       else{
            if($student_answer == ""){
                 return "False Empty";
            }
            else{
                 return "False";
            }
        }
    }
?>
