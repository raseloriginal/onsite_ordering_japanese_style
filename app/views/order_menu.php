<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakura Order - Table <?= $table['table_number'] ?></title>
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
            overflow: hidden;
        }
        h1, h2, h3, .font-mincho { font-family: 'Shippori Mincho', serif; }
        
        /* Game-like Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        @keyframes popIn {
            0% { transform: scale(0.9) opacity(0); }
            100% { transform: scale(1) opacity(1); }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes flash {
            0% { background-color: transparent; }
            50% { background-color: rgba(188, 0, 45, 0.1); }
            100% { background-color: transparent; }
        }

        .category-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .category-btn.active { 
            color: var(--jp-red); 
            transform: scale(1.1);
        }
        .category-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 20px;
            height: 2px;
            background: var(--jp-red);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .item-card { 
            border: 1px solid var(--jp-border); 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
        }
        .item-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--game-shadow);
            border-color: var(--jp-red);
        }
        .item-card:active { transform: scale(0.95); }

        .add-btn {
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .add-btn::after {
            content: '+';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--jp-red);
            transform: translateY(100%);
            transition: transform 0.2s;
        }
        .add-btn:hover::after { transform: translateY(0); }

        .cart-sidebar { 
            transition: transform 0.5s cubic-bezier(0.77, 0, 0.175, 1);
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        /* RPG Progress Bar */
        .xp-bar {
            height: 4px;
            background: #eee;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 4px;
        }
        .xp-fill {
            height: 100%;
            background: var(--jp-red);
            width: 0%;
            transition: width 0.5s ease;
        }

        @media (max-width: 768px) {
            .cart-sidebar { 
                position: fixed; bottom: 0; left: 0; right: 0; width: 100vw; height: 80vh; 
                transform: translateY(100%);
                z-index: 50; border-top: 1px solid var(--jp-border); border-radius: 0;
                box-shadow: 0 -20px 40px rgba(0,0,0,0.1);
            }
            .cart-sidebar.active { transform: translateY(0); }
            .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); z-index: 40; display: none; }
            .cart-overlay.active { display: block; }
        }

        .floating-notif {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--jp-charcoal);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            z-index: 100;
            display: none;
            animation: slideUp 0.3s ease-out;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col">
    <!-- Game Style Notification -->
    <div id="game-notif" class="floating-notif">Item Added to Inventory</div>

    <!-- Header -->
    <header class="glass-header border-b border-[#E5E1D8] px-6 py-4 flex justify-between items-center shrink-0 z-20">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-[#BC002D] rounded-sm flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-red-200">桜</div>
            <div>
                <h1 class="text-xl font-bold font-mincho tracking-tighter">Sakura</h1>
                <div class="xp-bar w-12"><div class="xp-fill"></div></div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden md:flex flex-col items-end mr-4">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Active Table</span>
                <span class="text-xs font-bold font-mincho">NO. <?= $table['table_number'] ?></span>
            </div>
            <button onclick="toggleCart()" class="relative p-2 bg-gray-50 border border-[#E5E1D8] rounded-full hover:border-[#BC002D] transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-[#BC002D]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span id="cart-count-header" class="absolute -top-1 -right-1 bg-[#BC002D] text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold">0</span>
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Main Menu -->
        <main class="flex-1 flex flex-col overflow-hidden bg-[#FDFCF9]/50">
            <!-- Categories -->
            <div class="px-6 py-4 border-b border-[#E5E1D8] bg-white/50 overflow-x-auto flex gap-8 no-scrollbar shrink-0 z-10">
                <button class="category-btn active pb-2 whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-gray-400" onclick="filterCategory('all', this)">All Items</button>
                <?php foreach($categories as $cat): ?>
                <button class="category-btn pb-2 whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-gray-400" onclick="filterCategory(<?= $cat['id'] ?>, this)"><?= $cat['name'] ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Items Grid -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 md:gap-8 content-start" id="items-grid">
                <?php foreach($items as $i => $item): ?>
                <div class="item-card p-3 md:p-5 rounded-xl flex flex-col group" 
                     data-category="<?= $item['category_id'] ?>"
                     style="animation: slideUp 0.4s ease-out <?= $i * 0.05 ?>s backwards;">
                    <div class="aspect-square bg-gray-50 rounded-lg mb-4 overflow-hidden relative">
                        <img src="<?= $item['image'] ?? 'https://via.placeholder.com/300' ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors pointer-events-none"></div>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 font-mincho text-lg truncate"><?= $item['name'] ?></h3>
                    <p class="text-[10px] text-gray-400 line-clamp-2 mb-4 uppercase tracking-tighter"><?= $item['description'] ?></p>
                    <div class="mt-auto flex justify-between items-center">
                        <span class="font-bold text-xl font-mincho text-gray-900">$<?= number_format($item['price'], 2) ?></span>
                        <button onclick="addToCart(<?= htmlspecialchars(json_encode($item)) ?>)" 
                                class="add-btn bg-[#2D2D2D] text-white w-10 h-10 rounded-lg flex items-center justify-center shadow-lg shadow-gray-200 transition-all hover:bg-[#BC002D] hover:shadow-red-100">
                            <span class="text-lg font-light">+</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Cart Overlay (Mobile Only) -->
        <div class="cart-overlay" onclick="toggleCart()"></div>
<!--  -->
        <!-- Sidebar Cart -->
        <aside class="cart-sidebar w-full md:w-96 border-l border-[#E5E1D8] bg-white flex flex-col shrink-0">
            <div class="p-8 border-b border-[#E5E1D8] flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold font-mincho">ご注文</h2>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-1">Current Order Inventory</p>
                </div>
                <button onclick="toggleCart()" class="md:hidden w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400">✕</button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-8" id="cart-items">
                <div class="flex flex-col items-center justify-center h-full text-gray-300 italic text-sm space-y-4">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p>Your bag is empty.</p>
                </div>
            </div>

            <div class="p-10 border-t border-[#E5E1D8] bg-[#FDFCF9]">
                <div class="space-y-3 mb-8">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Subtotal</span>
                        <span class="font-bold font-mincho text-gray-600" id="cart-subtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-dotted border-[#E5E1D8]">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Grand Total</span>
                        <span class="text-3xl font-bold font-mincho text-[#BC002D]" id="cart-total">$0.00</span>
                    </div>
                </div>
                <button onclick="showCheckout()" class="w-full bg-[#2D2D2D] text-white py-5 rounded-xl font-bold uppercase tracking-[0.3em] text-[10px] shadow-xl shadow-gray-200 active:scale-95 transition-all disabled:opacity-50 disabled:active:scale-100 hover:bg-black group" id="checkout-btn" disabled>
                    Complete Order <span class="group-hover:translate-x-1 inline-block transition-transform ml-2">→</span>
                </button>
            </div>
        </aside>
    </div>

    <!-- Mobile Floating UI -->
    <div class="md:hidden fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4 z-40">
        <button onclick="toggleCart()" class="bg-[#2D2D2D] text-white px-8 py-4 rounded-full shadow-2xl flex items-center gap-4 transition-all active:scale-95 group">
            <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Items</span>
            <span id="cart-count-fab" class="bg-white text-[#2D2D2D] text-[10px] w-6 h-6 rounded-full flex items-center justify-center font-black">0</span>
        </button>
    </div>

    <!-- Checkout Modal (RPG Style) -->
    <div id="checkout-modal" class="fixed inset-0 bg-[#2D2D2D]/80 backdrop-blur-md hidden flex items-center justify-center p-0 md:p-6 z-[100]">
        <div class="bg-[#FDFCF9] h-full w-full md:h-auto md:max-w-lg md:rounded-2xl p-12 text-center shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] border border-white/20 animate-in zoom-in duration-500 overflow-y-auto">
            <div class="w-20 h-20 bg-[#BC002D] rounded-full mx-auto mb-8 flex items-center justify-center text-white text-3xl font-bold shadow-2xl shadow-red-500/40">桜</div>
            <h2 class="text-4xl font-bold mb-3 font-mincho">確認</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-12">Finalize Your Selection</p>
            
            <div class="grid grid-cols-1 gap-6 mb-12">
                <button onclick="confirmOrder('online')" class="p-8 border border-[#E5E1D8] rounded-xl text-left hover:border-[#BC002D] hover:bg-white transition-all group flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xl text-gray-800 group-hover:text-[#BC002D] font-mincho mb-1">Online Checkout</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest">Instant Digital Payment</div>
                    </div>
                    <div class="w-10 h-10 border border-[#E5E1D8] rounded-full flex items-center justify-center group-hover:border-[#BC002D] transition-colors">→</div>
                </button>
                <button onclick="confirmOrder('hand')" class="p-8 border border-[#E5E1D8] rounded-xl text-left hover:border-[#BC002D] hover:bg-white transition-all group flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xl text-gray-800 group-hover:text-[#BC002D] font-mincho mb-1">Pay to Staff</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest">Cash or Card at Table</div>
                    </div>
                    <div class="w-10 h-10 border border-[#E5E1D8] rounded-full flex items-center justify-center group-hover:border-[#BC002D] transition-colors">→</div>
                </button>
            </div>
            
            <button onclick="closeCheckout()" class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] hover:text-[#BC002D] transition-colors py-4 px-8">Back to Selection</button>
        </div>
    </div>

    <script>
        let cart = [];
        const tableId = <?= $table['id'] ?>;

        function showGameNotif(msg) {
            const notif = $('#game-notif');
            notif.text(msg).fadeIn().css('display', 'block');
            setTimeout(() => notif.fadeOut(), 2000);
        }

        function toggleCart() {
            if (window.innerWidth > 768) return;
            $('.cart-sidebar').toggleClass('active');
            $('.cart-overlay').toggleClass('active');
        }

        function addToCart(item) {
            const existing = cart.find(i => i.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ ...item, qty: 1 });
            }
            updateCartUI();
            showGameNotif(`+1 ${item.name}`);
            
            // Pulse XP Bar
            $('.xp-fill').css('width', '100%');
            setTimeout(() => {
                const progress = (cart.length / 10) * 100;
                $('.xp-fill').css('width', `${Math.min(progress, 100)}%`);
            }, 500);
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.qty += delta;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
            }
            updateCartUI();
        }

        function updateCartUI() {
            const cartList = document.getElementById('cart-items');
            const subtotalEl = document.getElementById('cart-subtotal');
            const totalEl = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const cartCountHeader = document.getElementById('cart-count-header');
            const cartCountFab = document.getElementById('cart-count-fab');

            const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
            cartCountHeader.innerText = totalQty;
            if(cartCountFab) cartCountFab.innerText = totalQty;

            if (cart.length === 0) {
                cartList.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-300 italic text-sm space-y-4">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center opacity-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg></div>
                    <p>Your bag is empty.</p>
                </div>`;
                checkoutBtn.disabled = true;
            } else {
                cartList.innerHTML = cart.map(item => `
                    <div class="flex gap-5 mb-8 animate-in slide-in-from-right duration-500">
                        <div class="w-20 h-20 bg-gray-50 border border-[#E5E1D8] rounded-xl shrink-0 overflow-hidden shadow-sm">
                            <img src="${item.image || 'https://via.placeholder.com/300'}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm text-gray-800 font-mincho">${item.name}</h4>
                            <div class="text-[10px] text-gray-400 mb-4 tracking-widest uppercase">$${parseFloat(item.price).toFixed(2)}</div>
                            <div class="flex items-center gap-5">
                                <button onclick="updateQty(${item.id}, -1)" class="w-8 h-8 border border-[#E5E1D8] flex items-center justify-center text-gray-400 hover:text-[#BC002D] rounded-lg transition-colors">-</button>
                                <span class="text-xs font-black font-mincho">${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)" class="w-8 h-8 border border-[#E5E1D8] flex items-center justify-center text-gray-400 hover:text-[#BC002D] rounded-lg transition-colors">+</button>
                            </div>
                        </div>
                        <div class="text-sm font-bold font-mincho text-[#BC002D]">
                            $${(item.price * item.qty).toFixed(2)}
                        </div>
                    </div>
                `).join('');
                checkoutBtn.disabled = false;
            }

            const total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            subtotalEl.innerText = `$${total.toFixed(2)}`;
            totalEl.innerText = `$${total.toFixed(2)}`;
        }

        function filterCategory(catId, btn) {
            $('.category-btn').removeClass('active');
            $(btn).addClass('active');

            const items = $('#items-grid > div');
            items.each(function() {
                if (catId === 'all' || $(this).data('category') == catId) {
                    $(this).css('display', 'flex').css('animation', 'popIn 0.3s ease-out backwards');
                } else {
                    $(this).css('display', 'none');
                }
            });
        }

        function showCheckout() {
            $('#checkout-modal').removeClass('hidden').addClass('flex');
        }

        function closeCheckout() {
            $('#checkout-modal').addClass('hidden').removeClass('flex');
        }

        async function confirmOrder(method) {
            const total = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            const orderData = {
                table_id: tableId,
                items: cart,
                total_amount: total,
                payment_method: method
            };

            try {
                const response = await fetch('/restuarent_ordersystem/public/api/order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
                const result = await response.json();
                if (result.success) {
                    showGameNotif("Mission Complete: Order Sent!");
                    cart = [];
                    updateCartUI();
                    closeCheckout();
                    if (window.innerWidth <= 768) toggleCart();
                }
            } catch (err) {
                console.error(err);
                showGameNotif("Error: Network Failure");
            }
        }

        // Initialize XP Bar
        setTimeout(() => $('.xp-fill').css('width', '5%'), 500);
    </script>
</body>
</html>
