<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="e-Rapor Sisipan – Login">
    <title>
        <?= e($title ?? 'Login – ' . APP_NAME) ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="auth-body">

    <div class="auth-bg">
        <div class="auth-bg-pattern"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="<?= asset('img/logo.png') ?>" alt="Logo" style="width:100%; height:auto;">
                </div>
                <h1 class="auth-title">e-Rapor Sisipan</h1>
                <p class="auth-subtitle">SMKN 10 Surabaya</p>
            </div>

            <!-- Flash Messages -->
            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-error" style="margin: 0 0 1rem 0">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= e(Session::getFlash('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (Session::hasFlash('errors')): ?>
                <div class="alert alert-error" style="margin: 0 0 1rem 0">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin:0;padding-left:1rem">
                        <?php foreach (Session::getFlash('errors') as $err): ?>
                            <li>
                                <?= e($err) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success" style="margin: 0 0 1rem 0">
                    <i class="fas fa-check-circle"></i>
                    <?= e(Session::getFlash('success')) ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="<?= BASE_URL ?>/?page=login" class="auth-form" id="loginForm" novalidate>
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Alamat Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="email@smkn10sby.sch.id" value="<?= e(Session::getFlash('old_email', '')) ?>"
                        required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-icon-right">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="input-icon-btn" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <div class="auth-footer">
                <p class="text-muted">e Rapor Sisipan - SMKN 10 Surabaya &copy;
                    <?= date('Y') ?>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
        });

        // Prevent double submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        });
    </script>
</body>

</html>