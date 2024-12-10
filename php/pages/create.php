<?php
?>

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Create Entry in Table: <?php echo htmlspecialchars($tablename); ?></h1>
    <div class="mb-3 flex space-x-4">
        <a href="/" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Home</a>
        <a href="/table?tablename=<?php echo urlencode($tablename); ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to Table</a>
    </div>
    <form action="/create" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <input type="hidden" name="tablename" value="<?php echo htmlspecialchars($tablename); ?>">

        <?php foreach ($table_columns as $column): ?>
            <?php if ($column['Key'] === 'PRI' && $column['Extra'] === 'auto_increment') continue; ?>

            <div class="mb-4">
                <label for="<?php echo htmlspecialchars($column['Field']); ?>" class="block text-gray-700 text-sm font-bold mb-2">
                    <?php echo htmlspecialchars($column['Field']); ?>
                </label>

                <?php if ($column['Field'] == 'type' && isset($enumValues) && is_array($enumValues)): ?>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>
                        <?php foreach ($enumValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif (isset($dependedColumns[$column['Field']]) && is_array($dependedColumns[$column['Field']])): ?>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>
                        <?php foreach ($dependedColumns[$column['Field']] as $option): ?>
                            <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ($column['Type'] === 'int' || $column['Type'] === 'double' || strpos($column['Type'], 'decimal') !== false): ?>
                    <?php
                    $step = "any";
                    if (preg_match('/decimal\((\d+),(\d+)\)/i', $column['Type'], $matches)) {
                        $scale = $matches[2];
                        $step = "0." . str_repeat("0", $scale - 1) . "1";
                    }
                    ?>
                    <input type="number" step="<?php echo $step; ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" value="<?php echo htmlspecialchars($row[0][$column['Field']]); ?>" required>

                <?php elseif (strpos($column['Type'], 'varchar') !== false || $column['Type'] === 'text'): ?>
                    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>

                <?php elseif ($column['Type'] === 'date'): ?>
                    <input type="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>

                <?php elseif ($column['Type'] === 'datetime'): ?>
                    <input type="datetime-local" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>

                <?php else: ?>
                    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="<?php echo htmlspecialchars($column['Field']); ?>" name="<?php echo htmlspecialchars($column['Field']); ?>" required>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create</button>
        </div>
    </form>
</div>