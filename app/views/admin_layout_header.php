<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - Sakura Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;700&family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --jp-cream: #FDFCF9;
            --jp-charcoal: #2D2D2D;
            --jp-red: #BC002D;
            --jp-gold: #D4AF37;
            --jp-border: #E5E1D8;
        }
        body { 
            font-family: 'Noto Sans JP', sans-serif; 
            background-color: var(--jp-cream);
            color: var(--jp-charcoal);
        }
        h1, h2, h3, .font-mincho { font-family: 'Shippori Mincho', serif; }
        .sidebar-active { color: var(--jp-red) !important; border-right: 3px solid var(--jp-red); background: rgba(188, 0, 45, 0.05); }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid var(--jp-border); }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">
    <?php 
    $current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base_admin = '/restuarent_ordersystem/public/admin';
    ?>
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex w-72 bg-white border-r border-[#E5E1D8] flex-col p-8 shrink-0 min-h-screen sticky top-0">
        <div class="flex items-center gap-3 mb-12">
            <div class="w-10 h-10 bg-[#BC002D] rounded-sm flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-red-100">桜</div>
            <h1 class="text-2xl font-bold tracking-tighter">Sakura Admin</h1>
        </div>
        
        <nav class="space-y-1 flex-1">
            <a href="<?= $base_admin ?>" class="flex items-center px-4 py-4 rounded-sm font-medium transition-all <?= $current_url == $base_admin ? 'sidebar-active text-[#BC002D]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' ?>">
                <span class="mr-3">統計</span> Dashboard
            </a>
            <a href="<?= $base_admin ?>/items" class="flex items-center px-4 py-4 rounded-sm font-medium transition-all <?= strpos($current_url, 'items') !== false ? 'sidebar-active text-[#BC002D]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' ?>">
                <span class="mr-3">商品</span> Menu Items
            </a>
            <a href="<?= $base_admin ?>/tables" class="flex items-center px-4 py-4 rounded-sm font-medium transition-all <?= strpos($current_url, 'tables') !== false ? 'sidebar-active text-[#BC002D]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' ?>">
                <span class="mr-3">座席</span> Tables
            </a>
            <a href="<?= $base_admin ?>/expenses" class="flex items-center px-4 py-4 rounded-sm font-medium transition-all <?= strpos($current_url, 'expenses') !== false ? 'sidebar-active text-[#BC002D]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' ?>">
                <span class="mr-3">経費</span> Expenses
            </a>
            <a href="<?= $base_admin ?>/income" class="flex items-center px-4 py-4 rounded-sm font-medium transition-all <?= strpos($current_url, 'income') !== false ? 'sidebar-active text-[#BC002D]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' ?>">
                <span class="mr-3">収入</span> Income Logs
            </a>
        </nav>
        
        <div class="mt-auto pt-8 border-t border-[#E5E1D8]">
            <a href="/restuarent_ordersystem/public/" class="text-xs uppercase tracking-widest text-gray-400 hover:text-[#BC002D] transition-colors">Store View →</a>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="flex md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[#E5E1D8] justify-around p-3 z-50 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
        <a href="<?= $base_admin ?>" class="flex flex-col items-center gap-1 <?= $current_url == $base_admin ? 'text-[#BC002D]' : 'text-gray-400' ?>">
            <div class="text-xs font-bold">統計</div>
            <span class="text-[10px]">Stats</span>
        </a>
        <a href="<?= $base_admin ?>/items" class="flex flex-col items-center gap-1 <?= strpos($current_url, 'items') !== false ? 'text-[#BC002D]' : 'text-gray-400' ?>">
            <div class="text-xs font-bold">商品</div>
            <span class="text-[10px]">Items</span>
        </a>
        <a href="<?= $base_admin ?>/tables" class="flex flex-col items-center gap-1 <?= strpos($current_url, 'tables') !== false ? 'text-[#BC002D]' : 'text-gray-400' ?>">
            <div class="text-xs font-bold">座席</div>
            <span class="text-[10px]">Tables</span>
        </a>
        <a href="<?= $base_admin ?>/income" class="flex flex-col items-center gap-1 <?= strpos($current_url, 'income') !== false ? 'text-[#BC002D]' : 'text-gray-400' ?>">
            <div class="text-xs font-bold">収入</div>
            <span class="text-[10px]">Income</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-12 mb-20 md:mb-0 overflow-y-auto">
