<?php
/**
 * $Id: MCImageToolsGD.php 654 2009-01-23 13:00:39Z spocke $
 *
 * @package MCImageManager.utils
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles XML language packs.
 *
 * @package MCImageManager.utils
 */
class ImageToolsGD {
	/* Internal variables */
	var $_source = "";
	var $_target = "";
	var $_targetExt = "";
	var $_width = 0;
	var $_height = 0;
	var $_ext = "";
	var $_quality = "";
	var $_bgc = "0";
	var $_angle = "0";
	var $_hori = false;
	var $_vert = false;
	var $_top = 0;
	var $_left = 0;

	/**
	 * Constructor
	 */
	function ImageToolsGD() {
		// Constructor
	}

	/**
	 * Generates a resized image as the target file, from the source file
	 * @param String $source Absolute source path.
	 * @param String $target Absolute source target.
	 * @param Int $height Height of image.
	 * @param Int $width Width of the image.
	 * @param String $ext Extension of the image.
	 * @param Int $quality Quality in percent, 0-100.
	 * @return Bool true or false depending on success or not.
	 */
	function resizeImage($source, $target, $width, $height, $ext, $quality = "") {
		// No ext specified
		if (!$ext) {
			$ar = explode('.', $source);
			$ext = strtolower(array_pop($ar));
		}

		if (!$this->canEdit($ext))
			return false;

		$this->_quality = $quality;
		$this->_source = $source;
		$this->_target = $target;
		$this->_width = $width;
		$this->_height = $height;
		$this->_ext = $ext;
		$ar = explode('.', $target);
		$this->_targetExt = strtolower(array_pop($ar));

		switch ($ext) {
			case "gif":
				return $this->_resizeGif();

			case "jpg":
			case "jpe":
			case "jpeg":
				return $this->_resizeJpg();

			case "png":
				return $this->_resizePng();

			case "bmp":
				return false;
		}

		return false;
	}

	/**
	 * Generates a rotated image as the target file, from the source file
	 * @param String $source Absolute source path.
	 * @param String $target Absolute source target.
	 * @param String $ext Extension of the image.
	 * @param Int $angle Angle to set the angle.
	 * @param String $bgc Background color.
	 * @param Int $quality Quality in percent, 0-100.
	 * @return Bool true or false depending on success or not.
	 */
	function rotateImage($source, $target, $ext, $angle = "90", $bgc = "0", $quality = "") {
		if (!$this->canEdit($ext))
			return false;

		$this->_angle = $angle;
		$this->_source = $source;
		$this->_target = $target;
		$this->_quality = $quality;
		$this->_ext = $ext;
		$this->_bgc = $bgc;

		switch ($ext) {
			case "gif":
				return $this->_rotateGif();

			case "jpg":
			case "jpe":
			case "jpeg":
				return $this->_rotateJpg();

			case "png":
				return $this->_rotatePng();

			case "bmp":
				return false;
		}
		
		return false;
	}

	/**
	 * Generates a cropped image as the target file, from the source file
	 * @param String $source Absolute source path.
	 * @param String $target Absolute source target.
	 * @param Int $top Top of crop.
	 * @param Int $left Left of crop.
	 * @param Int $height Height of image.
	 * @param Int $width Width of the image.
	 * @param String $ext Extension of the image.
	 * @param Int $quality Quality in percent, 0-100.
	 * @return Bool true or false depending on success or not.
	 */
	function cropImage($source, $target, $top, $left, $width, $height, $ext, $quality="") {
		if (!$this->canEdit($ext))
			return false;

		$this->_left = $left;
		$this->_top = $top;
		$this->_quality = $quality;
		$this->_source = $source;
		$this->_target = $target;
		$this->_width = $width;
		$this->_height = $height;
		$this->_ext = $ext;

		switch ($ext) {
			case "gif":
				return $this->_cropGif();

			case "jpg":
			case "jpe":
			case "jpeg":
				return $this->_cropJpg();

			case "png":
				return $this->_cropPng();

			case "bmp":
				return false;
		}
	}

