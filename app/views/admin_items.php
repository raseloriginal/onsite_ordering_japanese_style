<?php 
$title = 'Menu Items';
include 'admin_layout_header.php'; 
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <span class="text-xs uppercase tracking-widest text-gray-400">Inventory Management</span>
        <h2 class="text-4xl font-bold">メニュー商品</h2>
    </div>
    <button onclick="openModal()" class="w-full md:w-auto bg-[#BC002D] text-white px-8 py-4 rounded-sm font-bold uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-red-100">+ Add New Item</button>
</div>

<div class="glass-card rounded-sm overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-[#E5E1D8] text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold">
                <tr>
                    <th class="px-8 py-5">Image</th>
                    <th class="px-8 py-5">Name / Category</th>
                    <th class="px-8 py-5">Price</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E1D8]">
                <?php foreach($items as $item): ?>
                <tr class="hover:bg-[#FDFCF9] transition-colors" id="item-row-<?= $item['id'] ?>">
                    <td class="px-8 py-6">
                        <div class="w-14 h-14 bg-gray-50 border border-[#E5E1D8] p-1 rounded-sm">
                            <img src="<?= $item['image'] ?>" class="w-full h-full object-cover rounded-sm">
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="font-bold text-gray-800 font-mincho text-lg"><?= $item['name'] ?></div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest mt-1"><?= $item['category_name'] ?></div>
                    </td>
                    <td class="px-8 py-6 font-bold text-lg font-mincho">$<?= number_format($item['price'], 2) ?></td>
                    <td class="px-8 py-6">
                        <span class="inline-block px-3 py-1 rounded-full text-[9px] font-bold tracking-widest border <?= $item['status'] == 'available' ? 'border-green-200 text-green-600 bg-green-50' : 'border-red-200 text-[#BC002D] bg-red-50' ?>">
                            <?= strtoupper($item['status']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-4">
                            <button onclick='editItem(<?= json_encode($item) ?>)' class="text-gray-400 hover:text-gray-900 transition-colors uppercase text-[10px] font-bold tracking-widest">Edit</button>
                            <button onclick="deleteItem(<?= $item['id'] ?>)" class="text-gray-400 hover:text-[#BC002D] transition-colors uppercase text-[10px] font-bold tracking-widest">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="item-modal" class="fixed inset-0 bg-[#2D2D2D]/60 backdrop-blur-sm hidden flex items-center justify-center p-6 z-50">
    <div class="bg-[#FDFCF9] rounded-sm max-w-lg w-full p-10 shadow-2xl animate-in zoom-in duration-300 border border-[#E5E1D8]">
        <h3 id="modal-title" class="text-3xl font-bold mb-8 font-mincho border-b border-[#E5E1D8] pb-4">Add New Item</h3>
        <form id="item-form" class="space-y-6">
            <input type="hidden" name="id" id="item-id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Item Name</label>
                    <input type="text" name="name" id="item-name" required class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D] transition-colors font-mincho text-lg">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Category</label>
                    <select name="category_id" id="item-category" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="item-price" required class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Image URL</label>
                    <input type="text" name="image" id="item-image" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" id="item-desc" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]" rows="3"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                    <select name="status" id="item-status" class="w-full bg-white border border-[#E5E1D8] rounded-sm px-4 py-3 focus:outline-none focus:border-[#BC002D]">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="closeModal()" class="flex-1 px-6 py-4 rounded-sm font-bold text-gray-400 hover:text-gray-900 transition-all uppercase tracking-widest text-xs border border-transparent">Cancel</button>
                <button type="submit" class="flex-1 bg-[#2D2D2D] text-white px-6 py-4 rounded-sm font-bold hover:bg-black transition-all shadow-lg shadow-gray-200 uppercase tracking-widest text-xs">Save Item</button>
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
    const modal = document.getElementById('item-modal');
    const form = document.getElementById('item-form');
    const notif = document.getElementById('notif-popup');

    function openModal() {
        document.getElementById('modal-title').innerText = 'Add New Item';
        form.reset();
        document.getElementById('item-id').value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editItem(item) {
        document.getElementById('modal-title').innerText = 'Edit Item';
        document.getElementById('item-id').value = item.id;
        document.getElementById('item-name').value = item.name;
        document.getElementById('item-category').value = item.category_id;
        document.getElementById('item-price').value = item.price;
        document.getElementById('item-image').value = item.image;
        document.getElementById('item-desc').value = item.description;
        document.getElementById('item-status').value = item.status;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('<?= url('/admin/items/save') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Success! Inventory Updated.', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showPopup('Error saving item.', 'error');
        }
    }

    async function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;
        
        try {
            const response = await fetch('<?= url('/admin/items/delete') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Item Deleted.', 'success');
                document.getElementById(`item-row-${id}`).remove();
            }
        } catch (err) {
            showPopup('Error deleting item.', 'error');
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
    const modal = document.getElementById('item-modal');
    const form = document.getElementById('item-form');
    const notif = document.getElementById('notif-popup');

    function openModal() {
        document.getElementById('modal-title').innerText = 'Add New Item';
        form.reset();
        document.getElementById('item-id').value = '';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function editItem(item) {
        document.getElementById('modal-title').innerText = 'Edit Item';
        document.getElementById('item-id').value = item.id;
        document.getElementById('item-name').value = item.name;
        document.getElementById('item-category').value = item.category_id;
        document.getElementById('item-price').value = item.price;
        document.getElementById('item-image').value = item.image;
        document.getElementById('item-desc').value = item.description;
        document.getElementById('item-status').value = item.status;
        modal.classList.remove('hidden');
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('<?= url('/admin/items/save') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Success! Menu updated.', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            showPopup('Error saving item.', 'error');
        }
    }

    async function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;
        
        try {
            const response = await fetch('<?= url('/admin/items/delete') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                showPopup('Item deleted.', 'success');
                document.getElementById(`item-row-${id}`).remove();
            }
        } catch (err) {
            showPopup('Error deleting item.', 'error');
        }
    }

    function showPopup(text, type = 'success') {
        const textEl = document.getElementById('notif-text');
        const iconEl = document.getElementById('notif-icon');
        const contentEl = document.getElementById('notif-content');
        
        textEl.innerText = text;
        if (type === 'success') {
            iconEl.innerText = '✓';
            iconEl.className = 'w-6 h-6 rounded-full flex items-center justify-center font-bold bg-green-500 text-white';
        } else {
            iconEl.innerText = '!';
            iconEl.className = 'w-6 h-6 rounded-full flex items-center justify-center font-bold bg-red-500 text-white';
        }
        
        notif.style.transform = 'translateX(0)';
        setTimeout(() => {
            notif.style.transform = 'translateX(150%)';
        }, 3000);
    }
</script>

<?php include 'admin_layout_footer.php'; ?>
