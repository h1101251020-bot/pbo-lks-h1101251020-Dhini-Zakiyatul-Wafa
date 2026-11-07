// Mengamankan teks sebelum ditampilkan dalam HTML
function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

// Menampilkan notifikasi
function showToast(message, type = 'success') {
    const toast = document.createElement('div');

    toast.className = `
        fixed top-5 right-5 z-50
        px-5 py-3 rounded-lg shadow-lg text-white
        ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
    `;

    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}