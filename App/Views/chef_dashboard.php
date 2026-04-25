<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen - Sakura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;700&family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --jp-cream: #FDFCF9;
            --jp-charcoal: #2D2D2D;
            --jp-red: #BC002D;
            --jp-border: #E5E1D8;
            --game-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
        }
        body { 
            font-family: 'Noto Sans JP', sans-serif; 
            background-color: var(--jp-cream);
            color: var(--jp-charcoal);
            min-height: 100vh;
        }
        h1, h2, h3, .font-mincho { font-family: 'Shippori Mincho', serif; }
        
        @keyframes popIn {
            0% { transform: scale(0.92); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }

        .order-card {
            background: white;
            border: 1px solid var(--jp-border);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px;
        }
        .order-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--game-shadow);
            border-color: var(--jp-red);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .xp-bar {
            height: 4px;
            background: #eee;
            border-radius: 2px;
            overflow: hidden;
            width: 120px;
        }
        .xp-fill {
            height: 100%;
            background: var(--jp-red);
            width: 0%;
            transition: width 0.8s ease;
        }

        .status-dot {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .status-dot::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: var(--jp-red);
            animation: pulse-ring 1.5s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }
    </style>
</head>
<body class="bg-[#FDFCF9]/50">
    <header class="glass-header border-b border-[#E5E1D8] px-6 py-4 flex justify-between items-center mb-10 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-[#BC002D] rounded-sm flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-red-100">厨</div>
            <div>
                <h1 class="text-2xl font-bold font-mincho">厨房管理</h1>
                <div class="xp-bar mt-1"><div class="xp-fill" id="sync-bar"></div></div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-right mr-2">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Kitchen Status</p>
                <p class="text-xs font-bold text-gray-800">Operational</p>
            </div>
            <div class="status-dot w-3 h-3 bg-[#BC002D] rounded-full"></div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-4 md:px-8 pb-20">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold font-mincho flex items-center gap-3">
                <span class="text-[#BC002D]">●</span> 受注一覧 (Orders)
            </h2>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]" id="order-count">0 Orders Active</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8" id="orders-container">
            <!-- AJAX content -->
        </div>
    </main>

    <script>
        function renderOrders(orders) {
            const container = $('#orders-container');
            $('#order-count').text(`${orders.length} Orders Active`);
            
            // Pulse the sync bar
            $('#sync-bar').css('width', '100%');
            setTimeout(() => $('#sync-bar').css('width', '90%'), 500);

            if (orders.length === 0) {
                container.html(`
                    <div class="col-span-full py-24 flex flex-col items-center justify-center border-2 border-dashed border-[#E5E1D8] rounded-3xl text-gray-400">
                        <p class="italic text-lg font-mincho">Waiting for new orders...</p>
                    </div>
                `);
                return;
            }

            container.html(orders.map((order, i) => `
                <div class="order-card bg-white p-8 rounded-3xl flex flex-col h-full shadow-sm animate-in fade-in zoom-in duration-500" style="animation-delay: ${i * 0.1}s">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Table</span>
                            <div class="text-4xl font-bold font-mincho text-gray-900 mt-1">${order.table_number}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-gray-300 font-bold uppercase tracking-widest block">${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            <span class="inline-block mt-2 px-2 py-0.5 bg-gray-50 text-gray-400 rounded text-[8px] font-bold uppercase tracking-tighter border border-[#E5E1D8]">
                                #${String(order.id).padStart(4, '0')}
                            </span>
                        </div>
                    </div>
                    
                    <ul class="flex-1 space-y-4 mb-10">
                        ${order.items.map(item => `
                            <li class="flex justify-between items-center border-b border-dotted border-gray-100 pb-2">
                                <span class="text-sm flex items-center">
                                    <span class="inline-block w-7 h-7 bg-gray-900 text-white text-center leading-7 rounded-lg font-black text-xs mr-3 shadow-md">${item.quantity}</span>
                                    <span class="font-medium text-gray-700">${item.name}</span>
                                </span>
                            </li>
                        `).join('')}
                    </ul>
                    
                    <button onclick="updateStatus(${order.id}, '${order.order_status === 'pending' ? 'cooking' : 'ready'}', this)" 
                            class="w-full py-4 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all active:scale-95 shadow-xl shadow-gray-100 ${order.order_status === 'pending' ? 'bg-[#2D2D2D] text-white hover:bg-black' : 'bg-[#BC002D] text-white hover:opacity-90'}">
                        ${order.order_status === 'pending' ? 'Start Preparing' : 'Mark as Ready'}
                    </button>
                </div>
            `).join(''));
        }

        async function fetchOrders() {
            try {
                const res = await $.get('<?= url('/api/chef/data') ?>');
                renderOrders(res.orders);
            } catch (err) { console.error('Failed to fetch orders', err); }
        }

        async function updateStatus(orderId, status, btn) {
            $(btn).prop('disabled', true).text('UPDATING...');
            try {
                const res = await fetch('<?= url('/chef/update') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: status })
                });
                const result = await res.json();
                if (result.success) fetchOrders();
            } catch (err) { console.error(err); }
        }

        // Initial fetch and 10s interval
        fetchOrders();
        setInterval(fetchOrders, 10000);
    </script>
</body>
</html>
