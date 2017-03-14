<?php

/**
 * GRENO CMS Class for image transform
 * Using GD2 > library
 *
 * @author Janis Rublevskis <janis.rublevskis@x-it.lv>
 * @version 2.0
 * @package GrenoCMS
 * @copyright    Copyright (c) 2008, SIA "X IT".
 * @link        http://x-it.lv
 * @since        Version 1.0
 */
Class Gdhelper {
	var $source = '';     //pat to source image
	var $destination = '';     //path to new image
	var $watermark = '';     //path to watermark
	var $width = 480;    //image height
	var $height = 640;    //image width
	var $horizontal = "LEFT"; //placemark for watermark
	var $vertical = "TOP";  //placemark for watermark
	var $padding = 0;      //padding for watermark
	var $quality = 100;
	var $background = array( 255, 255, 255 ); //background color for new image
	var $tmp_dest = '/tmp/greno_tmp_image.jpg';

	public static function extension( $filename ) {
		if ( ! file_exists( $filename ) ) {
			return false;
		} else {
			$arr = getimagesize( $filename );
			if ( isset( $arr[2] ) ) {
				switch ( $arr[2] ) {
					case 1:
						return 'gif';
						break;
					case 2:
						return 'jpg';
						break;
					case 3:
						return 'png';
						break;
				}
			}
		}

		return 'jpg';
	}

	public function rotateByEXIFOrientation( $orientation ) {
		switch ( $orientation ) {
			case 2: //flip horizontaly
				$this->flip( IMG_FLIP_HORIZONTAL );
				break;
			case 3: //rotate 180
				$this->rotate( 180 );
				break;
			case 4: //180 + hor flip
				$this->rotate( 180 );
				$this->flip( IMG_FLIP_HORIZONTAL );
				break;
			case 5:
				//90 + hor flip
				$this->rotate( -90 );
				$this->flip( IMG_FLIP_HORIZONTAL );
				break;
			case 6: //90
				$this->rotate( -90 );
				break;
			case 7: // -90 + hor flip
				$this->rotate( 90 );
				$this->flip( IMG_FLIP_HORIZONTAL );
				break;
			case 8:
				$this->rotate( 90 );
				break;
		}

		return $this;
	}

	public function flip( $mode ) {
		list( $owdt, $ohgt, $otype ) = getimagesize( $this->source );
		switch ( $otype ) {
			case 1:
				$newimg = imagecreatefromgif( $this->source );
				break;
			case 2:
				$newimg = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$newimg = imagecreatefrompng( $this->source );
				break;
			default:
				echo "Unkown filetype (file {$this->source}, typ $otype)<br />";

				return false;
		}

		if ( $newimg ) {
			if ( ! $this->destination ) {
				return $newimg;
			}
			if ( ! is_dir( dirname( $this->destination ) ) ) {
				mkdir( dirname( $this->destination ) );
			}

			imageflip( $newimg, $mode );

			switch ( $otype ) {
				case 1:
					imagegif( $newimg, $this->destination );
					break;
				case 2:
					imagejpeg( $newimg, $this->destination, $this->quality );
					break;
				case 3:
					imagepng( $newimg, $this->destination );
					break;
			}

			imagedestroy( $newimg );
		}

		return $this;
	}

	public function rotate( $deg = 0 ) {
		list( $owdt, $ohgt, $otype ) = getimagesize( $this->source );
		switch ( $otype ) {
			case 1:
				$newimg = imagecreatefromgif( $this->source );
				break;
			case 2:
				$newimg = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$newimg = imagecreatefrompng( $this->source );
				break;
			default:
				echo "Unkown filetype (file {$this->source}, typ $otype)<br />";

				return false;
		}

		if ( $newimg ) {
			if ( ! $this->destination ) {
				return $newimg;
			}
			if ( ! is_dir( dirname( $this->destination ) ) ) {
				mkdir( dirname( $this->destination ) );
			}

			$newimg = imagerotate( $newimg, $deg, 0 );

			switch ( $otype ) {
				case 1:
					imagegif( $newimg, $this->destination );
					break;
				case 2:
					imagejpeg( $newimg, $this->destination, $this->quality );
					break;
				case 3:
					imagepng( $newimg, $this->destination );
					break;
			}

			imagedestroy( $newimg );
		}

		return $this;
	}

	/**
	 * Resize image proportionaly by longest dimension
	 * @return mixed
	 */
	function resizeImg() {
		list( $owdt, $ohgt, $otype ) = getimagesize( $this->source );
		switch ( $otype ) {
			case 1:
				$newimg = imagecreatefromgif( $this->source );
				break;
			case 2:
				$newimg = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$newimg = imagecreatefrompng( $this->source );
				break;
			default:
				echo "Unkown filetype (file {$this->source}, typ $otype)<br />";

				return false;
		}

		if ( $newimg ) {
			$this->Resample( $newimg, $owdt, $ohgt );
			if ( ! $this->destination ) {
				return $newimg;
			}
			if ( ! is_dir( dirname( $this->destination ) ) ) {
				mkdir( dirname( $this->destination ) );
			}

			switch ( $otype ) {
				case 1:
					imagegif( $newimg, $this->destination );
					break;
				case 2:
					imagejpeg( $newimg, $this->destination, $this->quality );
					break;
				case 3:
					imagepng( $newimg, $this->destination );
					break;
			}

			imagedestroy( $newimg );
		}

		return $this;
	}

	/**
	 * Resample image changing source image object
	 *
	 * @param $img resource - image object
	 * @param $owdt integer - image width
	 * @param $ohgt integer - image height
	 *
	 * @return array
	 */
	function Resample( &$img, $owdt, $ohgt ) {
		if ( ! $this->width ) {
			$divwdt = 0;
		} else {
			$divwdt = $owdt / $this->width;
		}

		if ( ! $this->height ) {
			$divhgt = 0;
		} else {
			$divhgt = $ohgt / $this->height;
		}

		if ( $owdt >= $ohgt ) {
			$newwdt = $this->width;
			$newhgt = round( $ohgt / $divwdt );
			if ( $newwdt > $owdt ) {
				return false;
			}
		} else {
			$newhgt = $this->height;
			$newwdt = round( $owdt / $divhgt );
			if ( $newhgt > $ohgt ) {
				return false;
			}
		}

		$tn = imagecreatetruecolor( $newwdt, $newhgt );

		imagecopyresampled( $tn, $img, 0, 0, 0, 0, $newwdt, $newhgt, $owdt, $ohgt );
		imagedestroy( $img );

		$img = $tn;

		return array( $newwdt, $newhgt );
	}

	/**
	 * Resizes image only by height
	 * @return mixed
	 */
	function resizeImgByHeight() {
		// Get new dimensions
		$arr = getimagesize( $this->source );

		$width_orig  = $arr[0];
		$height_orig = $arr[1];
		$width       = (int) ( ( $this->height / $height_orig ) * $width_orig );

		switch ( $arr[2] ) {
			case 1:
				$image = imagecreatefromgif( $this->source );
				break;
			case 2:
				$image = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$image = imagecreatefrompng( $this->source );
				break;
			default:
				return false;
				break;
		}

		// Resample
		$image_p = imagecreatetruecolor( $width, $this->height );
		imagecopyresampled( $image_p, $image, 0, 0, 0, 0, $width, $this->height, $width_orig, $height_orig );

		// Output
		switch ( $arr[2] ) {
			case 1:
				imagegif( $image_p, $this->destination );
				break;
			case 2:
				imagejpeg( $image_p, $this->destination, $this->quality );
				break;
			case 3:
				imagepng( $image_p, $this->destination );
				break;
		}

		return false;
	}

	/**
	 * Creates blank image
	 */
	function create_blank( $destination = false ) {
		if ( ! $destination ) {
			$destination = $this->tmp_dest;
		}
		$im    = imagecreate( $this->width, $this->height );
		$color = imagecolorallocate( $im, $this->background[0], $this->background[1], $this->background[2] );
		\
			imagefill( $im, 0, 0, $color );
		imagejpeg( $im, $destination, $this->quality );
		imagedestroy( $im );

		return $destination;
	}


	/**
	 * Resizes image only by width
	 * @return mixed
	 */
	function resizeImgByWidth() {
		// Get new dimensions
		$arr = getimagesize( $this->source );

		$width_orig  = $arr[0];
		$height_orig = $arr[1];
		$height      = (int) ( ( $this->width / $width_orig ) * $height_orig );

		switch ( $arr[2] ) {
			case 1:
				$image = imagecreatefromgif( $this->source );
				break;
			case 2:
				$image = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$image = imagecreatefrompng( $this->source );
				break;
			default:
				return false;
				break;
		}

		// Resample
		$image_p = imagecreatetruecolor( $this->width, $height );

		switch ( $arr[2] ) {
			case 1:
			case 3:
				$color = imagecolortransparent( $image_p, imagecolorallocatealpha( $image_p, 0, 0, 0, 127 ) );
				imagefill( $image_p, 0, 0, $color );
				imagesavealpha( $image_p, true );
				break;
		}

		imagecopyresampled( $image_p, $image, 0, 0, 0, 0, $this->width, $height, $width_orig, $height_orig );

		// Output
		switch ( $arr[2] ) {
			case 1:
				imagegif( $image_p, $this->destination );
				break;
			case 2:
				imagejpeg( $image_p, $this->destination, $this->quality );
				break;
			case 3:
				imagepng( $image_p, $this->destination );
				break;
		}
		imagedestroy( $image );

		return $this;
	}

	public function crop( $offsetX = 0, $offsetY = 0 ) {
		//dabujam vecos izmeerus
		list( $oldWidth, $oldHeight, $oldtype ) = getimagesize( $this->source );
		// atrodam dimensijas
		#jaunais mazaaks par veco - taisam peec vecaa
		$this->width  = $this->width > $oldWidth ? $oldWidth : $this->width;
		$this->height = $this->height > $oldHeight ? $oldHeight : $this->height;

		//itamajaa baaziisim iekshaa
		$img = imagecreatetruecolor( $this->width, $this->height );
		//uztaisam bildi no vecaas
		switch ( $oldtype ) {
			case 1:
				$newimg = imagecreatefromgif( $this->source );
				break;
			case 2:
				$newimg = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$newimg = imagecreatefrompng( $this->source );
				break;
			default:
				imagedestroy( $img );

				return false;
		}

		imagecopy( $img, $newimg, 0, 0, $offsetX, $offsetY, $this->width, $this->height );
		switch ( $oldtype ) {
			case 1:
				imagegif( $img, $this->destination );
				break;
			case 2:
				imagejpeg( $img, $this->destination, $this->quality );
				break;
			case 3:
				imagepng( $img, $this->destination, round( ( $this->quality / 10 ) - 1 ) );
				break;
		}
		imagedestroy( $img );

		return $this;
	}

	/**
	 * Crops image to middle by width and height
	 * @return mixed
	 */
	function cropImageToMiddle() {
		//dabujam vecos izmeerus
		list( $oldWidth, $oldHeight, $oldtype ) = getimagesize( $this->source );
		// atrodam dimensijas
		#jaunais mazaaks par veco - taisam peec vecaa
		$this->width  = $this->width > $oldWidth ? $oldWidth : $this->width;
		$this->height = $this->height > $oldHeight ? $oldHeight : $this->height;
		#apreekjinam dimensijas
		$newDimX = round( $oldWidth / 2 ) - round( $this->width / 2 );
		$newDimY = round( $oldHeight / 2 ) - round( $this->height / 2 );

		//itamajaa baaziisim iekshaa
		$img = imagecreatetruecolor( $this->width, $this->height );
		//uztaisam bildi no vecaas
		switch ( $oldtype ) {
			case 1:
				$newimg = imagecreatefromgif( $this->source );
				break;
			case 2:
				$newimg = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$newimg = imagecreatefrompng( $this->source );
				break;
			default:
				imagedestroy( $img );

				return false;
		}
		//kopeejam peec vajadziigajiem parametriem
		imagecopy( $img, $newimg, 0, 0, $newDimX, $newDimY, $oldWidth, $oldHeight );
		switch ( $oldtype ) {
			case 1:
				imagegif( $img, $this->destination );
				break;
			case 2:
				imagejpeg( $img, $this->destination, $this->quality );
				break;
			case 3:
				imagepng( $img, $this->destination, round( ( $this->quality / 10 ) - 1 ) );
				break;
		}
		imagedestroy( $img );

		return $this;
	}


	/**
	 *    Resizes image to given dimensions and then crop to middle
	 * @return void
	 */
	function resizeCrop() {
		list( $width, $height ) = getimagesize( $this->source );
		if ( ( $this->width / $width ) > ( $this->height / $height ) ) {
			$this->resizeImgByWidth();
		} else {
			$this->resizeImgByHeight();
		}
		$tempsource   = $this->source;
		$this->source = $this->destination;
		$this->cropImageToMiddle();
		$this->source = $tempsource;
	}

	function resizeProportionally() {
		list( $width, $height ) = getimagesize( $this->source );
		if ( $width < $height ) {
			$this->resizeImgByWidth();
		} else {
			$this->resizeImgByHeight();
		}
	}

	/**
	 * function for placing PNG watermark on JPG images
	 * horizontal positions: LEFT, RIGHT, MIDDLE, numeric value
	 * vertical positions: TOP, BOTTOM, MIDDLE, numeric value
	 * can enable padding aswell
	 * @return self|boolean
	 */
	public function setWatermark() {
		list( $sWidth, $sHeight, $size ) = getimagesize( $this->source );
		list( $wmWidth, $wmHeight, $sizeWM ) = getimagesize( $this->watermark );

		//determine horizontal coordinates
		if ( is_numeric( $this->horizontal ) ) :
			$startX = $this->horizontal;
		else :
			switch ( $this->horizontal ) :
				case "LEFT":
					$startX = $this->padding;
					break;
				case "RIGHT":
					$startX = $sWidth - $wmWidth - $this->padding;
					break;
				case "MIDDLE":
					$startX = floor( $sWidth / 2 ) - floor( $wmWidth / 2 );
					break;
				default:
					$startX = 0;
			endswitch;
		endif;

		//determine vertical coordinates
		if ( is_numeric( $this->vertical ) ) :
			$startY = $this->vertical;
		else :
			switch ( $this->vertical ) :
				case "TOP":
					$startY = $this->padding;
					break;
				case "BOTTOM":
					$startY = $sHeight - $wmHeight - $this->padding;
					break;
				case "MIDDLE":
					$startY = floor( $sHeight / 2 ) - floor( $wmHeight / 2 );
					break;
				default:
					$startY = 0;
			endswitch;
		endif;

		switch ( $size ) {
			case 1:
				$source = imagecreatefromgif( $this->source );
				break;
			case 2:
				$source = imagecreatefromjpeg( $this->source );
				break;
			case 3:
				$source = imagecreatefrompng( $this->source );
				break;
			default:
				return false;
		}
		switch ( $sizeWM ) {
			case 1:
				$watermark = imagecreatefromgif( $this->watermark );
				break;
			case 2:
				$watermark = imagecreatefromjpeg( $this->watermark );
				break;
			case 3:
				$watermark = imagecreatefrompng( $this->watermark );
				break;
			default:
				return false;
		}
		imagecopy( $source, $watermark, $startX, $startY, 0, 0, $wmWidth, $wmHeight );
		imagejpeg( $source, $this->destination, $this->quality );
		imagedestroy( $source );

		return $this;
	}

	function merge( $source, $stamp = false, $destination = false ) {
		if ( ! $destination ) {
			$destination = $this->destination;
		}
		if ( ! $stamp ) {
			$stamp = $this->destination;
		}
		list( $sWidth, $sHeight ) = getimagesize( $stamp );
		list( $bWidth, $bHeight ) = getimagesize( $source );
		$stamp  = imagecreatefromjpeg( $stamp );
		$source = imagecreatefromjpeg( $source );
		if ( $bWidth > $sWidth ) {
			$startX        = floor( ( $bWidth - $sWidth ) / 2 );
			$source_pointX = 0;
		} else {
			$startX        = 0;
			$source_pointX = floor( ( $sWidth - $bWidth ) / 2 );
		}
		if ( $bHeight > $sHeight ) {
			$startY        = floor( ( $bHeight - $sHeight ) / 2 );
			$source_pointY = 0;
		} else {
			$startY        = 0;
			$source_pointY = floor( ( $sHeight - $bHeight ) / 2 );
		}
		imagecopy( $source, $stamp, $startX, $startY, $source_pointX, $source_pointY, $sWidth, $sHeight );
		imagejpeg( $source, $destination, $this->quality );
		imagedestroy( $source );
	}
}

?>