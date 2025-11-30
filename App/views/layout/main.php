<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title><?= $title ?? 'Roomify' ?></title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<?= $content ?? '' ?>
</body>
</html>
<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="p-3 mb-4 text-white rounded 
        <?= $_SESSION['flash_message']['type'] === 'success' ? 'bg-green-600' : 'bg-red-600' ?>">
        <?= $_SESSION['flash_message']['text'] ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>
