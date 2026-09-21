<?php
/**
 * View: Giao diện Luồng thanh toán nhiều bước
 */
?>

<div class="checkout-container" style="padding-top:140px;">
    <h1 class="checkout-title">THANH TOÁN</h1>

    <?php if (!empty($_SESSION['checkout_error'])): ?>
        <div style="color:red; font-weight:bold; text-align:center; margin:20px 0;">
            <?= htmlspecialchars($_SESSION['checkout_error']) ?>
        </div>
        <?php unset($_SESSION['checkout_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart_error'])): ?>
        <div style="color:red; font-weight:bold; text-align:center; margin:20px 0;">
            <?= htmlspecialchars($_SESSION['cart_error']) ?>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="checkout-steps">
        <?php
        $currentPaymentMethod = $formData['paymentMethod'] ?? 'cod';

        $steps = [
            ['num' => '1', 'label' => 'Thông tin giao hàng'],
            ['num' => '2', 'label' => 'Phương thức thanh toán']
        ];

        if ($currentPaymentMethod === 'bank' || $currentPaymentMethod === 'bank_transfer' || $step == '2.5') {
            $steps[] = ['num' => '2.5', 'label' => 'Xác nhận QR'];
        }

        $steps[] = ['num' => '3', 'label' => 'Xác nhận đơn hàng'];

        foreach ($steps as $s):
            $statusClass = '';

            if ((string)$step === (string)$s['num']) {
                $statusClass = 'active';
            } elseif ((float)$step > (float)$s['num']) {
                $statusClass = 'completed';
            }
        ?>
            <div class="step <?= $statusClass; ?>">
                <div class="step-number"><?= htmlspecialchars($s['num']); ?></div>
                <div class="step-label"><?= htmlspecialchars($s['label']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($step == '1'): ?>

        <form action="?url=cart/checkout&step=1" method="POST" class="checkout-form-wrapper">
            <div class="checkout-form">
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">HỌ TÊN</label>
                    <input name="fullname" type="text" class="form-input"
                           value="<?= htmlspecialchars($formData['fullname'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">SỐ ĐIỆN THOẠI</label>
                    <input name="phone" type="tel" class="form-input"
                           value="<?= htmlspecialchars($formData['phone'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">ĐỊA CHỈ</label>
                    <input name="address" type="text" class="form-input"
                           value="<?= htmlspecialchars($formData['address'] ?? ''); ?>"
                           placeholder="Số nhà, đường, quận, thành phố..."
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">MÃ GIẢM GIÁ</label>
                    <input name="voucher_code" type="text" class="form-input"
                           value="<?= htmlspecialchars($formData['voucher_code'] ?? ''); ?>"
                           placeholder="Nhập coupon nếu có">
                </div>

                <?php if (!empty($discount) && $discount > 0): ?>
                    <div style="color:green; font-weight:bold; margin-top:15px;">
                        Đã áp dụng mã giảm giá: -<?= number_format($discount, 0, ',', '.'); ?> đ
                    </div>
                <?php endif; ?>
            </div>

            <button class="btn btn-primary" type="submit" style="margin-top:20px; width:100%;">
                TIẾP TỤC
            </button>
        </form>

    <?php elseif ($step == '2'): ?>

        <form action="?url=cart/checkout&step=2" method="POST" class="checkout-form-wrapper">
            <div class="checkout-form">
                <label class="form-label">PHƯƠNG THỨC THANH TOÁN</label>

                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio"
                               name="paymentMethod"
                               value="cod"
                               <?= (($formData['paymentMethod'] ?? 'cod') === 'cod') ? 'checked' : ''; ?>>
                        <div class="payment-option-content">
                            <span class="payment-option-label">Thanh toán khi nhận hàng (COD)</span>
                            <span class="payment-option-description">Thanh toán bằng tiền mặt khi nhận hàng.</span>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio"
                               name="paymentMethod"
                               value="bank"
                               <?= (($formData['paymentMethod'] ?? '') === 'bank' || ($formData['paymentMethod'] ?? '') === 'bank_transfer') ? 'checked' : ''; ?>>
                        <div class="payment-option-content">
                            <span class="payment-option-label">Chuyển khoản ngân hàng</span>
                            <span class="payment-option-description">Quét mã QR và chuyển khoản trước khi xác nhận đơn.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="checkout-actions">
                <a href="?url=cart/checkout&step=1" class="btn btn-outline" style="text-align:center; text-decoration:none;">
                    QUAY LẠI
                </a>

                <button class="btn btn-primary" type="submit">
                    TIẾP TỤC
                </button>
            </div>
        </form>

    <?php elseif ($step == '2.5'): ?>

        <div class="checkout-form-wrapper">
            <div class="qr-payment-container">
                <h2 class="qr-title">Chuyển khoản ngân hàng</h2>

                <div class="qr-section">
                    <div class="qr-code-box" id="qrBox">
                        <img src="https://api.vietqr.io/image/vietcombank-1234567890-compact2.jpg?amount=<?= (int)$finalTotal; ?>&addInfo=KV%20PERFUME"
                             style="max-width:100%; display:block;"
                             alt="Mã QR thanh toán">
                    </div>

                    <div class="qr-timer">
                        <span class="timer-label">Hết hạn trong:</span>
                        <span class="timer-value" id="countdownTimer">01:00</span>
                    </div>
                </div>

                <div class="bank-info-section">
                    <h3 class="bank-info-title">Thông tin chuyển khoản</h3>

                    <div class="bank-info-item">
                        <strong>Ngân hàng:</strong>
                        <span>Vietcombank</span>
                    </div>

                    <div class="bank-info-item">
                        <strong>Số tài khoản:</strong>
                        <span class="account-number">1234567890</span>
                    </div>

                    <div class="bank-info-item">
                        <strong>Chủ tài khoản:</strong>
                        <span>KV PERFUME</span>
                    </div>

                    <div class="bank-info-item">
                        <strong>Số tiền:</strong>
                        <span class="amount"><?= number_format($finalTotal, 0, ',', '.'); ?> đ</span>
                    </div>

                    <div class="bank-info-note">
                        ⚠️ Vui lòng chuyển đúng số tiền. Sau khi chuyển khoản thành công, bấm “TIẾP TỤC”.
                    </div>
                </div>
            </div>

            <form action="?url=cart/checkout&step=2.5" method="POST" class="checkout-actions">
                <a href="?url=cart/checkout&step=2" class="btn btn-outline" style="text-align:center; text-decoration:none;">
                    QUAY LẠI
                </a>

                <button class="btn btn-primary" type="submit">
                    ĐÃ CHUYỂN KHOẢN - TIẾP TỤC
                </button>
            </form>
        </div>

        <script>
            let time = 60;
            const timerEl = document.getElementById('countdownTimer');

            const interval = setInterval(() => {
                time--;

                let min = Math.floor(time / 60);
                let sec = time % 60;

                timerEl.innerText = `${min}:${sec < 10 ? '0' : ''}${sec}`;

                if (time <= 0) {
                    clearInterval(interval);
                    document.getElementById('qrBox').style.opacity = '0.3';
                    timerEl.innerHTML = '<span style="color:#8b0000;">Mã QR đã hết hạn</span>';
                }
            }, 1000);
        </script>

    <?php elseif ($step == '3'): ?>

        <div class="checkout-form-wrapper">
            <div class="order-summary">
                <h2 class="order-summary-title">TÓM TẮT ĐƠN HÀNG</h2>

                <div class="order-items">
                    <?php foreach ($cart as $item): ?>
                        <div class="order-item">
                            <div class="order-item-info">
                                <span class="order-item-name">
                                    <?= htmlspecialchars($item['name']); ?>
                                </span>

                                <span class="order-item-quantity">
                                    x<?= (int)$item['quantity']; ?>
                                </span>
                            </div>

                            <span class="order-item-price">
                                <?= number_format((float)$item['price'] * (int)$item['quantity'], 0, ',', '.'); ?> đ
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($discount) && $discount > 0): ?>
                    <div class="order-total">
                        <span>Giảm giá:</span>
                        <span class="total-amount" style="color:green;">
                            -<?= number_format($discount, 0, ',', '.'); ?> đ
                        </span>
                    </div>
                <?php endif; ?>

                <div class="order-total">
                    <span>Tổng cộng:</span>
                    <span class="total-amount">
                        <?= number_format($finalTotal, 0, ',', '.'); ?> đ
                    </span>
                </div>

                <div class="order-delivery-info">
                    <h3 class="delivery-info-title">Thông tin giao hàng</h3>

                    <div class="delivery-info-item">
                        <strong>Tên:</strong>
                        <span><?= htmlspecialchars($formData['fullname'] ?? ''); ?></span>
                    </div>

                    <div class="delivery-info-item">
                        <strong>Số điện thoại:</strong>
                        <span><?= htmlspecialchars($formData['phone'] ?? ''); ?></span>
                    </div>

                    <div class="delivery-info-item">
                        <strong>Địa chỉ:</strong>
                        <span><?= htmlspecialchars($formData['address'] ?? ''); ?></span>
                    </div>

                    <?php if (!empty($formData['voucher_code'])): ?>
                        <div class="delivery-info-item">
                            <strong>Mã giảm giá:</strong>
                            <span><?= htmlspecialchars($formData['voucher_code']); ?></span>
                        </div>
                    <?php endif; ?>

                    <h3 class="delivery-info-title">Phương thức thanh toán</h3>

                    <div class="delivery-info-item">
                        <strong>Hình thức:</strong>
                        <span>
                            <?=
                            (($formData['paymentMethod'] ?? 'cod') === 'bank' || ($formData['paymentMethod'] ?? '') === 'bank_transfer')
                                ? 'Chuyển khoản ngân hàng'
                                : 'Thanh toán khi nhận hàng (COD)';
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <form action="?url=cart/submitOrder" method="POST" class="checkout-actions">
                <input type="hidden" name="fullname" value="<?= htmlspecialchars($formData['fullname'] ?? ''); ?>">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($formData['phone'] ?? ''); ?>">
                <input type="hidden" name="address" value="<?= htmlspecialchars($formData['address'] ?? ''); ?>">
                <input type="hidden" name="paymentMethod" value="<?= htmlspecialchars($formData['paymentMethod'] ?? 'cod'); ?>">
                <input type="hidden" name="voucher_code" value="<?= htmlspecialchars($formData['voucher_code'] ?? ''); ?>">

                <a href="<?= (($formData['paymentMethod'] ?? 'cod') === 'bank' || ($formData['paymentMethod'] ?? '') === 'bank_transfer') ? '?url=cart/checkout&step=2.5' : '?url=cart/checkout&step=2'; ?>"
                   class="btn btn-outline"
                   style="text-align:center; text-decoration:none;">
                    QUAY LẠI
                </a>

                <button class="btn btn-primary" type="submit">
                    XÁC NHẬN ĐẶT HÀNG
                </button>
            </form>
        </div>

    <?php endif; ?>
</div>