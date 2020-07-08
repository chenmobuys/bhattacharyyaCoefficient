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

    $image1_histogram = getHistogram($image1);

    $image2_histogram = getHistogram($image2);

    $bhattacharyyaCoefficient = getBhattacharyyaCoefficient($image1_histogram, $image2_histogram);

    echo sprintf('图片巴氏系数：%s', $bhattacharyyaCoefficient);

} catch (ImagickPixelException $exception) {

    echo $exception->getMessage();

} catch (ImagickException $exception) {

    echo $exception->getMessage();

}


