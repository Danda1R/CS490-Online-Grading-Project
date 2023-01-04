<?php
require(__DIR__ . "/../nav.php");

if ($role != "teacher") {
    redirect("");
}

$data = array("requestType" => "showBank");
$response = curlRequest($data);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<div class="container d-flex flex-row my-3">
    <div class="container overflow-auto me-3" style="height:85vh;">
        <h1 class="mb-3">Create Question</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label" for="question">Question</label>
                <textarea class="form-control" name="question" id="question"></textarea>
            </div>
            <label class="form-label">Test Cases</label>
            <div class="form-group mb-3" style="width:400px;">
                <div id="testCases">
                    <div class="input-group">
                        <input type="text" class="form-control" name="inputs[]" id="input1" placeholder="input">
                        <input type="text" class="form-control" name="outputs[]" id="output1" placeholder="output">
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" name="inputs[]" id="input2" placeholder="input">
                        <input type="text" class="form-control" name="outputs[]" id="output2" placeholder="output">
                    </div>
                </div> 
                <button type="button" class="btn btn-secondary mt-2" id="addTestCaseButton">Add Test Case</button>
            </div>
            <label class="form-label">Difficulty</label>
            <div class="mb-3">
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
            <label class="form-label">Category</label>
            <div class="mb-3">
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
                    <input class="form-check-input" type="radio" name="category" id="recursion" value="recursion">
                    <label class="form-check-label" for="recursion">
                        Recursion
                    </label>
                </div>
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
                    <input class="form-check-input" type="radio" name="category" id="lists" value="lists">
                    <label class="form-check-label" for="lists">
                        Lists
                    </label>
                </div>
            </div>
            <label class="form-label">Constraints</label>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="constraint" id="constraintFor" value="forLoops">
                    <label class="form-check-label" for="constraintFor">
                        For Loops
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="constraint" id="constraintWhile" value="whileLoops">
                    <label class="form-check-label" for="constraintWhile">
                        While Loops
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="constraint" id="constraintRecursion" value="recursion">
                    <label class="form-check-label" for="constraintRecursion">
                        Recursion
                    </label>
                </div>
            </div>
            <button class="btn btn-primary" name="submit" type="submit">Submit</button>
        </form>
    </div>
    <div class="container overflow-auto ms-3" style="height:85vh;">
        <h1>Question Bank</h1>
        <form method="POST">
        <div class="form-group mb-3">
            <label class="form-label" for="search">
                Search
            </label>
            <input class="form-control" name="search" id="search" type="text" placeholder="keywords">
        </div>
        <div class="d-flex flex-row justify-content-between mb-3">
            <div class="d-flex flex-column">
                <label class="form-label">Difficulty</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" id="filterEasy" name="filterDifficulty" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterDifficulty" for="filterEasy" value="easy">Easy</label>        
                
                    <input type="radio" class="btn-check" id="filterMedium" name="filterDifficulty" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterDifficulty" for="filterMedium" value="medium">Medium</label>
                           
                    <input type="radio" class="btn-check" id="filterHard" name="filterDifficulty" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterDifficulty" for="filterHard" value="hard">Hard</label>          
                </div>
            </div>
            <div class="d-flex flex-column">
                <label class="form-label">Category</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" id="filterFor" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterFor" value="forLoops">For</label>  
                
                    <input type="radio" class="btn-check" id="filterWhile" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterWhile" value="whileLoops">While</label>
                
                    <input type="radio" class="btn-check" id="filterRecursion" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterRecursion" value="recursion">Recursion</label>
                
                    <input type="radio" class="btn-check" id="filterVariables" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterVariables" value="variables">Variables</label>
                
                    <input type="radio" class="btn-check" id="filterConditionals" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterConditionals" value="conditionals">Conditionals</label>
                
                    <input type="radio" class="btn-check" id="filterLists" name="filterCategory" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm filterCategory" for="filterLists" value="lists">Lists</label>
                </div>
            </div>
        </div>
        <div class="d-none d-grid my-3" id="clearFilters">
            <button type="button" class="btn btn-danger">Clear Filters</button>
        </div>
        <?php foreach ($response as $question => $values) : ?>
            <div class="form-check shadow py-4 px-5 mb-4 bg-body rounded" id='<?php echo $values->questionID; ?>'>
                <!-- <input class="form-check-input" type="checkbox" value='<?php echo $values->questionID; ?>' name="questionIDs[]"> -->
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><span style="font-weight:bold;">Question: </span><?php echo $values->question; ?></li>
                    <li class="list-group-item"><span style="font-weight:bold;">Difficulty: </span><?php echo $values->difficulty; ?></li>
                    <li class="list-group-item"><span style="font-weight:bold;">Category: </span><?php echo $values->category; ?></li>
                    <!-- <li class="list-group-item"> 
                        <div class="input-group" style="width:125px">
                            <span class="input-group-text">Points</span>
                            <input type="text" class="form-control" placeholder="0" name="points[]">
                        </div>
                    </li> -->
                </ul>
            </div>
        <?php endforeach; ?>
        <!-- <div>
            <button class="btn btn-danger" name="submit" type="submit">Delete</button>
        </div> -->
    </form>
    </div>
