<?php

define("TASKS_FILE", "tasks.json");

function loadTasks(): array {
    if (!file_exists(TASKS_FILE)) {
        return [];
    }
    $data = file_get_contents(TASKS_FILE);
    return $data ? json_decode($data, true) : [];
}

function saveTasks(array $tasks): void {
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["task"])) {
        $tasks = loadTasks();
        if (!empty($_POST["task"])) {
            $tasks[] = [
                'task' => htmlspecialchars(trim($_POST["task"])),
                'done' => false
            ];
            saveTasks($tasks);
        }
    }
}


if (isset($_GET['action']) && isset($_GET['task'])) {
    $tasks = loadTasks();
    $taskToHandle = urldecode($_GET['task']);
    
    foreach ($tasks as &$task) {
        if ($task['task'] === $taskToHandle) {
            if ($_GET['action'] === 'delete') {
                $tasks = array_filter($tasks, function($t) use ($taskToHandle) {
                    return $t['task'] !== $taskToHandle;
                });
                break;
            } elseif ($_GET['action'] === 'complete') {
                $task['done'] = true;
                break;
            }
        }
    }
    saveTasks(array_values($tasks)); 
}


$tasks = loadTasks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        form {
            margin-top: 20px;
            display: flex;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #6a1b9a;
            color: white;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #5e178b;
        }
        .task-list {
            margin-top: 20px;
        }
        .task-list ul {
            list-style-type: none;
            padding: 0;
        }
        .task-list li {
            background: #f4f4f4;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-list li.completed {
            text-decoration: line-through;
            color: #888;
        }
        .task-list button {
            margin-left: 10px;
            background-color: transparent;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }
        .task-list button:hover {
            background-color: rgba(102, 245, 133, 0.1);
            color: rgb(0, 109, 0);
        }
        .task-list button.delete {
            background-color: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        .task-list button.delete:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>To-Do App</h1>
        <form action="" method="POST">
            <input type="text" name="task" placeholder="Enter a new task" required>
            <button type="submit">Add Task</button>
        </form>

        <div class="task-list">
            <h2>Task List</h2>
            <?php if (empty($tasks)): ?>
                <p>No task added</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($tasks as $task): ?>
                        <li class="<?= $task['done'] ? 'completed' : '' ?>">
                            <?= htmlspecialchars($task['task']) ?>
                            <div>
                                <?php if (!$task['done']): ?>
                                    <a href="?action=complete&task=<?= urlencode($task['task']) ?>"><button>Complete</button></a>
                                <?php endif; ?>
                                <a href="?action=delete&task=<?= urlencode($task['task']) ?>"><button class="delete">Delete</button></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
