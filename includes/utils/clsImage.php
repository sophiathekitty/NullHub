<?php 
// image class
/**
 * A really old image class that i think maybe still works. mainly it's good for 
 */
class clsImage {
	// private vars
	var $im;
	var $max_size;
	var $type;
	var $width;
	var $height;
	var $size;
	var $error;
	var $imageTypes;
	var $loaded;
	// constructor
	function __construct(){
		$this->loaded = false;
		$this->max_size = 100000000;
		$this->imageTypes = array(
					IMG_GIF=>'GIF',
					IMG_JPG=>'JPG',
					IMG_PNG=>'PNG',
					IMG_WBMP=>'WBMP'
				);
	}
	/**
	 * loads an image into memory
	 * @param string $path the path to the image
	 * @return bool returns true if successful 
	 */
	function load($path){
		if(!is_file($path)){
			$this->error = "Can't find $path, or it's not a file!";
			return false;
		}
		list($this->width, $this->height, $this->type, $attr) = getimagesize($path);
		@$this->size = filesize($path) or $this->size = 0;
		if($this->size < $this->max_size){
			if(imagetypes() & $this->type){
				switch($this->type){
					case IMG_GIF:
						$this->im=imagecreatefromgif($path);
					break;
					case IMG_JPG:
						$this->im=imagecreatefromjpeg($path);
					break;
					case IMG_PNG:
						$this->im=imagecreatefrompng($path);
					break;
					case IMG_WBMP:
						$this->im=imagecreatefromwbmp($path);
					break;
					default:
						$this->error = "Unknown Image Type. ($this->type)";
						return false;
					break;
				}
			} else {
				$this->error = "Unsupported Image Type. (".$this->imageTypes[$this->type]."[$this->type])";
				return false;
			}
		} else {
			$this->error = "Image too large. ($this->size)";
			return false;
		}
		$this->loaded = true;
		return true;
	}
	/**
	 * saves the image
	 * @param string $path where to save the image
	 */
	function save($path){
		if($this->loaded){
			switch($this->type){
				case 1:
					imagegif($this->im,$path);
				break;
				case 2:
					imagejpeg($this->im,$path);
				break;
				case 3:
					imagepng($this->im,$path);
				break;
				case 5:
					imagewbmp($this->im,$path);
				break;
				default:
					$this->error = "Unknown Image Type. ($this->type)";
					return false;
				break;
			}
			return true;
		}
	}
	/**
	 * resize an image. might also be able to grab a portion of image and make it the whole image?
	 * @param int $w the final width
	 * @param int $h the final height
	 * @param int $x the x offset for source (for cropping)
	 * @param int $y the x offset for source (for cropping)
	 * @param int $sw the source width
	 * @param int $sh the source height
	 */
	function resize($w,$h,$x,$y,$sw,$sh){
		if($this->loaded){
			$small = imagecreatetruecolor($w, $h);    // new image
			imagecopyresampled($small, $this->im,0,0,$x,$y,$w, $h, $sw, $sh);	// below is main function resampling image
			imagedestroy($this->im);
			$this->im = $small;
		}
	}
	/**
	 * scales an image to fit with a max width and height
	 * @param int $maxX the max width
	 * @param int $maxY the max height
	 */
	function scale($maxX,$maxY){
		if($this->loaded){
			if($this->width > $this->height){
				$w = $maxX;
				$percent = ($this->width / $w);
				$h = ($this->height / $percent);
			} else {
				$h = $maxY;
				$percent = ($this->height / $h);
				$w = ($this->width / $percent);
			}
			$this->resize($w,$h,0,0,$this->width,$this->height);
		}
	}
	/**
	 * scales an image to fit with a max width and height and crops out any extra
	 * @param int $w the final width
	 * @param int $h the final height
	 */
	function cropScale($w,$h){
		if($this->loaded){
			$width = abs($this->width - $w);
			$height = abs($this->height - $h);
			if($width > $height){
				$y = 0;
				$sh = $this->height;
				$percent = ($this->height / $h);
				$x = ($this->width / 2) - (($w * $percent) / 2);
				$sw = $w * $percent;
			} else {
				$x = 0;
				$sw = $this->width;
				$percent = ($this->width / $w);
				$y = ($this->height / 2) - (($h * $percent) / 2);
				$sh = $h * $percent;
			}
			if($x < 0){
				$x = 0;
				$sw = $this->width;
				$percent = ($this->width / $w);
				$y = ($this->height / 2) - (($h * $percent) / 2);
				$sh = $h * $percent;
			}
			if($y < 0){
				$y = 0;
				$sh = $this->height;
				$percent = ($this->height / $h);
				$x = ($this->width / 2) - (($w * $percent) / 2);
				$sw = $w * $percent;
			}
			$this->resize($w,$h,$x,$y,$sw,$sh);
		}
	}
	/**
	 * in theory this should display the image in memory (php acts like an image file)
	 * @todo i think the last time i tried this it wasn't working anymore
	 */
	function display(){
		if($this->loaded){
			switch($this->type){
				case IMG_GIF:
					header('Content-Type: image/gif');
					imagegif($this->im);
				break;
				case IMG_JPG:
					header('Content-Type: image/jpeg');
					imagejpeg($this->im);
				break;
				case IMG_PNG:
					header('Content-Type: image/png');
					imagepng($this->im);
				break;
				case IMG_WBMP:
					header('Content-Type: image/wbmp');
					imagewbmp($this->im);
				break;
				default:
					$this->error = "Unknown Image Type. ($this->type)";
					return false;
				break;
			}
		}
	}
	/**
	 * apply a color to an image?
	 */
	function colorize($r,$g,$b,$a){
		if($this->loaded){
			$im = imagecreatetruecolor($this->width,$this->height);
			$color = imagecolorallocate($im,$r,$g,$b);
			imagefill($im,0,0,$color);
			imagecopymerge($this->im,$im,0,0,0,0,$this->width,$this->height,$a);
		}
	}
}
?>