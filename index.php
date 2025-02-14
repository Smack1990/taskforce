<?php
require "taskForceFunctions.php";
$todoList = read();

$ongoingTasks = [];
$completedTasks = [];

foreach ($todoList as $index => $task) {
    if ($task['done']) {
        $completedTasks[$index] = $task;
    } else {
        $ongoingTasks[$index] = $task;
    }
}

// om 'rediger''name=edit' är tryck kör detta. 
if (isset($_GET["edit"])) {
    $index = $_GET["edit"];
    if (isset($todoList[$index])) {  // om isset öppna nytt html-dokument för forms
        ?>
        <!DOCTYPE html>
        <html lang="sv">

        <head>
            <meta charset="UTF-8">
            <title>Redigera uppgift</title>
            <link rel="stylesheet" href="style.css">
        </head>

        <body class="bodyR">
            <main class="redigera">
            <h1 class="h1r">Redigera uppgift</h1>
            <form method="post" action="taskForceFunctions.php">
                <input type="hidden" name="index" value="<?= $index; ?>">
                <input class="input" type="text" name="newTask" value="<?= htmlspecialchars($todoList[$index]["task"]); ?>"
                    required>
                <button class="button" type="submit" name="update">Spara</button>
            </form>
            <button class="buttonredigera"><a href="index.php">Tillbaka</a></button>
            </main>
            <script>document.body.addEventListener("click", function() {
    document.documentElement.classList.toggle("uppochner");
});</script>
        </body>

        </html>
        <?php
        exit;
    }
} //stäning av formuläret
?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="100">
    <link rel="stylesheet" href="style.css">
    <title>Taskforce</title>
</head>

<body>
    <main>
        <form method="post" action="taskForceFunctions.php">
            <input class="input" type="text" name="todo" placeholder="Lägg till uppgift.." required>
            <button class="button" type="submit">Lägg till</button>
            <h1>ToDo</h1>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Uppgift</th>
                    <th>Skapad</th>
                    <th>Uppdaterad</th>
                    <th>Redigera</th>
                    <th>Klar</th>
                    <th>Ta bort</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ongoingTasks as $index => $todos): ?>
                    <tr class="todo-item">
                        <td style="
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <?= htmlspecialchars($todos["task"]); ?>
                        </td>

                        <td style="
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <?= $todos["created_at"] ?? "Okänd"; ?>
                        </td>

                        <td style="
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <?= $todos["updated_at"] ?? ""; ?>
                        </td>
                        <!-- redigera -->
                        <td style="
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <form method="get" action="index.php">
                                <input type="hidden" name="edit" value="<?= $index; ?>">
                                <button class="button"  type="submit">Redigera</button>
                            </form>
                        </td>

                        <td style=" 
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <form method="get" action="taskForceFunctions.php">
                                <input type="hidden" name="done" value="<?= $index; ?>">
                                <input class="checkboxbefore" type="checkbox" <?= $todos["done"] ? "checked" : ""; ?>
                                    onchange="this.form.submit()">
                            </form>
                        </td>

                        <td style="
                    <?php
                    if (!empty($todos['updated_at'])) {
                        echo 'background:#CDAD45;';
                    } else {
                        echo 'background:#BCBCBC;';
                    }
                    ?>">
                            <form method="get" action="taskForceFunctions.php">
                                <input type="hidden" name="delete" value="<?= $index; ?>">
                                <button class="button" type="submit">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h1> Klara </h1>
        <table>
            <thead>
                <tr>
                    <th>Uppgift</th>
                    <th>Skapad</th>
                    <th>Klar(checka ur för ångra)</th>
                    <th>Ta bort</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($completedTasks as $index => $task): ?>
                    <tr style="background: #107068;">
                        <td><?= htmlspecialchars($task["task"]); ?></td>
                        <td><?= $task["created_at"] ?? "Okänd"; ?></td>
                        <td>
                            <form method="get" action="taskForceFunctions.php">
                                <input type="hidden" name="done" value="<?= $index; ?>">
                                <input class="checkbox" type="checkbox" checked onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>
                            <form method="get" action="taskForceFunctions.php">
                                <input type="hidden" name="delete" value="<?= $index; ?>">
                                <button class="button" type="submit">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <?php
        $deletedFile = "papperskorg.json";
        $deletedTasks = file_exists($deletedFile) ? json_decode(file_get_contents($deletedFile), true) : [];

        if (!is_array($deletedTasks)) {
            $deletedTasks = [];
        } ?>

        <h1>Papperskorg</h1>

        <table>
            <thead>
                <tr>
                    <th>Uppgift</th>
                    <th>Raderad</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($deletedTasks as $index => $task):
                    ?>
                    <tr style="background:#BCBCBC;">
                        <td><?= htmlspecialchars($task['task']); ?></td>
                        <td><?= $task['deleted_at'] ?? ""; ?></td>
                        <td>
                            <form method="get" action="taskForceFunctions.php">
                                <input type="hidden" name="regret" value="<?= $index; ?>">
                                <button class="button" type="submit">Ångra</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
        <form method="get" action="taskForceFunctions.php">
            <button class="buttonPapperskorg" type="submit" name="deleteAll">Töm papperskorg</button>
    </main>

<script>document.body.addEventListener("click", function() {
    document.documentElement.classList.toggle("uppochner");
});</script>
</body>

</html>