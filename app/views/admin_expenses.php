<?php 
$title = 'Expenses';
include 'admin_layout_header.php'; 
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <span class="text-xs uppercase tracking-widest text-gray-400">Financial Records</span>
        <h2 class="text-4xl font-bold">経費支出</h2>
    </div>
    <button onclick="openExpenseModal()" class="w-full md:w-auto bg-[#BC002D] text-white px-8 py-4 rounded-sm font-bold uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-red-100">+ Record Expense</button>
</div>

<div class="glass-card rounded-sm overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-[#E5E1D8] text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold">
                <tr>
                    <th class="px-8 py-5">Title / Category</th>
                    <th class="px-8 py-5">Amount</th>
                    <th class="px-8 py-5">Date</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E1D8]" id="expenses-body">
                <?php foreach($expenses as $exp): ?>
                <tr class="hover:bg-[#FDFCF9] transition-colors" id="expense-row-<?= $exp['id'] ?>">
                    <td class="px-8 py-6">
                        <div class="font-bold text-gray-800 font-mincho text-lg"><?= $exp['title'] ?></div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest mt-1"><?= $exp['category'] ?></div>
                    </td>
                    <td class="px-8 py-6 font-bold text-lg font-mincho text-[#BC002D]">-$<?= number_format($exp['amount'], 2) ?></td>
                    <td class="px-8 py-6 text-xs text-gray-400 uppercase tracking-widest"><?= date('M d, Y', strtotime($exp['expense_date'])) ?></td>
                    <td class="px-8 py-6 text-right">
                        <button onclick="deleteExpense(<?= $exp['id'] ?>)" class="text-[10px] text-gray-400 hover:text-[#BC002D] font-bold uppercase tracking-widest transition-colors">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Expense Modal -->
<div id="expense-modal" class="fixed inset-0 bg-[#2D2D2D]/60 backdrop-blur-sm hidden flex items-center justify-center p-6 z-50">
    <div class="bg-[#FDFCF9] rounded-sm max-w-sm w-full p-10 shadow-2xl animate-in zoom-in duration-300 border border-[#E5E1D8]">
        <h3 class="text-3xl font-bold mb-8 font-mincho border-b border-[#E5E1D8] pb-4">Record Expense</h3>
        <form id="expense-form" class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Title</label>
                <input type="text" name="title" required class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D] font-mincho text-xl">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Category</label>
                <input type="text" name="category" placeholder="e.g. Rent, Ingredients" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Amount ($)</label>
                <input type="number" step="0.01" name="amount" required class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Date</label>
                <input type="date" name="expense_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="closeExpenseModal()" class="flex-1 px-4 py-4 rounded-sm font-bold text-gray-400 hover:text-gray-900 transition-all uppercase tracking-widest text-xs">Cancel</button>
                <button type="submit" class="flex-1 bg-[#2D2D2D] text-white px-4 py-4 rounded-sm font-bold hover:bg-black transition-all shadow-lg shadow-gray-200 uppercase tracking-widest text-xs">Record</button>
            </div>
        </form>
    </div>
</div>

<!-- Notification Toast -->
<div id="notif-popup" class="fixed top-6 right-6 transform translate-x-[150%] transition-transform duration-500 z-[100]">
    <div id="notif-content" class="bg-[#2D2D2D] text-white px-8 py-4 rounded-sm shadow-2xl flex items-center gap-4 border border-gray-700">
        <div id="notif-icon" class="text-xl font-bold text-[#BC002D]">桜</div>
        <p id="notif-text" class="font-bold tracking-widest uppercase text-[10px]"></p>
    </div>
</div>

<script>
    const modal = document.getElementById('expense-modal');
    const form = document.getElementById('expense-form');
    const notif = document.getElementById('notif-popup');

    function openExpenseModal() {
        form.reset();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeExpenseModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/restuarent_ordersystem/public/admin/expenses/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Expense Recorded.', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showPopup('Error recording expense.', 'error');
        }
    }

    async function deleteExpense(id) {
        if (!confirm('Delete this expense record?')) return;
        try {
            const response = await fetch('/restuarent_ordersystem/public/admin/expenses/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Expense Removed.', 'success');
                document.getElementById(`expense-row-${id}`).remove();
            }
        } catch (err) {
            showPopup('Error deleting expense.', 'error');
        }
    }

    function showPopup(text, type = 'success') {
        const textEl = document.getElementById('notif-text');
        textEl.innerText = text;
        notif.style.transform = 'translateX(0)';
        setTimeout(() => {
            notif.style.transform = 'translateX(150%)';
        }, 3000);
    }
</script>

<?php include 'admin_layout_footer.php'; ?>

<script>
    const modal = document.getElementById('expense-modal');
    const form = document.getElementById('expense-form');
    const notif = document.getElementById('notif-popup');

    function openExpenseModal() {
        form.reset();
        modal.classList.remove('hidden');
    }

    function closeExpenseModal() {
        modal.classList.add('hidden');
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/restuarent_ordersystem/public/admin/expenses/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Expense recorded.', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showPopup('Error recording expense.', 'error');
        }
    }

    async function deleteExpense(id) {
        if (!confirm('Delete this expense record?')) return;
        try {
            const response = await fetch('/restuarent_ordersystem/public/admin/expenses/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Expense removed.', 'success');
                document.getElementById(`expense-row-${id}`).remove();
            }
        } catch (err) {
            showPopup('Error deleting expense.', 'error');
        }
    }

    function showPopup(text, type = 'success') {
        const textEl = document.getElementById('notif-text');
        const iconEl = document.getElementById('notif-icon');
        textEl.innerText = text;
        if (type === 'success') {
            iconEl.innerText = '✓';
            iconEl.className = 'w-6 h-6 rounded-full flex items-center justify-center font-bold bg-green-500 text-white';
        } else {
            iconEl.innerText = '!';
            iconEl.className = 'w-6 h-6 rounded-full flex items-center justify-center font-bold bg-red-500 text-white';
        }
        notif.style.transform = 'translateX(0)';
        setTimeout(() => notif.style.transform = 'translateX(150%)', 3000);
    }
</script>

<?php include 'admin_layout_footer.php'; ?>
