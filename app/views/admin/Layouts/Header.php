<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $title ?? 'KV Perfume Admin';
$adminName = $_SESSION['user']['name'] ?? $_SESSION['user']['full_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?> - KV PERFUME</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font hỗ trợ tiếng Việt tốt hơn, giữ nguyên phong cách luxury -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap&subset=vietnamese" rel="stylesheet">

    <style>
        :root {
            --primary-black: #111111;
            --soft-black: #1f1f1f;
            --white: #ffffff;
            --ivory: #fbfaf8;
            --cream: #f6f2ec;
            --border: #e7e2dc;
            --muted: #777777;
            --gold: #b89b5e;
            --danger: #b42318;
            --success: #1f7a4d;
            --warning: #a66b00;
            --font-serif: "Playfair Display", Georgia, "Times New Roman", serif;
            --font-sans: "Inter", "Segoe UI", Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--white);
            color: var(--primary-black);
            font-family: var(--font-sans);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 300px 1fr;
            background: var(--white);
        }

        .admin-sidebar {
            min-height: 100vh;
            padding: 42px 30px;
            background: var(--white);
            border-right: 1px solid var(--border);
        }

        .admin-logo {
            margin-bottom: 55px;
        }

        .admin-logo strong {
            display: block;
            font-family: var(--font-serif);
            font-size: 34px;
            font-weight: 400;
            letter-spacing: 7px;
            line-height: 1.15;
        }

        .admin-logo span {
            display: block;
            margin-top: 12px;
            font-size: 12px;
            color: var(--muted);
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .admin-nav a {
            padding: 17px 0;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: #333;
            border-bottom: 1px solid transparent;
            transition: .22s ease;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
            padding-left: 10px;
        }

        .admin-main {
            min-width: 0;
            background: var(--ivory);
        }

        .admin-topbar {
            height: 104px;
            padding: 0 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .admin-topbar-title {
            font-family: var(--font-serif);
            font-size: 22px;
            font-weight: 400;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--muted);
            font-size: 14px;
            letter-spacing: .5px;
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--primary-black);
            color: var(--white);
            font-weight: 700;
        }

        .admin-content {
            padding: 54px 66px;
            min-height: calc(100vh - 104px);
        }

        .admin-breadcrumb {
            margin-bottom: 34px;
            padding-bottom: 16px;
            display: flex;
            gap: 10px;
            align-items: center;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .admin-breadcrumb strong,
        .admin-breadcrumb a:hover {
            color: var(--primary-black);
        }

        .admin-breadcrumb-separator {
            color: var(--gold);
        }

        .admin-page-header {
            margin-bottom: 38px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        .admin-eyebrow {
            margin: 0 0 14px;
            color: var(--gold);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .admin-page-header h1 {
            margin: 0 0 14px;
            font-family: var(--font-serif);
            font-size: 42px;
            font-weight: 400;
            letter-spacing: 5px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .admin-page-header p {
            margin: 0;
            max-width: 850px;
            color: var(--muted);
            line-height: 1.8;
            font-size: 15px;
        }

        .admin-card,
        .admin-stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            padding: 30px;
            margin-bottom: 28px;
            box-shadow: 0 18px 45px rgba(0,0,0,.035);
        }

        .admin-card h2,
        .admin-stat-card h3 {
            margin: 0 0 18px;
            font-family: var(--font-serif);
            font-weight: 400;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .admin-card h2 {
            font-size: 24px;
        }

        .admin-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        .admin-stat-card h3 {
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
        }

        .admin-stat-card strong {
            display: block;
            font-family: var(--font-serif);
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .admin-stat-card span {
            color: var(--muted);
        }

        .admin-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
        }

        .admin-btn {
            border: 1px solid var(--primary-black);
            background: var(--white);
            color: var(--primary-black);
            padding: 13px 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: .22s ease;
        }

        .admin-btn:hover,
        .admin-btn.primary {
            background: var(--primary-black);
            color: var(--white);
        }

        .admin-btn.primary:hover {
            background: #333;
            border-color: #333;
        }

        .admin-btn.small {
            padding: 9px 13px;
            font-size: 11px;
        }

        .admin-btn.danger {
            border-color: var(--danger);
            color: var(--danger);
        }

        .admin-btn.danger:hover {
            background: var(--danger);
            color: var(--white);
        }

        .admin-btn.success {
            border-color: var(--success);
            color: var(--success);
        }

        .admin-btn.success:hover {
            background: var(--success);
            color: var(--white);
        }

        .admin-btn.warning {
            border-color: var(--warning);
            color: var(--warning);
        }

        .admin-btn.warning:hover {
            background: var(--warning);
            color: var(--white);
        }

        .admin-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        .admin-filter {
            padding: 11px 18px;
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: .2s ease;
        }

        .admin-filter.active,
        .admin-filter:hover {
            background: var(--primary-black);
            border-color: var(--primary-black);
            color: var(--white);
        }

        .admin-table-wrap {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
        }

        .admin-table th {
            padding: 16px 14px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .admin-table td {
            padding: 18px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .admin-table td span {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 13px;
        }

        .text-right {
            text-align: right;
        }

        .inline-form {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 3px;
        }

        .admin-product-img {
            width: 66px;
            height: 66px;
            object-fit: cover;
            background: var(--white);
            border: 1px solid var(--border);
        }

        .admin-product-img.placeholder {
            display: grid;
            place-items: center;
            font-size: 11px;
            color: var(--muted);
        }

        .admin-badge {
            display: inline-flex !important;
            padding: 7px 12px;
            border: 1px solid currentColor;
            font-size: 11px !important;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            background: transparent;
        }

        .admin-badge.active,
        .admin-badge.success {
            color: var(--success);
        }

        .admin-badge.warning {
            color: var(--warning);
        }

        .admin-badge.danger {
            color: var(--danger);
        }

        .admin-badge.hidden {
            color: var(--muted);
        }

        .admin-alert {
            padding: 15px 18px;
            margin-bottom: 22px;
            border: 1px solid var(--border);
            background: var(--white);
        }

        .admin-alert.success {
            color: var(--success);
            background: #f3fbf7;
            border-color: rgba(31,122,77,.35);
        }

        .admin-alert.error {
            color: var(--danger);
            background: #fff5f4;
            border-color: rgba(180,35,24,.35);
        }

        .admin-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group select,
        .form-group textarea,
        .admin-small-select {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--primary-black);
            outline: none;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus,
        .admin-small-select:focus {
            border-color: var(--primary-black);
            box-shadow: 0 0 0 3px rgba(0,0,0,.04);
        }

        .admin-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .admin-order-total {
            margin-top: 22px;
            text-align: right;
        }

        .admin-order-total h3 {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .admin-timeline-item {
            padding: 16px;
            border: 1px solid var(--border);
            margin-bottom: 12px;
            background: var(--white);
        }

        .admin-muted,
        .admin-empty {
            color: var(--muted) !important;
            text-align: center;
        }

        @media (max-width: 980px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .admin-content {
                padding: 30px 20px;
            }

            .admin-detail-grid,
            .admin-form-grid,
            .admin-stats-grid {
                grid-template-columns: 1fr;
            }

            .admin-page-header {
                flex-direction: column;
            }

            .admin-page-header h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>
<div class="admin-shell">
    <?php require_once __DIR__ . '/Sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-title">
                <?php echo htmlspecialchars($pageTitle); ?>
            </div>

            <div class="admin-profile">
                <span>Xin chào, <?php echo htmlspecialchars($adminName); ?></span>
                <div class="admin-avatar">
                    <?php echo strtoupper(mb_substr($adminName, 0, 1, 'UTF-8')); ?>
                </div>
            </div>
        </header>

        <section class="admin-content">
            <?php require_once __DIR__ . '/Breadcrumb.php'; ?>