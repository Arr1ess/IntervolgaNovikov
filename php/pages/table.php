<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Таблица: <?php echo htmlspecialchars($tablename); ?></h1>
    <div class="mb-3 flex space-x-4">
        <a href="/" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">На главную</a>
        <a href="/create_page?tablename=<?php echo urlencode($tablename); ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Создать новую запись</a>
    </div>
    <table class="table-auto w-full mt-4 bg-white shadow-md rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-200 text-left">
                <?php foreach ($table_column as $column): ?>
                    <th class="px-4 py-2"><?php echo htmlspecialchars($column['Field']); ?></th>
                <?php endforeach; ?>
                <th class="px-4 py-2">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($table_rows as $row): ?>
                <tr class="border-b border-gray-200">
                    <?php foreach ($row as $key => $value): ?>
                        <td class="px-4 py-2"><?php echo $value === null ? 'Данный параметр не известен' : htmlspecialchars($value); ?></td>
                    <?php endforeach; ?>
                    <td class="px-4 py-2">
                        <a href="/edit_page?tablename=<?php echo urlencode($tablename); ?>&id=<?php echo urlencode($row['id']); ?>" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Редактировать</a>
                        <button
                            data-tablename="<?php echo htmlspecialchars($tablename); ?>"
                            data-id="<?php echo htmlspecialchars($row['id']); ?>"
                            class="delete-btn bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                            Удалить
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const tableName = button.dataset.tablename;
                const id = button.dataset.id;

                const confirmDelete = confirm('Вы уверены, что хотите удалить эту запись?');
                if (!confirmDelete) return;

                try {
                    const response = await fetch(`/delete?tablename=${encodeURIComponent(tableName)}&id=${encodeURIComponent(id)}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    if (response.ok) {
                        alert('Запись успешно удалена!');
                        // Обновляем страницу или удаляем элемент из DOM
                        button.closest('tr').remove();
                    } else {
                        const errorData = await response.json();
                        alert(`Ошибка: ${errorData.error}`);
                    }
                } catch (error) {
                    console.error('Ошибка при выполнении запроса:', error);
                    alert('Произошла ошибка при удалении записи.');
                }
            });
        });
    });
</script>