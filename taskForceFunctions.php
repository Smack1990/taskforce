<?php
$filename = "task.json";

// skapa en uppgift
function create($task)
{
    if (empty($task)) {
        return;
    }
    $todoList = read();
    $todoList[] = [
        "task" => $task,
        "done" => false,
        "created_at" => date("Y-m-d H:i:s"),
        "updated_at" => null
    ]; //assosiativ array med alla funktioner
    save($todoList);
}

// spara todo
function save($todoList)
{
    global $filename;
    //Lägg till fil, variabel, omkoda till json(variabel som skall läggas till, jsonformat)
    file_put_contents($filename, json_encode($todoList, JSON_PRETTY_PRINT));
}


//Läsa fil
function read()
{
    global $filename;
    //om filen inte finns returnera tom array.
    if (!file_exists($filename)) {
        return [];
    }
    //spara content från filnamn i variable
    $data = file_get_contents($filename);
    //returnera avkodad json 
    return json_decode($data, true) ?? [];
}

//radera ($valt index)
function delete($index)
{
    $todoList = read(); //läs in fil med read()
    $deleted = "papperskorg.json";

    if (isset($todoList[$index])) {
        if(file_exists($deleted)){
            $deletedTask = json_decode(file_get_contents($deleted), true);
        }else{
            $deletedTask = [];
        }
        $todoList[$index]['deleted_at'] = date("Y-m-d H:i:s"); //lägg till key för index deleted_ay
        $deletedTask[] = $todoList[$index];
        file_put_contents($deleted, json_encode($deletedTask, JSON_PRETTY_PRINT));

        unset($todoList[$index]);
        $todoList = array_values($todoList); //indexera om
        save($todoList);
    }
}
function deleteAll()
{
    $deletedFile = "papperskorg.json";

    if (file_exists($deletedFile)) {
        file_put_contents($deletedFile, []);
    }
}

//toggla checkboxrutan
function done($index)
{
    $todoList = read();
    //om variabeln finns, sätt värdet till true/false.
    if (isset($todoList[$index])) {
        $todoList[$index]['done'] = !$todoList[$index]['done'];
        save($todoList);
    }
}

//uppdatera info
function update($index, $newTask)
{
    $todoList = read(); //hämtad data via read från .json filen
    if (isset($todoList[$index])) {
        //ändra ['task'] till $new task
        $todoList[$index]['task'] = $newTask;
        // lägg till uppdaterad tid
        $todoList[$index]['updated_at'] = date("Y-m-d H:i:s");
        save($todoList);
    }
}
function regret($index) // ångra uppgift flyttad till papperskorg. Hämtar tillbaka från json till json
{
    $filnamn = "task.json";
    $deletedFile = "papperskorg.json";

    if (file_exists($deletedFile)) {
        $deletedTask = json_decode(file_get_contents($deletedFile), true);
    } else {
        $deletedTask = [];
    }

    if (file_exists($filnamn)) {
        $todoList = json_decode(file_get_contents($filnamn), true);
    } else {
        $todoList = [];
    }

    if (isset($deletedTask[$index])) {
        // Ta bort "deleted_at" och lägg tillbaka uppgiften i todo-listan
        unset($deletedTask[$index]["deleted_at"]);
        $todoList[] = $deletedTask[$index];

        // Ta bort uppgiften från papperskorgen
        unset($deletedTask[$index]);

        // Reindexera arrayen för papperskorgen
        $deletedTask = array_values($deletedTask);

        // Spara den uppdaterade todo-listan och papperskorgen
        file_put_contents($filnamn, json_encode($todoList, JSON_PRETTY_PRINT));
        file_put_contents($deletedFile, json_encode($deletedTask, JSON_PRETTY_PRINT));
    }
}


//Hantera POST för ny uppgift
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["todo"])) {
        create($_POST["todo"]);
    }
    if (isset($_POST["update"])) {
        update($_POST["index"], $_POST["newTask"]);
    }
    header("Location: index.php");
    exit;
}
//Getförfrågning för att radera
if (isset($_GET["delete"])) {
    delete($_GET["delete"]);
    header("Location: index.php");
    exit;
}
//get för checkboxmarkering
if (isset($_GET["done"])) {
    done($_GET["done"]);
    header("Location: index.php");
    exit;
}


//get ta bort alla
if (isset($_GET["deleteAll"])) {
    deleteAll();
    header("Location: index.php");
    exit;
}

if (isset($_GET["regret"])) {
    regret($_GET["regret"]);
    header("Location: index.php");
}
