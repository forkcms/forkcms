<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder;

use CKSource\CKFinder\Exception\CKFinderException;

/**
 * The Image class.
 *
 * The class used for image processing.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class Image
{
    protected static $supportedExtensions = array('jpg', 'jpeg', 'gif', 'png');

    /**
     * Image width.
     *
     * @var int $witdh
     */
    protected $width;

    /**
     * Image height.
     *
     * @var int $height
     */
    protected $height;

    /**
     * Image MIME type.
     *
     * @var string $mime
     */
    protected $mime;

    /**
     * Number of bits for each color.
     *
     * @var string $mime
     */
    protected $bits;

    /**
     * Number of color channels (i.e. 3 for RGB pictures and 4 for CMYK pictures).
     *
     * @var int $channels
     */
    protected $channels;

    /**
     * GD image.
     *
     * @var resource $gdImage
     */
    protected $gdImage;

    /**
     * The size of the image produced by the `getData()` method.
     *
     * @var int $dataSize
     */
    protected $dataSize;

    /**
     * The factory method.
     *
     * @param string $data
     * @param bool   $bmpSupport
     *
     * @return Image
     */
    public static function create($data, $bmpSupport = false)
    {
        return new Image($data, $bmpSupport);
    }

    /**
     * Parses the image size from a string in the form of `[width]x[height]`,
     * for example 278x219.
     *
     * @param string $size WxH string
     *
     * @return array An array with width and height values array([width], [height]),
     *               for the example above: array(278, 219).
     */
    public static function parseSize($size)
    {
        $sizeParts = explode('x', trim($size));

        return count($sizeParts) === 2 ? array_map('intval', $sizeParts) : array(0, 0);
    }

    /**
     * Checks if a given exception is supported by the Image class.
     *
     * @param string $extension
     * @param bool   $bmpSupport
     *
     * @return bool
     */
    public static function isSupportedExtension($extension, $bmpSupport = false)
    {
        $supportedExtensions = static::$supportedExtensions;

        if ($bmpSupport) {
            $supportedExtensions[] = 'bmp';
        }

        return in_array(strtolower($extension), $supportedExtensions);
    }

    /**
     * Returns the MIME type for a given extension.
     *
     * @param string $extension
     *
     * @return string MIME type
     */
    public static function mimeTypeFromExtension($extension)
    {
        $extensionMimeTypeMap = array(
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'bmp'  => 'image/bmp',
            'png'  => 'image/png',
            'wbmp' => 'image/wbmp'
        );

        $extension = strtolower($extension);

        return array_key_exists($extension, $extensionMimeTypeMap) ? $extensionMimeTypeMap[$extension] : null;
    }

    /**
     * Constructor.
     *
     * @param string $imageData  image data
     * @param bool   $bmpSupport `true` if bitmaps are supported (be aware of poor efficiency!).
     *
     * @throws CKFinderException in case the image could not be initialized properly.
     */

    public function __construct($imageData, $bmpSupport = false)
    {
        if (!extension_loaded('gd')) {
            throw new CKFinderException('PHP GD library not found');
        }

        $imgInfo = @getimagesizefromstring($imageData);

        if ($imgInfo === false) {
            throw new CKFinderException('Unsupported image type');
        }

        $this->width    = isset($imgInfo[0])          ? $imgInfo[0]          : 0;
        $this->height   = isset($imgInfo[1])          ? $imgInfo[1]          : 0;
        $this->mime     = isset($imgInfo['mime'])     ? $imgInfo['mime']     : '';
        $this->bits     = isset($imgInfo['bits'])     ? $imgInfo['bits']     : 8;
        $this->channels = isset($imgInfo['channels']) ? $imgInfo['channels'] : 3;
        $this->dataSize = strlen($imageData);

        if (!$this->width || !$this->height || !$this->mime) {
            throw new CKFinderException('Unsupported image type');
        }

        $this->setMemory($this->width, $this->height, $this->bits, $this->channels);

        $gdSupportedTypes = @imagetypes();

        $supportedFormats = array(
            'image/gif'      => $gdSupportedTypes & IMG_GIF,
            'image/jpeg'     => $gdSupportedTypes & IMG_JPG,
            'image/png'      => $gdSupportedTypes & IMG_PNG,
            'image/wbmp'     => $gdSupportedTypes & IMG_WBMP,
            'image/bmp'      => $bmpSupport && ($gdSupportedTypes & IMG_JPG),
            'image/x-ms-bmp' => $bmpSupport && ($gdSupportedTypes & IMG_JPG)
        );

        if (!array_key_exists($this->mime, $supportedFormats) || !$supportedFormats[$this->mime]) {
            throw new CKFinderException('Unsupported image type: ' . $this->mime);
        }

        if ($this->mime === 'image/bmp' || $this->mime === 'image/x-ms-bmp') {
            $this->gdImage = $this->createFromBmp($imageData);
        } else {
            $this->gdImage = imagecreatefromstring($imageData);
        }

        if (!is_resource($this->gdImage)) {
            throw new CKFinderException('Unsupported image type (not resource): ' . $this->mime);
        }

        unset($imageData);
    }

    /**
     * Returns the aspect ratio size as associative array:
     * @code
     * array
     * (
     *      [width]  => 80
     *      [heigth] => 120
     * )
     * @endcode
     *
     * @param int  $maxWidth        requested width
     * @param int  $maxHeight       requested height
     * @param int  $actualWidth     original width
     * @param int  $actualHeight    original height
     * @param bool $useHigherFactor defines which factor should be used to calculate the new
     *                              size. For example:
     *                              - original image size 800x400
     *                              - calculateAspectRatio(300, 200, 800, 400, false) will return 300x150
     *                              - calculateAspectRatio(300, 200, 800, 400, true) will return 400x200
     *
     * @return array
     */
    public static function calculateAspectRatio($maxWidth, $maxHeight, $actualWidth, $actualHeight, $useHigherFactor = false)
    {
        $oSize = array('width' => $maxWidth, 'height' => $maxHeight);

        // Calculates the X and Y resize factors
        $iFactorX = (float) $maxWidth / (float) $actualWidth;
        $iFactorY = (float) $maxHeight / (float) $actualHeight;

        // If some dimension have to be resized
        if ($iFactorX != 1 || $iFactorY != 1) {
            if ($useHigherFactor) {
                // Uses the higher Factor to change the opposite size
                if ($iFactorX > $iFactorY) {
                    $oSize['height'] = (int) round($actualHeight * $iFactorX);
                } elseif ($iFactorX < $iFactorY) {
                    $oSize['width'] = (int) round($actualWidth * $iFactorY);
                }
            } else {
                // Uses the lower Factor to change the opposite size
                if ($iFactorX < $iFactorY) {
                    $oSize['height'] = (int) round($actualHeight * $iFactorX);
                } elseif ($iFactorX > $iFactorY) {
                    $oSize['width'] = (int) round($actualWidth * $iFactorY);
                }
            }
        }

        if ($oSize['height'] <= 0) {
            $oSize['height'] = 1;
        }

        if ($oSize['width'] <= 0) {
            $oSize['width'] = 1;
        }

        // Returns the Size
        return $oSize;
    }


    /**
     * @link http://pl.php.net/manual/pl/function.imagecreatefromjpeg.php
     * function posted by e dot a dot schultz at gmail dot com
     *
     * @param $imageWidth
     * @param $imageHeight
     * @param $imageBits
     * @param $imageChannels
     *
     * @return bool
     */
    public function setMemory($imageWidth, $imageHeight, $imageBits, $imageChannels)
    {
        $MB = 1048576; // number of bytes in 1M
        $K64 = 65536; // number of bytes in 64K
        $TWEAKFACTOR = 2.4; // Or whatever works for you
        $memoryNeeded = round(($imageWidth * $imageHeight
                    * $imageBits
                    * $imageChannels / 8
                    + $K64
                ) * $TWEAKFACTOR
            ) + 3 * $MB;

        //ini_get('memory_limit') only works if compiled with "--enable-memory-limit" also
        //Default memory limit is 8MB so well stick with that.
        //To find out what yours is, view your php.ini file.
        $memoryLimit = Utils::returnBytes(@ini_get('memory_limit')) / $MB;
        // There are no memory limits, nothing to do
        if ($memoryLimit == -1) {
            return true;
        }
        if (!$memoryLimit) {
            $memoryLimit = 8;
        }

        $memoryLimitMB = $memoryLimit * $MB;
        if (function_exists('memory_get_usage')) {
            if (memory_get_usage() + $memoryNeeded > $memoryLimitMB) {
                $newLimit = $memoryLimit + ceil((memory_get_usage()
                            + $memoryNeeded
                            - $memoryLimitMB
                        ) / $MB
                    );
                if (@ini_set('memory_limit', $newLimit . 'M') === false) {
                    return false;
                }
            }
        } else {
            if ($memoryNeeded + 3 * $MB > $memoryLimitMB) {
                $newLimit = $memoryLimit + ceil((3 * $MB
                            + $memoryNeeded
                            - $memoryLimitMB
                        ) / $MB
                    );
                if (false === @ini_set('memory_limit', $newLimit . 'M')) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @link http://pl.php.net/manual/en/function.imagecopyresampled.php
     * Replacement to `imagecopyresampled` that will deliver results that are almost identical except
     * MUCH faster (very typically 30 times faster).
     *
     * @static
     * @access public
     * @param resource $dstImage
     * @param resource $srcImage
     * @param int $dstX
     * @param int $dstY
     * @param int $srcX
     * @param int $srcY
     * @param int $dstW
     * @param int $dstH
     * @param int $srcW
     * @param int $srcH
     * @param int $quality
     *
     * @return boolean
     */
    public function fastCopyResampled(&$dstImage, $srcImage, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH, $quality = 3)
    {
        if (empty($srcImage) || empty($dstImage)) {
            return false;
        }

        if ($quality <= 1) {
            $temp = imagecreatetruecolor($dstW + 1, $dstH + 1);
            imagecopyresized($temp, $srcImage, $dstX, $dstY, $srcX, $srcY, $dstW + 1, $dstH + 1, $srcW, $srcH);
            imagecopyresized($dstImage, $temp, 0, 0, 0, 0, $dstW, $dstH, $dstW, $dstH);
            imagedestroy($temp);
        } elseif ($quality < 5 && (($dstW * $quality) < $srcW || ($dstH * $quality) < $srcH)) {
            $tmpW = $dstW * $quality;
            $tmpH = $dstH * $quality;
            $temp = imagecreatetruecolor($tmpW + 1, $tmpH + 1);
            imagecopyresized($temp, $srcImage, 0, 0, $srcX, $srcY, $tmpW + 1, $tmpH + 1, $srcW, $srcH);
            imagecopyresampled($dstImage, $temp, $dstX, $dstY, 0, 0, $dstW, $dstH, $tmpW, $tmpH);
            imagedestroy($temp);
        } else {
            imagecopyresampled($dstImage, $srcImage, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
        }

        return true;
    }

    /**
     * Source: http://pl.php.net/imagecreate
     * (optimized for speed and memory usage, but yet not very efficient).
     *
     * @param string $data bitmap data
     *
     * @return resource
     */
    public function createFromBmp($data)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $data);
        rewind($stream);

        //20 seconds seems to be a reasonable value to not kill a server and process images up to 1680x1050
        @set_time_limit(20);

        if (!is_resource($stream)) {
            return false;
        }

        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($stream, 14));
        if ($FILE['file_type'] != 19778) {
            return false;
        }

        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($stream, 40));

        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

        if ($BMP['size_bitmap'] == 0) {
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        }

        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);

        if ($BMP['decal'] == 4) {
            $BMP['decal'] = 0;
        }

        $PALETTE = array();
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($stream, $BMP['colors'] * 4));
        }

        //2048x1536px@24bit don't even try to process larger files as it will probably fail
        if ($BMP['size_bitmap'] > 3 * 2048 * 1536) {
            return false;
        }

        $IMG = fread($stream, $BMP['size_bitmap']);
        fclose($stream);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;

        $line_length = $BMP['bytes_per_pixel'] * $BMP['width'];

        if ($BMP['bits_per_pixel'] == 24) {
            while ($Y >= 0) {
                $X = 0;
                $temp = unpack("C*", substr($IMG, $P, $line_length));

                while ($X < $BMP['width']) {
                    $offset = $X * 3;
                    imagesetpixel($res, $X++, $Y, ($temp[$offset + 3] << 16) + ($temp[$offset + 2] << 8) + $temp[$offset + 1]);
                }
                $Y--;
                $P += $line_length + $BMP['decal'];
            }
        } elseif ($BMP['bits_per_pixel'] == 8) {
            while ($Y >= 0) {
                $X = 0;

                $temp = unpack("C*", substr($IMG, $P, $line_length));

                while ($X < $BMP['width']) {
                    imagesetpixel($res, $X++, $Y, $PALETTE[$temp[$X] + 1]);
                }
                $Y--;
                $P += $line_length + $BMP['decal'];
            }
        } elseif ($BMP['bits_per_pixel'] == 4) {
            while ($Y >= 0) {
                $X = 0;
                $i = 1;
                $low = true;

                $temp = unpack("C*", substr($IMG, $P, $line_length));

                while ($X < $BMP['width']) {
                    if ($low) {
                        $index = $temp[$i] >> 4;
                    } else {
                        $index = $temp[$i++] & 0x0F;
                    }
                    $low = !$low;

                    imagesetpixel($res, $X++, $Y, $PALETTE[$index + 1]);
                }
                $Y--;
                $P += $line_length + $BMP['decal'];
            }
        } elseif ($BMP['bits_per_pixel'] == 1) {
            $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
            if (($P * 8) % 8 == 0) {
                $COLOR[1] = $COLOR[1] >> 7;
            } elseif (($P * 8) % 8 == 1) {
                $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
            } elseif (($P * 8) % 8 == 2) {
                $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
            } elseif (($P * 8) % 8 == 3) {
                $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
            } elseif (($P * 8) % 8 == 4) {
                $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
            } elseif (($P * 8) % 8 == 5) {
                $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
            } elseif (($P * 8) % 8 == 6) {
                $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
            } elseif (($P * 8) % 8 == 7) {
                $COLOR[1] = ($COLOR[1] & 0x1);
            }
            $COLOR[1] = $PALETTE[$COLOR[1] + 1];
        } else {
            return false;
        }

        return $res;
    }

    /**
     * Resizes an image to a given size keeping the aspect ratio.
     *
     * @param int  $maxWidth        maximum width
     * @param int  $maxHeight       maximum height
     * @param int  $quality         quality
     * @param bool $useHigherFactor
     *
     * @return Image $this
     */
    public function resize($maxWidth, $maxHeight, $quality = 80, $useHigherFactor = false)
    {
        $maxWidth = (int) $maxWidth ?: $this->width;
        $maxHeight = (int) $maxHeight ?: $this->height;

        if ($this->width <= $maxWidth && $this->height <= $maxHeight) {
            return $this;
        }

        $targetSize = static::calculateAspectRatio($maxWidth, $maxHeight, $this->width, $this->height, $useHigherFactor);

        $targetWidth = $targetSize['width'];
        $targetHeight = $targetSize['height'];

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($this->mime === 'image/png') {
            $bg = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefill($targetImage, 0, 0, $bg);
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
        }

        $this->fastCopyResampled($targetImage, $this->gdImage, 0, 0, 0, 0, $targetWidth, $targetHeight,
                                 $this->width, $this->height, (int)max(floor($quality / 20), 6));

        imagedestroy($this->gdImage);
        $this->gdImage = $targetImage;
        $this->width = $targetWidth;
        $this->height = $targetHeight;

        return $this;
    }

    /**
     * Returns image data.
     *
     * @param string $format returned image format mimetype (current image mimetype is used if not set).
     *
     * @return string image data
     */
    public function getData($format = null)
    {
        $mime = $format ?: $this->mime;

        ob_start();

        switch ($mime) {
            case 'image/gif':
                imagegif($this->gdImage);
                break;
            case 'image/jpeg':
            case 'image/bmp':
            case 'image/x-ms-bmp':
                imagejpeg($this->gdImage);
                break;
            case 'image/png':
                imagealphablending($this->gdImage, false);
                imagesavealpha($this->gdImage, true);
                imagepng($this->gdImage);
                break;
            case 'image/wbmp':
                imagewbmp($this->gdImage);
                break;
        }

        $this->dataSize = ob_get_length();

        return ob_get_clean();
    }

    /**
     * Returns GD image resource.
     *
     * @return resource GD image resource
     */
    public function getGDImage()
    {
        return $this->gdImage;
    }

    /**
     * Returns the size of image data produced by the `getData()` method.
     *
     * @return int image data size in bytes
     */
    public function getDataSize()
    {
        return $this->dataSize;
    }

    /**
     * Returns image width in pixels.
     *
     * @return int image width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Returns image height in pixels.
     *
     * @return int image height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Returns image MIME type.
     *
     * @return string MIME type
     */
    public function getMimeType()
    {
        return $this->mime;
    }

    public function crop($x, $y, $width, $height)
    {
        $targetImage = imagecreatetruecolor($width, $height);

        if ($this->mime === 'image/png') {
            $bg = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefill($targetImage, 0, 0, $bg);
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
        }

        imagecopy($targetImage, $this->gdImage, 0, 0, $x, $y, $width, $height);

        imagedestroy($this->gdImage);
        $this->gdImage = $targetImage;
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function rotate($degrees, $bgcolor = 0)
    {
        if ($this->mime === 'image/png') {
            imagesavealpha($this->gdImage, true);
            $bgcolor = imagecolorallocatealpha($this->gdImage, 0, 0, 0, 127);
        }

        $this->gdImage = imagerotate($this->gdImage, $degrees, $bgcolor);
        $this->width = imagesx($this->gdImage);
        $this->height = imagesy($this->gdImage);

        return $this;
    }

    public function getInfo()
    {
        $info = array(
            'width'  => $this->getWidth(),
            'height' => $this->getHeight(),
            'size'   => $this->getDataSize()
        );

        return $info;
    }

    public function __destruct()
    {
        if (is_resource($this->gdImage)) {
            imagedestroy($this->gdImage);
        }
    }
}