	/**
	 * Generates a flipped image as the target file, from the source file
	 * @param String $source Absolute source path.
	 * @param String $target Absolute source target.
	 * @param String $ext Extension of the image.
	 * @param Bool $vert Vertical or not (Horizontal).
	 * @param Int $quality Quality in percent, 0-100.
	 * @return Bool true or false depending on success or not.
	 */
	function flipImage($source, $target, $ext, $vert = false, $hori = false, $quality="") {
		if (!$this->canEdit($ext))
			return false;

		$this->_source = $source;
		$this->_target = $target;
		$this->_ext = $ext;
		$this->_vert = $vert;
		$this->_hori = $hori;
		$this->_quality = $quality;

		switch ($ext) {
			case "gif":
				return $this->_flipGif();

			case "jpg":
			case "jpe":
			case "jpeg":
				return $this->_flipJpg();

			case "png":
				return $this->_flipPng();

			case "bmp":
				return false;
		}
	}

	/**
	 * Internal function for cropping gif images.
	 * @return Bool true or false depending on success or not.
	 */
	function _cropGif() {
		$source = ImagecreateFromGif($this->_source);
		$image = ImageCreate($this->_width, $this->_height);

		$transparent = imagecolorallocate($image, 255, 255, 255);

		imagefilledrectangle($image, 0, 0, $this->_width, $this->_width, $transparent);

		imagecolortransparent($image, $transparent);

		ImageCopyResampled($image, $source, 0, 0, $this->_left, $this->_top, $this->_width, $this->_height, $this->_width, $this->_height);
		
		ImageDestroy($source);
		$result = ImageGif($image, $this->_target);
		ImageDestroy($image);
		
		return $result; 
	}

	/**
	 * Internal function for cropping png images.
	 * @return Bool true or false depending on success or not.
	 */
	function _cropPng() {
		$source = ImagecreateFromPng($this->_source);

		if ($this->_isPNG8($this->_source))
			$image = ImageCreate($this->_width, $this->_height);
		else
			$image = ImageCreateTrueColor($this->_width, $this->_height);

		imagealphablending($image, false);
		imagesavealpha($image, true);
		ImageCopyResampled($image, $source, 0, 0, $this->_left, $this->_top, $this->_width, $this->_height, $this->_width, $this->_height);

		ImageDestroy($source);
		$result = ImagePng($image, $this->_target);
		ImageDestroy($image);

		return $result;
	}

	/**
	 * Internal function for cropping Jpg images.
	 * @return Bool true or false depending on success or not.
	 */
	function _cropJpg() {
		$source = ImagecreateFromJpeg($this->_source);
		$image = ImageCreateTrueColor($this->_width, $this->_height);

		ImageCopyResampled($image, $source, 0, 0, $this->_left, $this->_top, $this->_width, $this->_height, $this->_width, $this->_height);

		ImageDestroy($source);

		// this should set it to same file
		if ($this->_quality != "")
			$result = ImageJpeg($image, $this->_target, $this->_quality);
		else
			$result = ImageJpeg($image, $this->_target);

		ImageDestroy($image);
		
		
		return $result;
	}

	/**
	 * Internal function for resizing gif images.
	 * @return Bool true or false depending on success or not.
	 */
	function _resizeGif() {
		$source = ImagecreateFromGif($this->_source);
		$image = ImageCreate($this->_width, $this->_height);

		imagealphablending($image, false);
		imagesavealpha($image, true);

		$transparent = imagecolorallocate($image, 255, 255, 255);

		imagefilledrectangle($image, 0, 0, $this->_width, $this->_height, $transparent);

		imagecolortransparent($image, $transparent);

		ImageCopyResampled($image, $source, 0, 0, 0, 0, $this->_width, $this->_height, ImageSX($source), ImageSY($source));
		ImageDestroy($source);

		switch ($this->_targetExt) {
			case 'gif':
				$result = ImageGif($image, $this->_target);
				break;

			case 'jpeg':
			case 'jpg':
				if ($this->_quality != "")
					$result = ImageJpeg($image, $this->_target, $this->_quality);
				else
					$result = ImageJpeg($image, $this->_target);

				break;

			case 'png':
				$result = ImagePng($image, $this->_target);
				break;
		}

		ImageDestroy($image);
		
		return $result;
	}

