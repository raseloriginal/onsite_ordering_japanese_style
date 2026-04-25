<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakura Order System - Table Selection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', 'Noto Sans JP', sans-serif; background-color: #f9f7f2; }
        .japanese-border { border: 1px solid #d1ccc0; }
        .table-card:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6">
    <div class="max-w-4xl w-full">
        <header class="text-center mb-12">
            <h1 class="text-4xl font-semibold text-[#2c3e50] mb-2">Sakura Restaurant</h1>
            <p class="text-[#7f8c8d] uppercase tracking-widest text-sm">Select Table to Lock Tablet</p>
            <div class="w-16 h-1 bg-red-400 mx-auto mt-4"></div>
        </header>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach($tables as $table): ?>
            <a href="/restuarent_ordersystem/public/table/<?= $table['id'] ?>" 
               class="table-card bg-white p-8 rounded-2xl shadow-sm text-center japanese-border group hover:border-red-300">
                <div class="text-xs text-gray-400 mb-1">TABLE</div>
                <div class="text-4xl font-bold text-[#2c3e50] group-hover:text-red-500 transition-colors"><?= $table['table_number'] ?></div>
                <div class="mt-4 px-3 py-1 rounded-full text-[10px] inline-block <?= $table['status'] == 'available' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' ?>">
                    <?= strtoupper($table['status']) ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <footer class="mt-12 text-center text-xs text-gray-400">
            Staff Only Interface • Please do not leave tablets on this page
        </footer>
    </div>
</body>
</html>
