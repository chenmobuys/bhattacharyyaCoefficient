<?php

/**
 * 获取图片直方图
 * @param $file
 * @return array
 * @throws ImagickException
 * @throws ImagickPixelException
 */
function getHistogram($file)
{
    $grayBit = 4;
    $histogram = [];

    $image = new Imagick($file);
    /** @var ImagickPixel[] $imageHistogram */
    $imageHistogram = $image->getImageHistogram();
    $total = count($imageHistogram);

    foreach ($imageHistogram as $pixel) {
        $rgb = $pixel->getColor();

        $redIdx = floor(ceil($grayBit / 255) * $rgb['r']);
        $blueIdx = floor(ceil($grayBit / 255) * $rgb['b']);
        $greenIdx = floor(ceil($grayBit / 255) * $rgb['g']);

        $singleIndex = $redIdx + $greenIdx * $grayBit + $blueIdx * $grayBit * $grayBit;

        if (!isset($histogram[$singleIndex])) {
            $histogram[$singleIndex] = 0;
        }
        $histogram[$singleIndex] += 1;
    }

    foreach ($histogram as $key => $item) {
        $histogram[$key] = $histogram[$key] / $total;
    }

    return $histogram;
}

/**
 * 使用GD库获取图片直方图
 * @param $file
 * @return array
 */
function getHistogramWithGd($file)
{
    $ext = pathinfo($file, PATHINFO_EXTENSION);

    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($file);
            break;
        case 'png':
            $image = imagecreatefrompng($file);
            break;
        case 'bmp':
            $image = imagecreatefrombmp($file);
            break;
        case 'wbmp':
            $image = imagecreatefromwbmp($file);
            break;
        case 'webp':
            $image = imagecreatefromwebp($file);
            break;
        case 'xbm':
            $image = imagecreatefromxbm($file);
            break;
        case 'xpm':
            $image = imagecreatefromxpm($file);
            break;
        default:
            $image = imagecreatefromstring($file);
            break;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $total = 0;
    $grayBit = 4;
    $histogram = [];

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {

            $rgb = imageColorAt($image, $x, $y);

            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $redIdx = floor(ceil($grayBit / 255) * $r);
            $blueIdx = floor(ceil($grayBit / 255) * $b);
            $greenIdx = floor(ceil($grayBit / 255) * $g);

            $singleIndex = $redIdx + $greenIdx * $grayBit + $blueIdx * $grayBit * $grayBit;

            if (!isset($histogram[$singleIndex])) {
                $histogram[$singleIndex] = 0;
            }
            $histogram[$singleIndex] += 1;

            $total++;
        }
    }

    foreach ($histogram as $key => $item) {
        $histogram[$key] = $histogram[$key] / $total;
    }

    return $histogram;
}

/**
 * 获取巴氏系数
 * @param $source
 * @param $dist
 * @return float|int
 */
function getBhattacharyyaCoefficient($source, $dist)
{
    $mixed = [];

    foreach ($source as $key => $item) {
        $mixed[] = sqrt(($source[$key] ?? 0) * ($dist[$key] ?? 0));
    }

    return array_sum($mixed);
}

try {
    $image1 = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . '1_1.jpg';

    $image2 = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . '1_2.jpg';

    $image1_histogram = getHistogramWithGd($image1);

    $image2_histogram = getHistogramWithGd($image2);

    $bhattacharyyaCoefficient = getBhattacharyyaCoefficient($image1_histogram, $image2_histogram);

    echo sprintf('图片巴氏系数：%s', $bhattacharyyaCoefficient);

} catch (ImagickPixelException $exception) {

    echo $exception->getMessage();

} catch (ImagickException $exception) {

    echo $exception->getMessage();

}


