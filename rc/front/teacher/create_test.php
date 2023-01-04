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

<form method="POST" id="addToExamForm" class="container d-flex flex-row my-3">
    <div class="container overflow-auto ms-3" style="height:85vh;">
        <h1>Create Test</h1>
        <div class="mb-3">
            <label class="form-label" for="examName">
                Test Name
            </label>
            <input class="form-control" id="examNameBar" name="examName" type="text" placeholder="testname1">
        </div>
        <!-- <div class="form-check shadow py-4 px-5 mb-4 bg-body rounded" id='<?php echo $values->questionID; ?>'>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><span style="font-weight:bold;">Question: </span><?php echo $values->question; ?></li>
                <li class="list-group-item"><span style="font-weight:bold;">Difficulty: </span><?php echo $values->difficulty; ?></li>
                <li class="list-group-item"><span style="font-weight:bold;">Category: </span><?php echo $values->category; ?></li>
                <li class="list-group-item"> 
                    <div class="input-group" style="width:125px">
                        <span class="input-group-text">Points</span>
                        <input type="text" class="form-control" placeholder="0" name="points[]">
                    </div>
                </li>
            </ul>
        </div> -->
        <div id="questionsAdded">
        </div>
        <div>
            <button class="d-none btn btn-primary" id="createExam" name="submit" type="submit">Submit</button>
        </div>
    </div>
    <div class="container overflow-auto ms-3" style="height:85vh;">
        <h1>Question Bank</h1>
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
            <button class="btn btn-danger" type="button">Clear Filters</button>
        </div>
        <?php foreach ($response as $question => $values) : ?>
            <div class="form-check shadow py-4 px-5 mb-4 bg-body rounded" id='<?php echo $values->questionID; ?>'>
                <input class="form-check-input" type="checkbox" value='<?php echo $values->questionID; ?>' name="questionIDs[]">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><span style="font-weight:bold;">Question: </span><?php echo $values->question; ?></li>
                    <li class="list-group-item"><span style="font-weight:bold;">Difficulty: </span><?php echo $values->difficulty; ?></li>
                    <li class="list-group-item"><span style="font-weight:bold;">Category: </span><?php echo $values->category; ?></li>
                </ul>
            </div>
        <?php endforeach; ?>
        <div>
            <button class="btn btn-primary" id="addToExam" type="button">Add To Exam</button>
        </div>
    </div>
</form>


<?php
if (isset($_POST["questionIDs"]) and isset($_POST["points"])) {
    $questionIDs = $_POST["questionIDs"];
    $points = $_POST["points"];
    $examName = $_POST["examName"];
    $acceptable = true;
    $sum = 0;
    
    if (empty($examName)) {
        $acceptable = false;
    }

    for ($i = 0; $i < count($points); $i++) {
        if (empty($points[$i])) {
            $acceptable = false;
        }
    }

    if ($acceptable) {
        for ($i = 0; $i < count($points); $i++) {
            $sum += (int)$points[$i];
        }

        if ($sum > 100) {
            $acceptable = false;
        }
    }

    if ($acceptable) {
        $data2 = array("requestType" => "saveExam", "examName" => $examName, "questionIDs" => $questionIDs, "points" => $points);
        $response2 = curlRequest($data2);
    }
    else {
        echo "unacceptable";
    }
}
?>

<script>
    const filterDifficulty = document.querySelectorAll('.filterDifficulty')
    const filterDControl = document.getElementsByName('filterDifficulty')
    const filterCategory = document.querySelectorAll('.filterCategory')
    const filterCControl = document.getElementsByName('filterCategory')
    const clearFiltersButton = document.querySelector('#clearFilters')
    const search = document.querySelector('#search')
    const addToExamForm = document.querySelector('#addToExamForm')
    const addToExamButton = document.querySelector('#addToExam')
    const questionsAddedContainer = document.querySelector('#questionsAdded')
    const createExamButton = document.querySelector('#createExam')
    const examNameBar = document.querySelector('#examNameBar')
    const fullBank = <?php echo json_encode($response); ?>;
    let filteredBank = []
    let questionsAdded = []
    let filterD = ''
    let filterC = ''
    let examQuestionChecker = false

    function filterDOM() {
        if (filterD == '' && filterC == '' && search.value == '') {
            filteredBank = fullBank
        }

        for (let i = 0; i < fullBank.length; i++) {
            if (!filteredBank.includes(fullBank[i])) {
                document.getElementById(fullBank[i].questionID).classList.add('d-none')
            }
            else {
                document.getElementById(fullBank[i].questionID).classList.remove('d-none')
            }

            if (questionsAdded.includes(fullBank[i])) {
                document.getElementById(fullBank[i].questionID).classList.add('d-none')
            }
        }
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

    function addQuestion(question) {
        const info = ['Question: ', 'Difficulty: ', 'Category: ']
        let qDiv = document.createElement('div')
        qDiv.setAttribute('class', 'form-check shadow py-4 px-5 mb-4 bg-body rounded')
        qDiv.setAttribute('id', `qAdd${question.questionID}`)

        let ulist = document.createElement('ul')
        ulist.setAttribute('class', 'list-group list-group-flush')
        qDiv.appendChild(ulist)

        for (let i = 0; i < info.length; i++) {
            let listItem = document.createElement('li')
            listItem.setAttribute('class', 'list-group-item')
    
            let span = document.createElement('span')
            span.setAttribute('style', 'font-weight:bold;')
            span.innerText = info[i]
            listItem.appendChild(span)
            
            if (i == 0) {
                listItem.innerHTML += question.question
            }
            else if (i == 1) {
                listItem.innerHTML += question.difficulty
            }
            else {
                listItem.innerHTML += question.category
            }
            ulist.appendChild(listItem)
        }

        listItem = document.createElement('li')
        listItem.setAttribute('class', 'list-group-item')

        let pDiv = document.createElement('div')
        pDiv.setAttribute('class', 'input-group')
        pDiv.setAttribute('style', 'width:125px;')
        
        let pSpan = document.createElement('span')
        pSpan.setAttribute('class', 'input-group-text')
        pSpan.innerText = 'Points'
        pDiv.appendChild(pSpan)

        let input = document.createElement('input')
        input.setAttribute('type', 'text')
        input.setAttribute('class', 'form-control')
        input.setAttribute('placeholder', '0')
        input.setAttribute('name', 'points[]')
        pDiv.appendChild(input)

        listItem.appendChild(pDiv)
        ulist.appendChild(listItem)

        questionsAddedContainer.appendChild(qDiv)
    }

    function addToExam(questions) {
        let selectedExists = false
        for (let i = 0; i < questions.length; i++) {
            if (questions[i].checked && !questionsAdded.includes(fullBank[i])) {
                questionsAdded.push(fullBank[i])
                addQuestion(fullBank[i])
                selectedExists = true
            }
        }

        if (selectedExists) {
            filterDOM()
            examQuestionChecker = true
            if (examNameBar.value != '') {
                createExamButton.classList.remove('d-none')
            }
        }
    }
    
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

    addToExamButton.addEventListener('click', () => {
        addToExam(addToExamForm.elements['questionIDs[]'])
    })

    examNameBar.addEventListener('keyup', () => {
        if (examQuestionChecker) {
            createExamButton.classList.remove('d-none')
        }
    })
</script>