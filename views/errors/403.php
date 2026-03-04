<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>403 – Akses Ditolak |
        <?= APP_NAME ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #F4F6F8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0
        }

        .box {
            background: #fff;
            padding: 3rem 4rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08)
        }

        .icon {
            font-size: 4rem;
            color: #E53935;
            margin-bottom: 1rem
        }

        h1 {
            color: #1a1a2e;
            font-size: 1.8rem;
            margin: 0 0 .5rem
        }

        p {
            color: #666;
            margin: 0 0 1.5rem
        }

        a {
            background: #2E7D32;
            color: #fff;
            padding: .7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600
        }

        a:hover {
            background: #1B5E20
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="icon">🔒</div>
        <h1>403 – Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="<?= BASE_URL ?>/?page=dashboard">← Kembali ke Dashboard</a>
    </div>
</body>

</html>