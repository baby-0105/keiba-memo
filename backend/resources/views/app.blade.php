<?php
$devMode = env('APP_ENV') === 'local'; // 環境変数で開発モードを判定
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel + React</title>

    <?php if ($devMode): ?>
        <!-- React 開発サーバーを利用 -->
        <script src="http://localhost:3000/static/js/bundle.js" defer></script>
    <?php else: ?>
        <?php
        $manifestPath = public_path('asset-manifest.json');
        $jsFile = '/static/js/main.js'; // デフォルト値

        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (isset($manifest['files']['main.js'])) {
                $jsFile = $manifest['files']['main.js'];
            }
        }
        ?>
        <script src="{{ secure_asset($jsFile) }}" defer></script>
    <?php endif; ?>
</head>
<body>
    <div id="root"></div> <!-- React を描画するエリア -->
</body>
</html>
