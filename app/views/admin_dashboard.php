<?php include 'admin_layout_header.php'; ?>

<header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
    <div>
        <span class="text-xs uppercase tracking-widest text-gray-400">Management Overview</span>
        <h2 class="text-4xl font-bold">全体概要</h2>
    </div>
    <div class="glass-card px-4 py-2 rounded-sm flex items-center gap-3">
        <div class="w-2 h-2 bg-[#BC002D] rounded-full animate-pulse"></div>
        <span class="text-xs font-bold uppercase tracking-widest">System Active</span>
    </div>
</header>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <div class="glass-card p-8 rounded-sm shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <div class="text-6xl font-bold font-mincho text-[#BC002D]">金</div>
        </div>
        <p class="text-gray-400 text-xs uppercase tracking-widest mb-2">Total Revenue</p>
        <h3 class="text-4xl font-bold font-mincho">$<?= number_format($revenue, 2) ?></h3>
        <div class="text-green-600 text-[10px] mt-4 font-bold tracking-widest">↑ 12% FROM PREVIOUS WEEK</div>
    </div>
    
    <div class="glass-card p-8 rounded-sm shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <div class="text-6xl font-bold font-mincho text-gray-400">注</div>
        </div>
        <p class="text-gray-400 text-xs uppercase tracking-widest mb-2">Orders Completed</p>
        <h3 class="text-4xl font-bold font-mincho"><?= $orderCount ?></h3>
        <div class="text-gray-400 text-[10px] mt-4 font-bold tracking-widest">ACTIVE TABLES TRACKED</div>
    </div>
    
    <div class="glass-card p-8 rounded-sm shadow-sm relative overflow-hidden group sm:col-span-2 lg:col-span-1">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <div class="text-6xl font-bold font-mincho text-red-300">出</div>
        </div>
        <p class="text-gray-400 text-xs uppercase tracking-widest mb-2">Total Expenses</p>
        <h3 class="text-4xl font-bold font-mincho text-[#BC002D]">$<?= number_format($expenses, 2) ?></h3>
        <div class="text-red-400 text-[10px] mt-4 font-bold tracking-widest">OPERATING COSTS</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-12">
    <div class="glass-card p-8 rounded-sm shadow-sm xl:col-span-2">
        <h4 class="text-sm font-bold uppercase tracking-widest mb-8 flex items-center gap-2">
            <span class="w-1 h-4 bg-[#BC002D]"></span>
            収益傾向 (Weekly Trend)
        </h4>
        <div class="h-[300px] relative">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>
    <div class="glass-card p-8 rounded-sm shadow-sm">
        <h4 class="text-sm font-bold uppercase tracking-widest mb-8 flex items-center gap-2">
            <span class="w-1 h-4 bg-gray-300"></span>
            損益分配 (Distribution)
        </h4>
        <div class="h-[300px] relative">
            <canvas id="profitChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Income Chart
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    const incomeData = <?= json_encode($dailyIncome) ?>;
    
    new Chart(incomeCtx, {
        type: 'line',
        data: {
            labels: incomeData.map(d => d.date),
            datasets: [{
                label: 'Revenue',
                data: incomeData.map(d => d.total),
                borderColor: '#BC002D',
                backgroundColor: 'rgba(188, 0, 45, 0.05)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#BC002D',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Profit Chart
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    new Chart(profitCtx, {
        type: 'doughnut',
        data: {
            labels: ['Income', 'Expense'],
            datasets: [{
                data: [<?= $revenue ?>, <?= $expenses ?>],
                backgroundColor: ['#2D2D2D', '#BC002D'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            cutout: '80%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 10, family: 'Noto Sans JP' }
                    }
                } 
            }
        }
    });
</script>

<?php include 'admin_layout_footer.php'; ?>
