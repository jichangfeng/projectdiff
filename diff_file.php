<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>文件差异比较工具</title>
        <link rel="stylesheet" href="css/styles.css" type="text/css"/>
    </head>
    <body>
        <?php
        date_default_timezone_set('Asia/Shanghai');
        error_reporting(E_ERROR);
        ini_set('display_errors', '1');

        require_once __DIR__ . '/extensions/Unit.php';
        require_once __DIR__ . '/extensions/diff/Diff.php';

        $renderer = isset($_GET['renderer']) ? $_GET['renderer'] : 'Unified';
        $sourcePath = isset($_GET['sourcePath']) ? $_GET['sourcePath'] : '';
        $destinationPath = isset($_GET['destinationPath']) ? $_GET['destinationPath'] : '';
        $file = isset($_GET['file']) ? $_GET['file'] : '';

        if (!$sourcePath) {
            Unit::jsonExit(1, '源项目路径必须填写');
        } else if (!$destinationPath) {
            Unit::jsonExit(1, '目标项目路径必须填写');
        } else if ($sourcePath == $destinationPath) {
            Unit::jsonExit(1, '源项目路径、目标项目路径不能相同');
        } else if (!is_dir($sourcePath)) {
            Unit::jsonExit(1, '源项目路径不存在');
        } else if (!is_dir($destinationPath)) {
            Unit::jsonExit(1, '目标项目路径不存在');
        } else if (!$file) {
            Unit::jsonExit(1, '文件必须填写');
        }

        $sourceFile = $sourcePath . DIRECTORY_SEPARATOR . iconv('UTF-8', 'GBK//IGNORE', $file);
        $destinationFile = $destinationPath . DIRECTORY_SEPARATOR . iconv('UTF-8', 'GBK//IGNORE', $file);

        if (!is_file($sourceFile)) {
            Unit::jsonExit(1, '源项目文件不存在');
        } else if (!is_file($destinationFile)) {
            Unit::jsonExit(1, '目标项目文件不存在');
        }

        $navHtml = array();
        $navHtml[] = '<a style="color: ' . ($renderer == 'Unified' ? '#d43f3a' : '#337ab7') . ';" href="diff_file.php?renderer=Unified&sourcePath=' . $sourcePath . '&destinationPath=' . $destinationPath . '&file=' . $file . '">统一差异</a>';
        $navHtml[] = '<a style="color: ' . ($renderer == 'Context' ? '#d43f3a' : '#337ab7') . ';" href="diff_file.php?renderer=Context&sourcePath=' . $sourcePath . '&destinationPath=' . $destinationPath . '&file=' . $file . '">上下文差异</a>';
        $navHtml[] = '<a style="color: ' . ($renderer == 'InlineHTML' ? '#d43f3a' : '#337ab7') . ';" href="diff_file.php?renderer=InlineHTML&sourcePath=' . $sourcePath . '&destinationPath=' . $destinationPath . '&file=' . $file . '">内联HTML差异</a>';
        $navHtml[] = '<a style="color: ' . ($renderer == 'SidebySideHTML' ? '#d43f3a' : '#337ab7') . ';" href="diff_file.php?renderer=SidebySideHTML&sourcePath=' . $sourcePath . '&destinationPath=' . $destinationPath . '&file=' . $file . '">并排HTML差异</a>';
        $sourceString = explode("\n", file_get_contents($sourceFile));
        $destinationString = explode("\n", file_get_contents($destinationFile));
        $options = array(
                //'ignoreWhitespace' => true,
                //'ignoreCase' => true,
        );
        $diff = new Diff($sourceString, $destinationString, $options);
        echo '<h2>' . implode('　', $navHtml) . '</h2>';
        echo '<hr />';
        echo '<p>Old File: ' . strtr($sourceFile, array('/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR)) . '</p>';
        echo '<p>New File: ' . strtr($destinationFile, array('/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR)) . '</p>';
        echo '<hr />';
        if ($renderer == 'Unified') {
            require_once __DIR__ . '/extensions/diff/Diff/Renderer/Text/Unified.php';
            $renderer = new Diff_Renderer_Text_Unified;
            echo '<pre>' . htmlspecialchars($diff->render($renderer)) . '</pre>';
        } else if ($renderer == 'Context') {
            require_once __DIR__ . '/extensions/diff/Diff/Renderer/Text/Context.php';
            $renderer = new Diff_Renderer_Text_Context;
            echo '<pre>' . htmlspecialchars($diff->render($renderer)) . '</pre>';
        } else if ($renderer == 'InlineHTML') {
            require_once __DIR__ . '/extensions/diff/Diff/Renderer/Html/Inline.php';
            $renderer = new Diff_Renderer_Html_Inline;
            echo '<pre>' . $diff->Render($renderer) . '</pre>';
        } else if ($renderer == 'SidebySideHTML') {
            require_once __DIR__ . '/extensions/diff/Diff/Renderer/Html/SideBySide.php';
            $renderer = new Diff_Renderer_Html_SideBySide;
            echo '<pre>' . $diff->Render($renderer) . '</pre>';
        }
        ?>
    </body>
</html>