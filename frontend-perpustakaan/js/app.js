
// Mengatur perpindahan halaman berdasarkan URL hash
async function renderPage() {
    const app = document.getElementById('app');
    const page = window.location.hash || '#dashboard';

    app.innerHTML = `
        <p class="text-gray-500">Memuat halaman...</p>
    `;

    if (page === '#dashboard') {
        app.innerHTML = await renderDashboard();
    } else if (page === '#books') {
        app.innerHTML = await renderBooks();
    } else if (page === '#categories') {
    app.innerHTML = await renderCategories();
    
    } else {
        app.innerHTML = `
            <p class="text-red-600">
                Halaman tidak ditemukan.
            </p>
        `;
    }
}

// Menjalankan router ketika menu diklik
window.addEventListener('hashchange', renderPage);

// Menampilkan halaman pertama saat aplikasi dibuka
document.addEventListener('DOMContentLoaded', renderPage);
