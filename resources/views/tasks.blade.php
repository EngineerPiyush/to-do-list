<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">To-Do List</h2>
    <div class="input-group mb-3">
        <input type="text" id="taskInput" class="form-control" placeholder="Enter task">
        <button class="btn btn-primary" id="addTask">Enter</button>
    </div>
    <button class="btn btn-secondary mb-3" id="showTasks">Show All Tasks</button>
    <ul class="list-group" id="taskList"></ul>
</div>

<script>
$(document).ready(function () {
    let showAllTasks = false; 

    function fetchTasks() {
        $.get('/tasks', function (tasks) {
            $('#taskList').html('');
            tasks.forEach(task => {
                if (showAllTasks || !task.is_completed) {
                    addTaskToUI(task);
                }
            });
        });
    }


function addTaskToUI(task) {
    let taskItem = `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
        <input type="checkbox" class="toggle-complete" data-id="${task.id}" ${task.is_completed ? 'checked' : ''}>
       <span class="task-title ${task.is_completed ? 'text-decoration-line-through text-muted' : 'fw-bold text-dark'}">${task.title}</span>
        <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
    </li>`;
    $('#taskList').append(taskItem);
}


    $('#addTask').click(function () {
        let taskTitle = $('#taskInput').val().trim();
        if (!taskTitle) return alert('Task cannot be empty');

        $.post('/tasks', { title: taskTitle, _token: '{{ csrf_token() }}' })
        .done(function (task) {
            $('#taskInput').val('');
            addTaskToUI(task); 
        })
        .fail(function () {
            alert('Task already exists!');
        });
    });

$(document).on('change', '.toggle-complete', function () {
    let taskId = $(this).data('id');
    $.ajax({
        url: `/tasks/${taskId}`,
        type: 'PATCH',
        data: { _token: '{{ csrf_token() }}' },
        success: function () {
            let taskTitle = $(`li[data-id="${taskId}"] .task-title`);
            taskTitle.toggleClass('text-decoration-line-through text-muted fw-bold text-dark');
        }
    });
});


    $(document).on('click', '.delete-task', function () {
        let taskId = $(this).data('id');
        if (!confirm('Are you sure to delete this task?')) return;

        $.ajax({
            url: `/tasks/${taskId}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () {
                $(`li[data-id="${taskId}"]`).remove(); 
            }
        });
    });

    $('#showTasks').click(function () {
        showAllTasks = !showAllTasks; 
        fetchTasks();
    });

    fetchTasks();
});
</script>
</body>
</html>
