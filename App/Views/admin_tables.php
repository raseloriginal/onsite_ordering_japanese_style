<?php 
$title = 'Tables';
include 'admin_layout_header.php'; 
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <span class="text-xs uppercase tracking-widest text-gray-400">Seating Management</span>
        <h2 class="text-4xl font-bold">座席管理</h2>
    </div>
    <button onclick="openTableModal()" class="w-full md:w-auto bg-[#BC002D] text-white px-8 py-4 rounded-sm font-bold uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-red-100">+ Add New Table</button>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6" id="tables-grid">
    <?php foreach($tables as $table): ?>
    <div class="glass-card p-8 rounded-sm shadow-sm flex flex-col items-center text-center group hover:border-[#BC002D] transition-all" id="table-card-<?= $table['id'] ?>">
        <div onclick="showQRCode(<?= $table['id'] ?>, '<?= $table['table_number'] ?>')" 
             class="w-20 h-20 bg-gray-50 border border-[#E5E1D8] rounded-full flex items-center justify-center mb-6 text-3xl font-bold font-mincho text-[#2D2D2D] group-hover:bg-[#BC002D] group-hover:text-white group-hover:border-transparent transition-all cursor-pointer relative"
             title="Click for QR Code">
            <?= $table['table_number'] ?>
            <div class="absolute -bottom-1 -right-1 bg-white text-[#2D2D2D] p-1.5 rounded-full shadow-sm border border-[#E5E1D8] scale-0 group-hover:scale-100 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
        </div>
        <h4 class="font-bold text-gray-800 mb-2 uppercase tracking-widest text-xs">Table <?= $table['table_number'] ?></h4>
        <span class="inline-block px-3 py-1 rounded-full text-[9px] font-bold tracking-[0.1em] border mb-6 <?= $table['status'] == 'available' ? 'border-green-200 text-green-600 bg-green-50' : 'border-orange-200 text-orange-600 bg-orange-50' ?>">
            <?= strtoupper($table['status']) ?>
        </span>
        <div class="w-full flex border-t border-[#E5E1D8] pt-6 gap-4">
            <button onclick='editTable(<?= json_encode($table) ?>)' class="flex-1 text-[10px] text-gray-400 hover:text-gray-900 font-bold uppercase tracking-widest transition-colors">Edit</button>
            <button onclick="deleteTable(<?= $table['id'] ?>)" class="flex-1 text-[10px] text-gray-400 hover:text-[#BC002D] font-bold uppercase tracking-widest transition-colors">Delete</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table Modal -->
<div id="table-modal" class="fixed inset-0 bg-[#2D2D2D]/60 backdrop-blur-sm hidden flex items-center justify-center p-6 z-50">
    <div class="bg-[#FDFCF9] rounded-sm max-w-sm w-full p-10 shadow-2xl animate-in zoom-in duration-300 border border-[#E5E1D8]">
        <h3 id="modal-title" class="text-3xl font-bold mb-8 font-mincho border-b border-[#E5E1D8] pb-4">Add Table</h3>
        <form id="table-form" class="space-y-6">
            <input type="hidden" name="id" id="table-id">
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Table Number / Name</label>
                <input type="text" name="table_number" id="table-number" required class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D] font-mincho text-xl">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                <select name="status" id="table-status" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                </select>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="closeTableModal()" class="flex-1 px-4 py-4 rounded-sm font-bold text-gray-400 hover:text-gray-900 transition-all uppercase tracking-widest text-xs">Cancel</button>
                <button type="submit" class="flex-1 bg-[#2D2D2D] text-white px-4 py-4 rounded-sm font-bold hover:bg-black transition-all shadow-lg shadow-gray-200 uppercase tracking-widest text-xs">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qr-modal" class="fixed inset-0 bg-[#2D2D2D]/60 backdrop-blur-sm hidden flex items-center justify-center p-6 z-[60]">
    <div class="bg-[#FDFCF9] rounded-sm max-w-sm w-full p-10 text-center animate-in zoom-in duration-300 border border-[#E5E1D8]">
        <h3 id="qr-modal-title" class="text-2xl font-bold mb-2 font-mincho">Table QR Code</h3>
        <p id="qr-modal-subtitle" class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-8">Scan to open menu</p>
        
        <div class="bg-white p-4 inline-block border border-[#E5E1D8] mb-8 shadow-sm">
            <img id="qr-image" src="" alt="QR Code" class="w-48 h-48">
        </div>
        
        <div class="space-y-4">
            <button id="download-qr" class="w-full bg-[#BC002D] text-white py-4 rounded-sm font-bold uppercase tracking-widest text-xs hover:opacity-90 transition-all shadow-lg shadow-red-100">
                Download Image
            </button>
            <button onclick="closeQRModal()" class="w-full text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div id="notif-popup" class="fixed top-6 right-6 transform translate-x-[150%] transition-transform duration-500 z-[100]">
    <div id="notif-content" class="bg-[#2D2D2D] text-white px-8 py-4 rounded-sm shadow-2xl flex items-center gap-4 border border-gray-700">
        <div id="notif-icon" class="w-6 h-6 rounded-full flex items-center justify-center font-bold">✓</div>
        <p id="notif-text" class="font-bold tracking-widest uppercase text-[10px]"></p>
    </div>
</div>

<script>
    const modal = document.getElementById('table-modal');
    const qrModal = document.getElementById('qr-modal');
    const form = document.getElementById('table-form');
    const notif = document.getElementById('notif-popup');

    function openTableModal() {
        document.getElementById('modal-title').innerText = 'Add New Table';
        form.reset();
        document.getElementById('table-id').value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeTableModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editTable(table) {
        document.getElementById('modal-title').innerText = 'Edit Table';
        document.getElementById('table-id').value = table.id;
        document.getElementById('table-number').value = table.table_number;
        document.getElementById('table-status').value = table.status;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    async function showQRCode(id, tableNumber) {
        const url = '<?= url('/table/') ?>' + tableNumber;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(url)}`;
        
        document.getElementById('qr-modal-title').innerText = `Table ${tableNumber} QR`;
        document.getElementById('qr-image').src = qrUrl;
        
        // Setup download button
        const downloadBtn = document.getElementById('download-qr');
        downloadBtn.onclick = async () => {
            try {
                const response = await fetch(qrUrl);
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = `table_${tableNumber}_qr.png`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(blobUrl);
            } catch (err) {
                showPopup('Failed to download QR code', 'error');
            }
        };

        qrModal.classList.remove('hidden');
        qrModal.classList.add('flex');
    }

    function closeQRModal() {
        qrModal.classList.add('hidden');
        qrModal.classList.remove('flex');
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('<?= url('/admin/tables/save') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Success! Tables Updated.', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showPopup('Error saving table.', 'error');
        }
    }

    async function deleteTable(id) {
        if (!confirm('Delete this table?')) return;
        try {
            const response = await fetch('<?= url('/admin/tables/delete') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Table Removed.', 'success');
                document.getElementById(`table-card-${id}`).remove();
            }
        } catch (err) {
            showPopup('Error deleting table.', 'error');
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
            iconEl.className = 'w-6 h-6 rounded-full flex items-center justify-center font-bold bg-[#BC002D] text-white';
        }
        
        notif.style.transform = 'translateX(0)';
        setTimeout(() => {
            notif.style.transform = 'translateX(150%)';
        }, 3000);
    }
</script>

<?php include 'admin_layout_footer.php'; ?>
