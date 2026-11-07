$baseUrl = "http://localhost:8000/api/books"

Write-Host "=== Test 1: GET semua buku ==="
$books = Invoke-RestMethod -Uri $baseUrl -Method Get
if ($books.success) {
    Write-Host "BERHASIL: Daftar buku ditemukan"
}

Write-Host "`n=== Test 2: POST buku baru ==="
$newBook = @{
    judul = "Buku Uji Otomatis"
    penulis = "Penulis Uji"
    tahun = 2026
    stok = 5
} | ConvertTo-Json

$created = Invoke-RestMethod -Uri $baseUrl -Method Post `
    -Body $newBook -ContentType "application/json"

$id = $created.data.id
Write-Host "BERHASIL: Buku dibuat dengan ID $id"

Write-Host "`n=== Test 3: GET buku berdasarkan ID ==="
$detail = Invoke-RestMethod -Uri "$baseUrl/$id" -Method Get
if ($detail.success) {
    Write-Host "BERHASIL: Detail buku ditemukan"
}

Write-Host "`n=== Test 4: PUT ubah buku ==="
$updateBook = @{
    judul = "Buku Uji Otomatis Revisi"
    penulis = "Penulis Uji"
    tahun = 2026
    stok = 8
} | ConvertTo-Json

$updated = Invoke-RestMethod -Uri "$baseUrl/$id" -Method Put `
    -Body $updateBook -ContentType "application/json"

if ($updated.success) {
    Write-Host "BERHASIL: Buku berhasil diperbarui"
}

Write-Host "`n=== Test 5: DELETE buku ==="
$deleted = Invoke-RestMethod -Uri "$baseUrl/$id" -Method Delete
if ($deleted.success) {
    Write-Host "BERHASIL: Buku berhasil dihapus"
}

Write-Host "`n=== Test 6: Validasi error ==="
try {
    $invalidBook = @{
        judul = ""
    } | ConvertTo-Json

    Invoke-RestMethod -Uri $baseUrl -Method Post `
        -Body $invalidBook -ContentType "application/json"

    Write-Host "GAGAL: Validasi tidak berjalan"
}
catch {
    if ($_.Exception.Response.StatusCode.value__ -eq 422) {
        Write-Host "BERHASIL: Validasi menolak data kosong (HTTP 422)"
    }
    else {
        Write-Host "GAGAL: Terjadi kesalahan lain"
        Write-Host $_.Exception.Message
    }
}

Write-Host "`n=== PENGUJIAN SELESAI ==="