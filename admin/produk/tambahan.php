<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Bahan</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 400px; /* Membatasi lebar form */
            margin: auto;    /* Agar form berada di tengah */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Tambah Bahan</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form dibatasi dengan class "form-container" -->
    <div class="form-container mt-4">
        <form method="post" action="">
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori:</label>
                <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Tambah Kategori</button>
            <a href="index.php" class="btn btn-secondary w-100">Kembali</a>
        </form>
    </div>
</div>

<!-- Script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