	/**
	 * Internal function for resizing png images.
	 * @return Bool true or false depending on success or not.
	 */
	function _resizePng() {
		$source = imagecreatefrompng($this->_source);

		if ($this->_isPNG8($this->_source))
			$image = ImageCreate($this->_width, $this->_height);
		else {
			$image = ImageCreateTrueColor($this->_width, $this->_height);
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}

		ImageCopyResampled($image, $source, 0, 0, 0, 0, $this->_width, $this->_height, ImageSX($source), ImageSY($source));
		ImageDestroy($source);

		switch ($this->_targetExt) {
			case 'gif':
				$result = ImageGif($image, $this->_target);
				break;

			case 'jpeg':
			case 'jpg':
				if ($this->_quality != "")
					$result = ImageJpeg($image, $this->_target, $this->_quality);
				else
					$result = ImageJpeg($image, $this->_target);

				break;

			case 'png':
				$result = ImagePng($image, $this->_target);
				break;
		}

		ImageDestroy($image);

		return $result;
	}

	/**
	 * Internal function for resizing jpg images.
	 * @return Bool true or false depending on success or not.
	 */
	function _resizeJpg() {
		$source = ImageCreateFromJpeg($this->_source);
		$image = ImageCreateTrueColor($this->_width, $this->_height);
		ImageCopyResampled($image, $source, 0, 0, 0, 0, $this->_width, $this->_height, ImageSX($source), ImageSY($source));
		ImageDestroy($source);

		$result = false;

		switch ($this->_targetExt) {
			case 'gif':
				$result = ImageGif($image, $this->_target);
				break;

			case 'jpeg':
			case 'jpg':
				if ($this->_quality != "")
					$result = ImageJpeg($image, $this->_target, $this->_quality);
				else
					$result = ImageJpeg($image, $this->_target);

				break;

			case 'png':
				$result = ImagePng($image, $this->_target);
				break;
		}

		ImageDestroy($image);

		return $result;
	}

