<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiter - Sakura</title>
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
        
        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes popIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }

        .task-card {
            background: white;
            border: 1px solid var(--jp-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .task-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--game-shadow);
            border-color: var(--jp-red);
        }

        .notif-card {
            border-left: 4px solid var(--jp-red);
            background: white;
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
            width: 100px;
        }
        .xp-fill {
            height: 100%;
            background: var(--jp-red);
            width: 75%;
            transition: width 1s ease;
        }
    </style>
</head>
<body class="bg-[#FDFCF9]/50">
    <header class="glass-header border-b border-[#E5E1D8] px-6 py-4 flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-[#BC002D] rounded-sm flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-red-100">給</div>
            <div>
                <h1 class="text-2xl font-bold font-mincho">給仕管理</h1>
                <div class="xp-bar mt-1"><div class="xp-fill" id="sync-bar"></div></div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-right">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Active Shift</p>
                <p class="text-xs font-bold text-gray-800">Sakura Waiter Beta</p>
            </div>
            <div class="status-dot w-3 h-3 bg-[#BC002D] rounded-full"></div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Payments -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold font-mincho flex items-center gap-3">
                        <span class="text-[#BC002D]">●</span> 会計リクエスト
                    </h2>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" id="payment-count">0 Pending</span>
                </div>
                <div id="payments-container" class="space-y-6">
                    <!-- AJAX content -->
                </div>
            </section>

            <!-- Notifications -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold font-mincho flex items-center gap-3">
                        <span class="text-gray-300">●</span> 通知
                    </h2>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" id="notif-count">0 New</span>
                </div>
                <div id="notifications-container" class="space-y-4">
                    <!-- AJAX content -->
                </div>
            </section>
        </div>
    </main>

    <script>
        function renderData(data) {
            const payContainer = $('#payments-container');
            const notifContainer = $('#notifications-container');
            $('#payment-count').text(`${data.pendingPayments.length} Pending`);
            $('#notif-count').text(`${data.notifications.length} New`);

            // Pulse the sync bar
            $('#sync-bar').css('width', '100%');
            setTimeout(() => $('#sync-bar').css('width', '85%'), 500);

            // Render Payments
            if (data.pendingPayments.length === 0) {
                payContainer.html(`
                    <div class="flex flex-col items-center justify-center py-20 border-2 border-dashed border-[#E5E1D8] rounded-xl text-gray-400">
                        <p class="italic text-sm">All tables cleared.</p>
                    </div>
                `);
            } else {
                payContainer.html(data.pendingPayments.map((order, i) => `
                    <div class="task-card p-8 rounded-2xl flex justify-between items-center animate-in fade-in slide-in-from-right duration-500" style="animation-delay: ${i * 0.1}s">
                        <div>
                            <span class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Table Number</span>
                            <div class="text-5xl font-bold font-mincho text-gray-900 mt-1">${order.table_number}</div>
                            <div class="inline-flex items-center mt-4 bg-red-50 text-[#BC002D] px-3 py-1 rounded-full text-sm font-black">
                                $${parseFloat(order.total_amount).toFixed(2)}
                            </div>
                        </div>
                        <button onclick="approvePayment(${order.id}, this)" class="bg-[#2D2D2D] text-white px-8 py-5 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-[#BC002D] transition-all active:scale-95 shadow-xl shadow-gray-200">
                            Collect
                        </button>
                    </div>
                `).join(''));
            }

            // Render Notifications
            if (data.notifications.length === 0) {
                notifContainer.html('<p class="text-gray-300 italic text-sm text-center py-10">No active alerts.</p>');
            } else {
                notifContainer.html(data.notifications.map((notif, i) => `
                    <div class="notif-card p-6 rounded-xl flex justify-between items-center shadow-sm animate-in fade-in slide-in-from-right duration-500" style="animation-delay: ${i * 0.05}s">
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-800">${notif.message}</p>
                            <span class="text-[10px] text-gray-400 uppercase tracking-widest font-medium">${new Date(notif.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                        <button onclick="markRead(${notif.id})" class="text-gray-200 hover:text-[#BC002D] p-3 transition-colors text-xl">✕</button>
                    </div>
                `).join(''));
            }
        }

        async function fetchData() {
            try {
                const res = await $.get('/restuarent_ordersystem/public/api/waiter/data');
                renderData(res);
            } catch (err) { console.error('Failed to fetch data', err); }
        }

        async function approvePayment(orderId, btn) {
            $(btn).prop('disabled', true).text('PROCESSING...');
            try {
                const res = await fetch('/restuarent_ordersystem/public/waiter/approve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId })
                });
                const result = await res.json();
                if (result.success) fetchData();
            } catch (err) { console.error(err); }
        }

        async function markRead(id) {
            try {
                const res = await fetch('/restuarent_ordersystem/public/waiter/notif-read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await res.json();
                if (result.success) fetchData();
            } catch (err) { console.error(err); }
        }

        // Initial fetch and 10s interval
        fetchData();
        setInterval(fetchData, 10000);
    </script>
</body>
</html>