</div>

<?php
$question = $_POST["question"];
$inputs = $_POST["inputs"];
$outputs = $_POST["outputs"];
$difficulty = $_POST["difficulty"];
$category = $_POST["category"];
$constraint = $_POST["constraint"];

if (!empty($question) and !empty($inputs[0]) and !empty($inputs[1]) and !empty($outputs[0]) and !empty($outputs[1]) and isset($difficulty) and isset($category)) {
    if (!isset($constraint)) {
        $constraint = "none";
    }
    
    while (count($inputs) != 5) {
        array_push($inputs, '');
    }

    while (count($outputs) != 5) {
        array_push($outputs, '');
    }

    $data2 = array("requestType" => "insertBank", "question" => $question, "testcases" => $inputs, "testcaseAns" => $outputs, "difficulty" => $difficulty, "category" => $category, "constraint" => $constraint);
    
    $response2 = curlRequest($data2);
    redirect("teacher/create_question.php");
}
?>

<script>
    const testCases = document.getElementById('testCases')
    const addTestCaseButton = document.getElementById('addTestCaseButton')
    const filterDifficulty = document.querySelectorAll('.filterDifficulty')
    const filterDControl = document.getElementsByName('filterDifficulty')
    const filterCategory = document.querySelectorAll('.filterCategory')
    const filterCControl = document.getElementsByName('filterCategory')
    const clearFiltersButton = document.querySelector('#clearFilters')
    const search = document.querySelector('#search')
    const fullBank = <?php echo json_encode($response); ?>;
    let filteredBank = []
    let filterD = ''
    let filterC = ''
    let count = 3

    function addTestCase(id) {
        let caseDiv = document.createElement('div')
        caseDiv.setAttribute('class', 'input-group')

        let input = document.createElement('input')
        input.setAttribute('type', 'text')
        input.setAttribute('class', 'form-control')
        input.setAttribute('name', `inputs[]`)
        input.setAttribute('id', `input${id}`)
        input.setAttribute('placeholder', 'input')
        caseDiv.appendChild(input)

        let output = document.createElement('input')
        output.setAttribute('type', 'text')
        output.setAttribute('class', 'form-control')
        output.setAttribute('name', `outputs[]`)
        output.setAttribute('id', `output${id}`)
        output.setAttribute('placeholder', 'output')
        caseDiv.appendChild(output)

        testCases.appendChild(caseDiv)
    }

    function clearFilters() {
        filteredBank = []
        filterD = ''
        filterC = ''
        search.value = ''

        for (let i = 0; i < filterDControl.length; i++) {
            filterDControl[i].checked = false
        }

        for (let i = 0; i < filterCControl.length; i++) {
            filterCControl[i].checked = false
        }

        clearFiltersButton.classList.add('d-none')
        
        for (let i = 0; i < fullBank.length; i++) {
            filteredBank.push(fullBank[i])
        }

        filterDOM()
    }

    function filterDOM() {
        for (let i = 0; i < fullBank.length; i++) {
            if (!filteredBank.includes(fullBank[i])) {
                document.getElementById(fullBank[i].questionID).classList.add('d-none')
            }
            else {
                document.getElementById(fullBank[i].questionID).classList.remove('d-none')
            }
        }
    }

    function filterQuestions() {
        filteredBank = []
        
        for (let i = 0; i < fullBank.length; i++) {
            if ((filterD == fullBank[i].difficulty) && (filterD != '')) {
                filteredBank.push(fullBank[i])
            }
            else if ((filterC == fullBank[i].category) && (filterC != '') && (filterD == '')) {
                filteredBank.push(fullBank[i])
            }
            else if ((fullBank[i].question.includes(search.value)) && (search.value != '') && (filterC == '') && (filterD == '')) {
                filteredBank.push(fullBank[i])
            }
        }

        if (filterD != '') {
            filteredBank = filteredBank.filter(question => question.difficulty == filterD)
        }

        if (filterC != '') {
            filteredBank = filteredBank.filter(question => question.category == filterC)
        }

        if (search.value != '') {
            filteredBank = filteredBank.filter(question => question.question.includes(search.value))
        }

        filterDOM()
        clearFiltersButton.classList.remove('d-none')
    }

    addTestCaseButton.addEventListener('click', () => {
        if (count < 6) {
            addTestCase(count)
        }
        count++
    })
    
    filterDifficulty.forEach((item) => {
        item.addEventListener('click', () => {
            filterD = item.attributes[2].value
            filterQuestions()
        })
    })

    filterCategory.forEach((item) => {
        item.addEventListener('click', () => {
            filterC = item.attributes[2].value
            filterQuestions()
        })
    })

    search.addEventListener('keyup', () => {
        filterQuestions()
    }) 

    clearFiltersButton.addEventListener('click', () => {
        clearFilters()
    })
</script>