	function _rotateJpg() {
		$image = ImagecreateFromJpeg($this->_source);

		$width = imagesx($image);
		$height = imagesy($image);

		switch ($this->_angle) {
			case 90:
				$fill = ImageCreateTrueColor($height, $width);
				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $height-$y-1, $x, $x, $y, 1, 1);
			break;

			case 180:
				$this->_hori = true;
				$this->_vert = true;
				return $this->_flipJpg();

			case 270:
				$fill = ImageCreateTrueColor($height, $width);
				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $y, $width-$x-1, $x, $y, 1, 1);
			break;
		}

		ImageDestroy($image);
		$result = ImageJpeg($fill, $this->_target);
		ImageDestroy($fill);

		return $result;

	}

	function _rotatePng() {
		$image = ImagecreateFromPng($this->_source);

		$width = imagesx($image);
		$height = imagesy($image);

		switch ($this->_angle) {
			case 90:
				if ($this->_isPNG8($this->_source))
					$fill = ImageCreate($height, $width);
				else {
					$fill = ImageCreateTrueColor($height, $width);
					imagealphablending($fill, false);
					imagesavealpha($fill, true);
				}

				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $height-$y-1, $x, $x, $y, 1, 1);
			break;

			case 180:
				$this->_hori = true;
				$this->_vert = true;
				return $this->_flipPng();

			case 270:
				if ($this->_isPNG8($this->_source))
					$fill = ImageCreate($height, $width);
				else {
					$fill = ImageCreateTrueColor($height, $width);
					imagealphablending($fill, false);
					imagesavealpha($fill, true);
				}

				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $y, $width-$x-1, $x, $y, 1, 1);
			break;
		}
		
		ImageDestroy($image);
		$result = ImagePng($fill, $this->_target);
		ImageDestroy($fill);

		return $result;
	}

	function _rotateGif() {
		$image = ImagecreateFromGif($this->_source);

		$width = imagesx($image);
		$height = imagesy($image);

		switch ($this->_angle) {
			case 90:
				$fill = ImageCreateTrueColor($height, $width);
				imagealphablending($fill, false);
				imagesavealpha($fill, true);

				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $height-$y-1, $x, $x, $y, 1, 1);
			break;

			case 180:
				$this->_hori = true;
				$this->_vert = true;
				return $this->_flipGif();

			case 270:
				$fill = ImageCreateTrueColor($height, $width);
				imagealphablending($fill, false);
				imagesavealpha($fill, true);

				for ($x=0; $x<$width; $x++)
					for ($y=0; $y<$height; $y++)
						imagecopy($fill, $image, $y, $width-$x-1, $x, $y, 1, 1);
			break;
		}
		
		ImageDestroy($image);
		$result = ImageGif($fill, $this->_target);
		ImageDestroy($fill);

		return $result;
	}

	function _flipPng() {
		$source = ImagecreateFromPng($this->_source);
		$width = imagesx($source);
		$height = imagesy($source);

		if ($this->_isPNG8($this->_source))
			$image = ImageCreate($height, $width);
		else {
			$image = ImageCreateTrueColor($height, $width);
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}

		if ($this->_hori) {
			for ($i=0; $i<$width; $i++)
				ImageCopyResampled($image, $source, $width - $i - 1, 0, $i, 0, 1, $height, 1, $height);

			if ($this->_vert)
				ImageCopyResampled($source, $image, 0, 0, 0, 0, $width, $height, $width, $height);
		}

		if ($this->_vert) {
			for ($i=0; $i<$height; $i++)
				ImageCopyResampled($image, $source, 0, $height - $i - 1, 0, $i, $width, 1, $width, 1);
		}

		ImageDestroy($source);
		$result = ImagePng($image, $this->_target);
		ImageDestroy($image);

		return $result;
	}

	function _flipJpg() {
		$source = ImagecreateFromJpeg($this->_source);
		$width = imagesx($source);
		$height = imagesy($source);

		$image = ImageCreateTrueColor($width, $height);

		if ($this->_hori) {
			for ($i=0; $i<$width; $i++)
				ImageCopyResampled($image, $source, $width - $i - 1, 0, $i, 0, 1, $height, 1, $height);

			if ($this->_vert)
				ImageCopyResampled($source, $image, 0, 0, 0, 0, $width, $height, $width, $height);
		}

		if ($this->_vert) {
			for ($i=0; $i<$height; $i++)
				ImageCopyResampled($image, $source, 0, $height - $i - 1, 0, $i, $width, 1, $width, 1);
		}

		ImageDestroy($source);

		$result = ImageJpeg($image, $this->_target);

		ImageDestroy($image);

		return $result;
	}

	function _flipGif() {
		$source = ImagecreateFromGif($this->_source);
		$width = imagesx($source);
		$height = imagesy($source);

		$image = ImageCreateTrueColor($width, $height);

		if ($this->_hori) {
			for ($i=0; $i<$width; $i++)
				ImageCopyResampled($image, $source, $width - $i - 1, 0, $i, 0, 1, $height, 1, $height);

			if ($this->_vert)
				ImageCopyResampled($source, $image, 0, 0, 0, 0, $width, $height, $width, $height);
		}

		if ($this->_vert) {
			for ($i=0; $i<$height; $i++)
				ImageCopyResampled($image, $source, 0, $height - $i - 1, 0, $i, $width, 1, $width, 1);
		}

		ImageDestroy($source);
		$result = ImageGif($image, $this->_target);
		ImageDestroy($image);

		return $result;
	}

	function _getPNGHeader($path) {
		$fp = fopen($path, "rb");

		if ($fp) {
			$magic = fread($fp, 8);
			if ($magic == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A" ) { // Is PNG
				// Read chunks
				while (!feof($fp)) {
					$buff = fread($fp, 4);

					if (strlen($buff) != 4)
						break;

					$chunk = unpack('Nlen', $buff);
					$chunk['type'] = fread($fp, 4);

					if (strlen($chunk['type']) != 4)
						break;

					// Found header then read it
					if ($chunk['type'] == 'IHDR') {
						$buff = fread($fp, 13);
						$header = unpack('Nwidth/Nheight/Cbits/Ctype/Ccompression/Cfilter/Cinterlace', $buff);
						fclose($fp);
						return $header;
					}

					// Jump to next chunk and skip CRC
					fseek($fp, $chunk['len'] + 4, SEEK_CUR);
				}
			}

			fclose($fp);
		}

		return null;
	}

	function _isPNG8($path) {
		$header = $this->_getPNGHeader($path);

		return $header && $header['type'] == 3;
	}

	function _isPNG24($path) {
		$header = $this->_getPNGHeader($path);

		return $header && $header['type'] == 2;
	}

	function hasFunctions($list) {
		$list = split(",", $list);

		foreach($list as $item) {
			if (!function_exists($item))
				return false;
		}

		return true;
	}

	/**
	 * Get the support with current GD.
 	 * @param String $type Extensions of image.
	 * @return Array An array with booleans, crop, resize, rotate.
	 */
	function getEditSupport($type) {
		$rotate = function_exists("imagerotate");
		$crop = false;
		$resize = false;

		switch ($type) {
			case "jpg":
				$resize = $this->hasFunctions("ImageCreateTrueColor,ImageCopyResampled,ImageSX,ImageSY");
				$crop = $this->hasFunctions("ImageCreateTrueColor,ImageCopyResampled");
			break;

			case "gif":
				$resize = $this->hasFunctions("imagecolorallocate,imagefilledrectangle,imagecolortransparent,ImageCopyResampled,ImageSX,ImageSY");
				$crop = $this->hasFunctions("imagecolorallocate,imagefilledrectangle,imagecolortransparent,ImageCopyResampled");
			break;

			case "png":
				$resize = $this->hasFunctions("imagecolorallocate,imagefilledrectangle,imagecolortransparent,ImageCopyResampled,ImageSX,ImageSY");
				$crop = $this->hasFunctions("imagealphablending,imagesavealpha,ImageColorTransparent,ImageCopyResampled,ImageCreateTrueColor");
			break;
		}

		return array($crop, $resize, $rotate);
	}

	/**
	 * Formats a image based in the input parameter.
	 *
	 * Format parameters:
	 *  %f - Filename.
	 *  %e - Extension.
	 *  %w - Image width.
	 *  %h - Image height.
	 *  %tw - Target width.
	 *  %th - Target height.
	 *  %ow - Original width.
	 *  %oh - Original height.
	 *
	 *  Example: 320x240|gif=%f_%w_%h.gif,320x240=%f_%w_%h.%e
	 */
	function formatImage($path, $format, $quality = 90) {
		$chunks = explode(',', $format);
		$imageInfo = @getimagesize($path);
		$width = $imageInfo[0];
		$height = $imageInfo[1];

		foreach ($chunks as $chunk) {
			if (!$chunk)
				continue;

			$parts = explode('=', $chunk);
			$actions = array();

			$fileName = preg_replace('/\..+$/', '', basename($path));
			$extension = preg_replace('/^.+\./', '', basename($path));
			$targetWidth = $newWidth = $width;
			$targetHeight = $newHeight = $height;

			$items = explode('|', $parts[0]);
			foreach ($items as $item) {
				switch ($item) {
					case "gif":
					case "jpg":
					case "jpeg":
					case "png":
						$extension = $item;
						break;

					default:
						$matches = array();

						if (preg_match('/\s?([0-9]+)\s?x([0-9]+)\s?/', $item, $matches)) {
							$actions[] = "resize";
							$targetWidth = $matches[1];
							$targetHeight = $matches[2];
						}
				}
			}

			// Add default action
			if (count($actions) == 0)
				$actions[] = "resize";

			// Scale it
			if ($targetWidth != $width || $targetHeight != $height) {
				$scale = min($targetWidth / $width, $targetHeight / $height);
				$newWidth = $scale > 1 ? $width : floor($width * $scale);
				$newHeight = $scale > 1 ? $height : floor($height * $scale);
			}

			// Build output path
			$outPath = $parts[1];
			$outPath = str_replace("%f", $fileName, $outPath);
			$outPath = str_replace("%e", $extension, $outPath);
			$outPath = str_replace("%ow", "" . $width, $outPath);
			$outPath = str_replace("%oh", "" . $height, $outPath);
			$outPath = str_replace("%tw", "" . $targetWidth, $outPath);
			$outPath = str_replace("%th", "" . $targetHeight, $outPath);
			$outPath = str_replace("%w", "" . $newWidth, $outPath);
			$outPath = str_replace("%h", "" . $newHeight, $outPath);
			$outPath = dirname($path) . '/' . $outPath;
			$this->_mkdirs(dirname($outPath));

			foreach ($actions as $action) {
				switch ($action) {
					case 'resize':
						//debug($path, $outPath);
						$this->resizeImage($path, $outPath, $newWidth, $newHeight, "", $quality);
						break;
				}
			}
		}
	}

	/**
	 * Deletes formats for the specified image.
	 *
	 * Format parameters:
	 *  %f - Filename.
	 *  %e - Extension.
	 *  %w - Image width.
	 *  %h - Image height.
	 *  %tw - Target width.
	 *  %th - Target height.
	 *  %ow - Original width.
	 *  %oh - Original height.
	 *
	 *  Example: 320x240|gif=%f_%w_%h.gif,320x240=%f_%w_%h.%e
	 */
	function deleteFormatImages($path, $format) {
		$chunks = explode(',', $format);
		$imageInfo = @getimagesize($path);
		$width = $imageInfo[0];
		$height = $imageInfo[1];

		foreach ($chunks as $chunk) {
			if (!$chunk)
				continue;

			$parts = explode('=', $chunk);

			$fileName = preg_replace('/\..+$/', '', basename($path));
			$extension = preg_replace('/^.+\./', '', basename($path));
			$targetWidth = $newWidth = $width;
			$targetHeight = $newHeight = $height;

			$items = explode('|', $parts[0]);
			foreach ($items as $item) {
				switch ($item) {
					case "gif":
					case "jpg":
					case "jpeg":
					case "png":
						$extension = $item;
						break;

					default:
						$matches = array();

						if (preg_match('/\s?([0-9]+)\s?x([0-9]+)\s?/', $item, $matches)) {
							$targetWidth = $matches[1];
							$targetHeight = $matches[2];
						}
				}
			}

			// Scale it
			if ($targetWidth != $width || $targetHeight != $height) {
				$scale = min($targetWidth / $width, $targetHeight / $height);
				$newWidth = $scale > 1 ? $width : floor($width * $scale);
				$newHeight = $scale > 1 ? $height : floor($height * $scale);
			}

			// Build output path
			$outPath = $parts[1];
			$outPath = str_replace("%f", $fileName, $outPath);
			$outPath = str_replace("%e", $extension, $outPath);
			$outPath = str_replace("%ow", "" . $width, $outPath);
			$outPath = str_replace("%oh", "" . $height, $outPath);
			$outPath = str_replace("%tw", "" . $targetWidth, $outPath);
			$outPath = str_replace("%th", "" . $targetHeight, $outPath);
			$outPath = str_replace("%w", "" . $newWidth, $outPath);
			$outPath = str_replace("%h", "" . $newHeight, $outPath);
			$outPath = dirname($path) . '/' . $outPath;

			if (file_exists($outPath))
				unlink($outPath);
		}
	}

	/**
	 * Check for the GD functions that are beeing used.
  	 * @param String $type Extensions of image.
	 * @return Bool true or false depending on success or not.
	 */
	function canEdit($type) {
		// just make a quick check, we dont need to loop if we can't find GD at all.
		if (!function_exists("gd_info"))
			return false;

		$gdUsedFunctions = array();

		// Check type specific functions
		switch ($type) {
			case "jpg":
				$gdUsedFunctions[] = "ImagecreateFromJpeg";		
				$gdUsedFunctions[] = "ImageJpeg";
			break;

			case "gif":
				$gdUsedFunctions[] = "ImagecreateFromGif";
				$gdUsedFunctions[] = "ImageGif";
			break;

			case "png":
				$gdUsedFunctions[] = "ImagecreateFromPng";
				$gdUsedFunctions[] = "ImagePng";
			break;

			default:
				return false;
		}

		// check so that each function exists
		foreach ($gdUsedFunctions as $function) {
			if (!function_exists($function)) {
				trigger_error("Function " . $function . " does not exist.", WARNING);
				return false;
			}
		}

		return true;
	}

	function _mkdirs($path, $rights = 0777) {
		$path = preg_replace('/\/$/', '', $path);
		$dirs = array();

		// Figure out what needs to be created
		while ($path) {
			if (file_exists($path))
				break;

			$dirs[] = $path;
			$pathAr = explode("/", $path);
			array_pop($pathAr);
			$path = implode("/", $pathAr);
		}

		// Create the dirs
		$dirs = array_reverse($dirs);
		foreach ($dirs as $path) {
			if (!@is_dir($path) && strlen($path) > 0)
				mkdir($path, $rights);
		}
	}
}
?>