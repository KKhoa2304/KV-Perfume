<?php
/**
 * View: Giao diện Đăng nhập
 */
?>

<div class="container" style="padding-top: 160px; padding-bottom: 100px; max-width: 480px; margin: 0 auto; font-family: var(--font-sans);">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-family: var(--font-serif); font-size: 26px; letter-spacing: 4px; text-transform: uppercase;">
            ĐĂNG NHẬP
        </h1>
        <div style="width: 40px; height: 1px; background-color: var(--primary-black); margin: 20px auto 0 auto;"></div>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background: #fdf2f2; color: #b71c1c; padding: 15px; text-align: center; margin-bottom: 30px; border: 1px solid #ffcdd2; font-size: 13px; letter-spacing: 1px;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div style="background: #f1f8e9; color: #33691e; padding: 15px; text-align: center; margin-bottom: 30px; border: 1px solid #dcedc8; font-size: 13px; letter-spacing: 1px;">
            ✅ <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form action="?url=account/login" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                EMAIL *
            </label>
            <input type="email" name="email" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                MẬT KHẨU *
            </label>
            <input type="password" name="password" class="form-input" required>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%;">
            ĐĂNG NHẬP
        </button>
    </form>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-light); text-align: center;">
        <h3 style="font-family: var(--font-serif); font-size: 16px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; color: var(--primary-black);">
            Bạn chưa có tài khoản?
        </h3>

        <p style="font-size: 13px; color: #666; margin-bottom: 25px; line-height: 1.6;">
            Trở thành thành viên của KV PERFUME để nhận các đặc quyền ưu đãi, tích lũy hạng thẻ và theo dõi hành trình đơn hàng dễ dàng hơn.
        </p>

        <a href="?url=account/registerForm" class="btn btn-outline" style="width: 100%; display: block; box-sizing: border-box; text-decoration: none;">
            TẠO TÀI KHOẢN MỚI
        </a>
    </div>
</div>