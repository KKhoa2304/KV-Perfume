<?php
/**
 * View: Giao diện Đăng ký
 */
?>

<div class="container" style="padding-top: 160px; padding-bottom: 100px; max-width: 480px; margin: 0 auto; font-family: var(--font-sans);">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-family: var(--font-serif); font-size: 26px; letter-spacing: 4px; text-transform: uppercase;">
            ĐĂNG KÝ THÀNH VIÊN
        </h1>

        <div style="width: 40px; height: 1px; background-color: var(--primary-black); margin: 20px auto 0 auto;"></div>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background: #fdf2f2; color: #b71c1c; padding: 15px; text-align: center; margin-bottom: 30px; border: 1px solid #ffcdd2; font-size: 13px; letter-spacing: 1px;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="?url=account/register" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                HỌ VÀ TÊN TRỌN VẸN *
            </label>
            <input type="text" name="fullname" class="form-input" placeholder="VD: Nguyễn Văn A" required>
        </div>

        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                EMAIL (DÙNG ĐỂ ĐĂNG NHẬP) *
            </label>
            <input type="email" name="email" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                SỐ ĐIỆN THOẠI
            </label>
            <input type="tel" name="phone" class="form-input" placeholder="VD: 090xxxxxxx">
        </div>

        <div class="form-group">
            <label class="form-label" style="font-size: 10px; color: var(--text-muted); letter-spacing: 1px;">
                MẬT KHẨU *
            </label>
            <input type="password" name="password" class="form-input" required>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%;">
            HOÀN TẤT ĐĂNG KÝ
        </button>
    </form>

    <div style="margin-top: 30px; text-align: center; padding-top: 20px;">
        <a href="?url=account" style="font-size: 12px; color: #666; letter-spacing: 1px; text-transform: uppercase; transition: color 0.3s; text-decoration: none;">
            ← ĐÃ CÓ TÀI KHOẢN? ĐĂNG NHẬP
        </a>
    </div>
</div>