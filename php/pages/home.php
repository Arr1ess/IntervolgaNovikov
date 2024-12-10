<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Список таблиц</h1>
    <div class="flex flex-col space-y-4">
        <?php $colors = ['bg-blue-200', 'bg-green-200', 'bg-yellow-200', 'bg-red-200', 'bg-purple-200']; ?>
        <?php foreach ($tables as $index => $table): ?>
            <a href="/table?tablename=<?php echo urlencode($table); ?>" class="block p-4 shadow-md rounded-lg <?php echo $colors[$index % count($colors)]; ?> hover:bg-blue-100 transition duration-300">
                <span class="text-lg font-semibold"><?php echo htmlspecialchars($table); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>