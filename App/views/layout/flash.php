<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="mx-8 mt-2 mb-4 px-4 py-3 rounded text-sm
      <?= $_SESSION['flash_message']['type'] === 'success'
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800' ?>">
        <?= htmlspecialchars($_SESSION['flash_message']['text'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>