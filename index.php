<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/extensions/generator_OpenGraph/index.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$pic = $_SERVER['DOCUMENT_ROOT'] . '/picture.jpg';
$title = "Как подключить Яндекс.Метрику и Google.Analytics в Google Tag Manager?";

?>

<html>
<head>

    <title>Title</title>

    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="<?=Generator_OpenGraph::getInstance()->getPathImage($pic, $title)?>" />
</head>
<body>
    <p>Body</p>
</body>
</html>
