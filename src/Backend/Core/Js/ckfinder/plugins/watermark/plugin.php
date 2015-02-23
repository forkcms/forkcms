<?php
/*
* CKFinder
* ========
* http://cksource.com/ckfinder
* Copyright (C) 2007-2014, CKSource - Frederico Knabben. All rights reserved.
*
* The software, this file and its contents are subject to the CKFinder
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the Source Code of CKFinder.
*
* CKFinder extension: adds watermark to uploaded images.
*/

class Watermark
{
    function onAfterFileUpload($currentFolder, $uploadedFile, $sFilePath)
    {
        global $config;
        $watermarkSettings = $config['Plugin_Watermark'];

        $this->createWatermark($sFilePath, $watermarkSettings['source'], $watermarkSettings['marginRight'],
            $watermarkSettings['marginBottom'], $watermarkSettings['quality'], $watermarkSettings['transparency']);

        return true;
    }

    function createWatermark($sourceFile, $watermarkFile, $marginLeft = 5, $marginBottom = 5, $quality = 90, $transparency = 100)
    {
        if (!file_exists($watermarkFile)) {
            $watermarkFile = dirname(__FILE__) . "/" . $watermarkFile;
        }
        if (!file_exists($watermarkFile)) {
            return false;
        }

        $watermarkImageAttr = @getimagesize($watermarkFile);
        $sourceImageAttr = @getimagesize($sourceFile);
        if ($sourceImageAttr === false || $watermarkImageAttr === false) {
            return false;
        }

        switch ($watermarkImageAttr['mime'])
        {
            case 'image/gif':
                {
                    if (@imagetypes() & IMG_GIF) {
                        $oWatermarkImage = @imagecreatefromgif($watermarkFile);
                    } else {
                        $ermsg = 'GIF images are not supported';
                    }
                }
                break;
            case 'image/jpeg':
                {
                    if (@imagetypes() & IMG_JPG) {
                        $oWatermarkImage = @imagecreatefromjpeg($watermarkFile) ;
                    } else {
                        $ermsg = 'JPEG images are not supported';
                    }
                }
                break;
            case 'image/png':
                {
                    if (@imagetypes() & IMG_PNG) {
                        $oWatermarkImage = @imagecreatefrompng($watermarkFile) ;
                    } else {
                        $ermsg = 'PNG images are not supported';
                    }
                }
                break;
            case 'image/wbmp':
                {
                    if (@imagetypes() & IMG_WBMP) {
                        $oWatermarkImage = @imagecreatefromwbmp($watermarkFile);
                    } else {
                        $ermsg = 'WBMP images are not supported';
                    }
                }
                break;
            default:
                $ermsg = $watermarkImageAttr['mime'].' images are not supported';
                break;
        }

        switch ($sourceImageAttr['mime'])
        {
            case 'image/gif':
                {
                    if (@imagetypes() & IMG_GIF) {
                        $oImage = @imagecreatefromgif($sourceFile);
                    } else {
                        $ermsg = 'GIF images are not supported';
                    }
                }
                break;
            case 'image/jpeg':
                {
                    if (@imagetypes() & IMG_JPG) {
                        $oImage = @imagecreatefromjpeg($sourceFile) ;
                    } else {
                        $ermsg = 'JPEG images are not supported';
                    }
                }
                break;
            case 'image/png':
                {
                    if (@imagetypes() & IMG_PNG) {
                        $oImage = @imagecreatefrompng($sourceFile) ;
                    } else {
                        $ermsg = 'PNG images are not supported';
                    }
                }
                break;
            case 'image/wbmp':
                {
                    if (@imagetypes() & IMG_WBMP) {
                        $oImage = @imagecreatefromwbmp($sourceFile);
                    } else {
                        $ermsg = 'WBMP images are not supported';
                    }
                }
                break;
            default:
                $ermsg = $sourceImageAttr['mime'].' images are not supported';
                break;
        }

        if (isset($ermsg) || false === $oImage || false === $oWatermarkImage) {
            return false;
        }

        $watermark_width = $watermarkImageAttr[0];
        $watermark_height = $watermarkImageAttr[1];
        $dest_x = $sourceImageAttr[0] - $watermark_width - $marginLeft;
        $dest_y = $sourceImageAttr[1] - $watermark_height - $marginBottom;

        if ( $sourceImageAttr['mime'] == 'image/png')
        {
            if(function_exists('imagesavealpha') && function_exists('imagecolorallocatealpha') )
            {
                 $bg = imagecolorallocatealpha($oImage, 255, 255, 255, 127); // (PHP 4 >= 4.3.2, PHP 5)
                 imagefill($oImage, 0, 0 , $bg);
                 imagealphablending($oImage, false);
                 imagesavealpha($oImage, true);  // (PHP 4 >= 4.3.2, PHP 5)
            }
        }
        if ($watermarkImageAttr['mime'] == 'image/png') {
            imagecopy($oImage, $oWatermarkImage, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
        }
        else {
            imagecopymerge($oImage, $oWatermarkImage, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $transparency);
        }

        switch ($sourceImageAttr['mime'])
        {
            case 'image/gif':
                imagegif($oImage, $sourceFile);
                break;
            case 'image/jpeg':
                imagejpeg($oImage, $sourceFile, $quality);
                break;
            case 'image/png':
                imagepng($oImage, $sourceFile);
                break;
            case 'image/wbmp':
                imagewbmp($oImage, $sourceFile);
                break;
        }

        imageDestroy($oImage);
        imageDestroy($oWatermarkImage);
    }
}

$watermark = new Watermark();
$config['Hooks']['AfterFileUpload'][] = array($watermark, 'onAfterFileUpload');
if (empty($config['Plugin_Watermark']))
{
    $config['Plugin_Watermark'] = array(
        "source" => "logo.gif",
        "marginRight" => 5,
        "marginBottom" => 5,
        "quality" => 90,
        "transparency" => 80,
    );
}
