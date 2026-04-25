<?php 
$title = 'Income Logs';
include 'admin_layout_header.php'; 
?>

<div class="mb-12">
    <span class="text-xs uppercase tracking-widest text-gray-400">Revenue Records</span>
    <h2 class="text-4xl font-bold">入金履歴</h2>
    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">A detailed record of all completed payments.</p>
</div>

<div class="glass-card rounded-sm overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-[#E5E1D8] text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold">
                <tr>
                    <th class="px-8 py-5">Order ID</th>
                    <th class="px-8 py-5">Table</th>
                    <th class="px-8 py-5">Method</th>
                    <th class="px-8 py-5">Amount</th>
                    <th class="px-8 py-5">Time</th>
                    <th class="px-8 py-5 text-right">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E1D8]">
                <?php if(empty($income)): ?>
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-gray-400 italic text-sm">No income records found.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($income as $log): ?>
                <tr class="hover:bg-[#FDFCF9] transition-colors">
                    <td class="px-8 py-6 font-mono text-[10px] text-gray-400 uppercase tracking-tighter">#ORD-<?= str_pad($log['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-8 py-6">
                        <span class="bg-gray-50 border border-[#E5E1D8] px-3 py-1 rounded-sm text-[10px] font-bold text-gray-700 uppercase tracking-widest">Table <?= $log['table_number'] ?></span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-[9px] font-bold uppercase tracking-[0.1em] px-3 py-1 rounded-full border <?= $log['payment_method'] == 'online' ? 'border-blue-200 text-blue-600 bg-blue-50' : 'border-orange-200 text-orange-600 bg-orange-50' ?>">
                            <?= strtoupper($log['payment_method']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 font-bold text-lg font-mincho text-gray-800">$<?= number_format($log['total_amount'], 2) ?></td>
                    <td class="px-8 py-6 text-[10px] text-gray-400 uppercase tracking-widest"><?= date('M d, H:i', strtotime($log['created_at'])) ?></td>
                    <td class="px-8 py-6 text-right">
                        <button class="text-[#BC002D] hover:underline text-[10px] font-bold uppercase tracking-widest">Print</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'admin_layout_footer.php'; ?>
