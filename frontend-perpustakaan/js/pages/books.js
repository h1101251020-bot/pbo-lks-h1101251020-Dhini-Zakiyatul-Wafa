
async function renderBooks() {
    try {
        const response = await api.get('/books');
        const books = response.data || [];

        return `
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Data Buku
                    </h2>

                    <button
                        onclick="openBookModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        + Tambah Buku
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Judul</th>
                                <th class="p-4">Penulis</th>
                                <th class="p-4">Tahun</th>
                                <th class="p-4">Stok</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            ${
                                books.length
                                ? books.map(book => `
                                    <tr class="border-t">
                                        <td class="p-4">${book.id}</td>
                                        <td class="p-4">${escapeHtml(book.judul)}</td>
                                        <td class="p-4">${escapeHtml(book.penulis)}</td>
                                        <td class="p-4">${book.tahun}</td>
                                        <td class="p-4">${book.stok}</td>
                                        <td class="p-4 whitespace-nowrap">
                                            <button
                                                onclick="openBookModal(${book.id})"
                                                class="text-blue-600 mr-3">
                                                Edit
                                            </button>

                                            <button
                                                onclick="deleteBook(${book.id})"
                                                class="text-red-600">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')
                                : `
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-500">
                                            Belum ada data buku.
                                        </td>
                                    </tr>
                                `
                            }
                        </tbody>
                    </table>
                </div>

                <div id="book-modal"></div>
            </div>
        `;
    } catch (error) {
        console.error(error);

        return `
            <p class="bg-red-100 text-red-700 p-4 rounded-lg">
                Gagal memuat data buku. Periksa koneksi backend API.
            </p>
        `;
    }
}

// Membuka modal tambah atau edit buku
async function openBookModal(id = null) {
    let book = {
        judul: '',
        penulis: '',
        tahun: '',
        stok: ''
    };

    if (id !== null) {
        const response = await api.get(`/books/${id}`);

        if (!response.success || !response.data) {
            showToast('Data buku tidak ditemukan', 'error');
            return;
        }

        book = response.data;
    }

    const modal = document.getElementById('book-modal');

    modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <h3 class="text-xl font-bold mb-4">
                    ${id === null ? 'Tambah Buku' : 'Edit Buku'}
                </h3>

                <form id="book-form" class="space-y-4">
                    <div>
                        <label class="block mb-1">Judul Buku</label>
                        <input
                            name="judul"
                            value="${escapeHtml(book.judul)}"
                            class="w-full border rounded-lg p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-1">Penulis</label>
                        <input
                            name="penulis"
                            value="${escapeHtml(book.penulis)}"
                            class="w-full border rounded-lg p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-1">Tahun</label>
                        <input
                            type="number"
                            name="tahun"
                            value="${book.tahun}"
                            class="w-full border rounded-lg p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-1">Stok</label>
                        <input
                            type="number"
                            name="stok"
                            value="${book.stok}"
                            class="w-full border rounded-lg p-2"
                            required>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="closeBookModal()"
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

    document.getElementById('book-form').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        const data = {
            judul: formData.get('judul'),
            penulis: formData.get('penulis'),
            tahun: Number(formData.get('tahun')),
            stok: Number(formData.get('stok'))
        };

        try {
            const result = id === null
                ? await api.post('/books', data)
                : await api.put(`/books/${id}`, data);

            if (result.success) {
                closeBookModal();
                showToast('Data buku berhasil disimpan');
                document.getElementById('app').innerHTML = await renderBooks();
            } else {
                showToast(result.message || 'Gagal menyimpan data', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan saat menyimpan data', 'error');
        }
    });
}

// Menutup modal
function closeBookModal() {
    document.getElementById('book-modal').innerHTML = '';
}

// Menghapus buku
async function deleteBook(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
        return;
    }

    try {
        const result = await api.delete(`/books/${id}`);

        if (result.success) {
            showToast('Buku berhasil dihapus');
            document.getElementById('app').innerHTML = await renderBooks();
        } else {
            showToast(result.message || 'Gagal menghapus buku', 'error');
        }
    } catch (error) {
        showToast('Terjadi kesalahan saat menghapus buku', 'error');
    }
}
