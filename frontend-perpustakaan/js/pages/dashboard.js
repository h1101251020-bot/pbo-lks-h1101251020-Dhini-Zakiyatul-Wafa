async function renderDashboard() {
    try {
        // Mengambil data buku dan kategori dari API
        const [bookResponse, categoryResponse] = await Promise.all([
            api.get('/books'),
            api.get('/categories')
        ]);

        const books = bookResponse.data || [];
        const categories = categoryResponse.data || [];

        // Mengambil maksimal 5 buku terbaru
        const latestBooks = [...books].slice(-5).reverse();

        return `
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Dashboard
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-600 text-white p-6 rounded-xl shadow">
                        <p class="text-sm">Total Buku</p>
                        <h3 class="text-3xl font-bold">
                            ${books.length}
                        </h3>
                    </div>

                    <div class="bg-orange-500 text-white p-6 rounded-xl shadow">
                        <p class="text-sm">Kategori</p>
                        <h3 class="text-3xl font-bold">
                            ${categories.length}
                        </h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-bold mb-4">
                        5 Buku Terbaru
                    </h3>

                    <div class="space-y-3">
                        ${
                            latestBooks.length
                            ? latestBooks.map(book => `
                                <div class="border-b pb-3">
                                    <p class="font-semibold">
                                        ${escapeHtml(book.judul)}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        ${escapeHtml(book.penulis)}
                                    </p>
                                </div>
                            `).join('')
                            : '<p class="text-gray-500">Belum ada data buku.</p>'
                        }
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Gagal memuat dashboard:', error);

        return `
            <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                Gagal memuat data dashboard. Periksa koneksi backend API.
            </div>
        `;
    }
}