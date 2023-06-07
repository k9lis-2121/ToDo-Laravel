@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>CRUD операции с использованием jQuery и AJAX</h2>

        <!-- Форма для создания новой задачи -->
        <form id="create-form">
            @csrf
            <div class="form-group">
                <label for="user_id">ID пользователя:</label>
                <input type="number" class="form-control" id="user_id" name="user_id" required>
            </div>
            <div class="form-group">
                <label for="title">Заголовок:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_public" name="is_public">
                <label class="form-check-label" for="is_public">Публичная задача</label>
            </div>
            <div class="form-group">
                <label for="image_url">URL изображения:</label>
                <input type="text" class="form-control" id="image_url" name="image_url">
            </div>
            <button type="submit" class="btn btn-primary">Создать</button>
        </form>

        <!-- Таблица для отображения списка задач -->
        <table class="table mt-3">
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
                    <td>{{ $task->image_url }}</td>
                    <td>
                        <button class="btn btn-primary edit-btn mr-2">Редактировать</button>
                        <button class="btn btn-danger delete-btn">Удалить</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Форма для редактирования задачи (скрыта по умолчанию) -->
        <form id="edit-form" style="display:none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group">
                <label for="edit-title">Заголовок:</label>
                <input type="text" class="form-control" id="edit-title" name="title" required>
            </div>
            <div class="form-group">
                <label for="edit-description">Описание:</label>
                <textarea class="form-control" id="edit-description" name="description"></textarea>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="edit-is_public" name="is_public">
                <label class="form-check-label" for="edit-is_public">Публичная задача</label>
            </div>
            <div class="form-group">
                <label for="edit-image_url">URL изображения:</label>
                <input type="text" class="form-control" id="edit-image_url" name="image_url">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-secondary ml-2 cancel-btn">Отмена</button>
        </form>
    </div>

    <!-- Скрипты для выполнения AJAX запросов -->
    <script>
        $(document).ready(function() {
            // Отправка данных для создания новой задачи
            $('#create-form').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: '{{ url('/tasks') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert('Задача успешно создана.');
                        window.location.reload();
                    },
                    error: function() {
                        alert('Произошла ошибка при создании задачи.');
                    }
                });
            });

            // Отображение формы для редактирования задачи
            $('.edit-btn').click(function() {
                var id = $(this).closest('tr').data('id');
                var title = $(this).closest('tr').find('td:nth-child(2)').text();
                var description = $(this).closest('tr').find('td:nth-child(3)').text();
                var is_public = ($(this).closest('tr').find('td:nth-child(4)').text() == 'Да');
                var image_url = $(this).closest('tr').find('td:nth-child(5)').text();
                $('#edit-id').val(id);
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
                $.ajax({
                    url: '{{ url('/tasks') }}/' + id,
                    method: 'PUT',
                    data: $(this).serialize(),
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


