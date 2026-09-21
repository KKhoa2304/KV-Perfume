<?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
    <div class="admin-breadcrumb">
        <?php foreach ($breadcrumbs as $index => $item): ?>
            <?php if ($index > 0): ?>
                <span class="admin-breadcrumb-separator">›</span>
            <?php endif; ?>

            <?php if (!empty($item['link'])): ?>
                <a href="<?php echo htmlspecialchars($item['link']); ?>">
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php else: ?>
                <strong><?php echo htmlspecialchars($item['label']); ?></strong>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>