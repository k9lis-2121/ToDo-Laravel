@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container text-white">
        <h2>CRUD операции с использованием jQuery и AJAX</h2>

        <div class="card bg-dark text-light col-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Новая задача</h5>
                <form method="post" id="addTaskForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" class="form-control form-control-dark bg-dark text-white" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
                    <div class="form-group mb-3">
                        <label for="title">Заголовок:</label>
                        <input type="text" class="form-control form-control-dark bg-dark text-white" id="title" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Описание:</label>
                        <textarea class="form-control form-control-dark bg-dark text-white" id="description" name="description"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input bg-dark text-primary" id="is_public" value="1" name="is_public">
                        <label class="form-check-label text-light" for="is_public">Публичная задача</label>
                    </div>
                    <div class="form-group mb-4">
                        <label for="image_url">URL изображения:</label>
                        <input type="text" class="form-control form-control-dark bg-dark text-white" id="image_url" name="image_url">
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>




        <!-- Таблица для отображения списка задач -->
        <table class="table mt-3 text-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Описание</th>
                <th>Публичная задача</th>
                <th>URL изображения</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody id="task-list">
            @foreach($tasks as $task)
                <tr data-id="{{ $task->id }}">
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->is_public ? 'Да' : 'Нет' }}</td>
                    <td>{{ $task->image_url }}


                        <a href="../img/{{ $task->image_url }}" target="_blank">
                            <div class="thumbnail">
                                <img src="../img/{{ $task->image_url }}" width="150px" height="150px">
                            </div>
                        </a>

                    </td>
                    <td>
                        <button class="btn btn-primary edit-btn mr-2">Редактировать</button>
                        <button class="btn btn-danger delete-btn">Удалить</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Форма для редактирования задачи (скрыта по умолчанию) -->
        <form id="edit-form" style="display:none;" class="bg-dark">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group">
                <label for="edit-title" class="text-light">Заголовок:</label>
                <input type="text" class="form-control form-control-dark" id="edit-title" name="title" required>
            </div>
            <input type="hidden" name="user_id" id="edit-user_id">
            <div class="form-group">
                <label for="edit-description" class="text-light">Описание:</label>
                <textarea class="form-control form-control-dark" id="edit-description" name="description"></textarea>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="edit-is_public" name="is_public">
                <label class="form-check-label text-light" for="edit-is_public">Публичная задача</label>
            </div>
            <div class="form-group">
                <label for="edit-image_url" class="text-light">URL изображения:</label>
                <input type="text" class="form-control form-control-dark" id="edit-image_url" name="image_url">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-secondary ml-2 cancel-btn">Отмена</button>
        </form>
    </div>

    <!-- Скрипты для выполнения AJAX запросов -->
    <script>

        function updateTaskList() {
            $.ajax({
                url: '/tasks?list=update', // Здесь нужно указать URL-адрес на сервере, который возвращает список задач в формате JSON.
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var taskList = $('#task-list');
                    taskList.empty(); // Очистить существующий список задач.


                    var obj = jQuery.parseJSON(response);

                    console.log('tetst');
                    console.log(obj);

                    // Добавить новые строки для каждой задачи из полученных данных.
                    $.each(obj.tasks, function(index, task) {

                        var obj = jQuery.parseJSON(response);

                        console.log('tetst');
                        console.log(obj);

                        var rowHtml = '<tr data-id="' + task.id + '">' +
                            '<td>' + task.id + '</td>' +
                            '<td>' + task.title + '</td>' +
                            '<td>' + task.description + '</td>' +
                            '<td>' + (task.is_public ? 'Да' : 'Нет') + '</td>' +
                            '<td>' + task.image_url + '</td>' +
                            '<td>' +
                            '<button class="btn btn-primary edit-btn mr-2">Редактировать</button>' +
                            '<button class="btn btn-danger delete-btn">Удалить</button>' +
                            '</td>' +
                            '</tr>';
                        taskList.append(rowHtml);
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Ошибка при загрузке списка задач:', textStatus, errorThrown);
                    alert('Не удалось загрузить список задач. Попробуйте еще раз.');
                }
            });
        }

        $(document).ready(function() {
            // Отправка данных для создания новой задачи
            $('#addTaskForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: '{{ url('/tasks') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function(response) {
                        updateTaskList();
                        var form = document.getElementById("addTaskForm");
                        form.reset();
                        alert('Задача успешно создана.');
                        // window.location.reload();
                    },
                    error: function() {
                        alert('Произошла ошибка при создании задачи.');
                    }
                });
            });

            // Отображение формы для редактирования задачи
            $('.edit-btn').click(function() {
                var id = $('.edit-btn').closest('tr').data('id');
                var user_id = $('.edit-btn').closest('tr').data('user_id');
                var title = $('.edit-btn').closest('tr').find('td:nth-child(2)').text();
                var description = $(this).closest('tr').find('td:nth-child(3)').text();
                var is_public = ($(this).closest('tr').find('td:nth-child(4)').text() == 'Да');
                var image_url = $(this).closest('tr').find('td:nth-child(5)').text();
                $('#edit-id').val(id);
                $('#edit-user_id').val(user_id);
                $('#edit-title').val(title);
                $('#edit-description').val(description);
                $('#edit-is_public').prop('checked', is_public);
                $('#edit-image_url').val(image_url);
                $('#task-list').hide();
                $('#edit-form').show();
            });

            // Отправка данных для обновления задачи
            $('#edit-form').submit(function(event) {
                event.preventDefault();
                var id = $('#edit-id').val();

                // Получаем значение чекбокса is_public
                var is_public = $('#edit-is_public').is(':checked') ? 1 : 0;

                // Собираем данные для отправки на сервер
                var formData = new FormData(this);
                formData.append('is_public', is_public);
                formData.append('_method', 'put');
                console.log('FormData:');
                formData.forEach((value, key) => {
                    console.log(key + ': ' + value);
                });
                $.ajax({
                    url: '{{ url('/tasks') }}/' + id,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    enctype: 'multipart/form-data', // Добавляем тип кодирования данных для работы с файлами
                    processData: false, // Отключаем обработку данных перед отправкой
                    contentType: false, // Отключаем установку заголовка Content-Type

                    success: function(response) {
                        alert('Задача успешно обновлена.');
                        window.location.reload();
                    },
                    error: function() {
                        alert('Произошла ошибка при обновлении задачи.');
                    }
                });
            });

            // Отмена редактирования задачи и возврат к списку задач
            $('.cancel-btn').click(function() {
                $('#edit-form').hide();
                $('#task-list').show();
            });

            // Удаление задачи
            $('.delete-btn').click(function() {
                var id = $(this).closest('tr').data('id');
                if (confirm('Вы уверены, что хотите удалить эту задачу?')) {
                    $.ajax({
                        url: '{{ url('/tasks') }}/' + id,
                        method: 'DELETE',
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('Задача успешно удалена.');
                            window.location.reload();
                        },
                        error: function() {
                            alert('Произошла ошибка при удалении задачи.');
                        }
                    });
                }
            });
        });
    </script>
@endsection


