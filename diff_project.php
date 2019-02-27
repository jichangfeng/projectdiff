<?php

date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ERROR);
ini_set('display_errors', '1');
set_time_limit(0);

require_once __DIR__ . '/extensions/Unit.php';
require_once __DIR__ . '/extensions/CFileHelper.php';

$sourcePath = isset($_POST['sourcePath']) ? $_POST['sourcePath'] : (isset($_GET['sourcePath']) ? $_GET['sourcePath'] : '');
$destinationPath = isset($_POST['destinationPath']) ? $_POST['destinationPath'] : (isset($_GET['destinationPath']) ? $_GET['destinationPath'] : '');
$export = isset($_POST['export']) ? $_POST['export'] : (isset($_GET['export']) ? $_GET['export'] : '');

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
}

$added = $modified = $deleted = array();

$sourceFileMd5s = array();
$sourceFiles = CFileHelper::findFiles($sourcePath, array('exclude' => array('.svn'), 'absolutePaths' => false));
$sourceCount = count($sourceFiles);
foreach ($sourceFiles as $file) {
    $file = strtr($file, array('\\' => '/'));
    $key = iconv('GBK', 'UTF-8//IGNORE', $file);
    $md5 = md5_file($sourcePath . DIRECTORY_SEPARATOR . $file);
    $sourceFileMd5s[$key] = $md5;
    $deleted[$key] = $md5;
}
unset($sourceFiles);

$destinationFiles = CFileHelper::findFiles($destinationPath, array('exclude' => array('.svn'), 'absolutePaths' => false));
$destinationCount = count($destinationFiles);
foreach ($destinationFiles as $file) {
    $file = strtr($file, array('\\' => '/'));
    $key = iconv('GBK', 'UTF-8//IGNORE', $file);
    $md5 = md5_file($destinationPath . DIRECTORY_SEPARATOR . $file);
    if (isset($sourceFileMd5s[$key])) {
        unset($deleted[$key]);
        if ($sourceFileMd5s[$key] == $md5) {
            continue;
        } else {
            $modified[$key] = $md5;
        }
    } else {
        $added[$key] = $md5;
    }
}
unset($destinationFiles);

if ($export) {
    $zipPath = 'files_' . date('YmdHis') . '_' . rand(10000, 99999) . '.zip';
    $zip = new ZipArchive;
    if (!$zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)) {
        throw new CException(basename($zipPath) . ' can not create');
    }
    $files = array_merge($added, $modified);
    foreach ($files as $file => $md5) {
        $file = iconv('UTF-8', 'GBK//IGNORE', $file);
        $zip->addFile($destinationPath . DIRECTORY_SEPARATOR . $file, 'files/' . $file);
    }
    $zip->addFromString('files/diff.json', json_encode(array(
        'added' => $added,
        'addedCount' => count($added),
        'modified' => $modified,
        'modifiedCount' => count($modified),
        'deleted' => $deleted,
        'deletedCount' => count($deleted),
        'sourceCount' => $sourceCount,
        'destinationCount' => $destinationCount,
    )));
    $zip->close();
    $fp = fopen($zipPath, 'rb');
    if (!$fp) {
        exit('无法打开文件');
    }
    $filemime = mime_content_type($zipPath);
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Type: ' . $filemime . ';charset=utf-8');
    header("Content-Length: " . filesize($zipPath));
    header('Content-Disposition: attachment;filename="files.zip"');
    while (!feof($fp)) {
        $contents = fread($fp, 8192);
        echo $contents;
        ob_flush();
        flush();
    }
    fclose($fp);
    unlink($zipPath);
    exit;
}

Unit::jsonExit(0, '', array(
    'added' => $added,
    'addedCount' => count($added),
    'modified' => $modified,
    'modifiedCount' => count($modified),
    'deleted' => $deleted,
    'deletedCount' => count($deleted),
    'sourceCount' => $sourceCount,
    'destinationCount' => $destinationCount,
));
