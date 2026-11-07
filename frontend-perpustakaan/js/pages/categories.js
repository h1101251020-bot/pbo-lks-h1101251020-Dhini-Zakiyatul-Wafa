
async function renderCategories() {
    try {
        const response = await api.get('/categories');
        const categories = response.data || [];

        return `
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Data Kategori
                    </h2>

                    <button
                        onclick="openCategoryModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        + Tambah Kategori
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Nama Kategori</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            ${
                                categories.length
                                ? categories.map(category => `
                                    <tr class="border-t">
                                        <td class="p-4">${category.id}</td>
                                        <td class="p-4">${escapeHtml(category.nama)}</td>
                                        <td class="p-4">${escapeHtml(category.deskripsi || '-')}</td>
                                        <td class="p-4 whitespace-nowrap">
                                            <button
                                                onclick="openCategoryModal(${category.id})"
                                                class="text-blue-600 mr-3">
                                                Edit
                                            </button>

                                            <button
                                                onclick="deleteCategory(${category.id})"
                                                class="text-red-600">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')
                                : `
                                    <tr>
                                        <td colspan="4" class="p-4 text-center text-gray-500">
                                            Belum ada data kategori.
                                        </td>
                                    </tr>
                                `
                            }
                        </tbody>
                    </table>
                </div>

                <div id="category-modal"></div>
            </div>
        `;
    } catch (error) {
        console.error('Gagal memuat kategori:', error);

        return `
            <p class="bg-red-100 text-red-700 p-4 rounded-lg">
                Gagal memuat data kategori.
            </p>
        `;
    }
}

// Membuka modal tambah atau edit kategori
async function openCategoryModal(id = null) {
    let category = {
        nama: '',
        deskripsi: ''
    };

    if (id !== null) {
        const response = await api.get(`/categories/${id}`);

        if (!response.success || !response.data) {
            showToast('Data kategori tidak ditemukan', 'error');
            return;
        }

        category = response.data;
    }

    const modal = document.getElementById('category-modal');

    modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <h3 class="text-xl font-bold mb-4">
                    ${id === null ? 'Tambah Kategori' : 'Edit Kategori'}
                </h3>

                <form id="category-form" class="space-y-4">
                    <div>
                        <label class="block mb-1">Nama Kategori</label>
                        <input
                            name="nama"
                            value="${escapeHtml(category.nama)}"
                            class="w-full border rounded-lg p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-1">Deskripsi</label>
                        <textarea
                            name="deskripsi"
                            class="w-full border rounded-lg p-2"
                            rows="3">${escapeHtml(category.deskripsi || '')}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="closeCategoryModal()"
                            class="px-4 py-2 bg-gray-200 rounded-lg">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.getElementById('category-form').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        const data = {
            nama: formData.get('nama'),
            deskripsi: formData.get('deskripsi')
        };

        try {
            const result = id === null
                ? await api.post('/categories', data)
                : await api.put(`/categories/${id}`, data);

            if (result.success) {
                closeCategoryModal();
                showToast('Data kategori berhasil disimpan');

                document.getElementById('app').innerHTML =
                    await renderCategories();
            } else {
                showToast(result.message || 'Gagal menyimpan kategori', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan saat menyimpan kategori', 'error');
        }
    });
}

// Menutup modal
function closeCategoryModal() {
    document.getElementById('category-modal').innerHTML = '';
}

// Menghapus kategori
async function deleteCategory(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        return;
    }

    try {
        const result = await api.delete(`/categories/${id}`);

        if (result.success) {
            showToast('Kategori berhasil dihapus');

            document.getElementById('app').innerHTML =
                await renderCategories();
        } else {
            showToast(result.message || 'Gagal menghapus kategori', 'error');
        }
    } catch (error) {
        showToast('Terjadi kesalahan saat menghapus kategori', 'error');
    }
}
