<?php
class Skin2d {
    private $image = NULL;
 
    function __destructor () {
        if ($this->image != NULL) {
            imagedestroy($this->image);
        }
    }

    function AssignSkinFromFile ($file) {
        if ($this->image != NULL) {
            imagedestroy($this->image);
        }
        if(($this->image = imagecreatefrompng($file)) == False) {
            // Error occured
            throw new Exception("Could not open PNG file.");
        }
        if(!$this->Valid()) {
            throw new Exception("Invalid skin image.");
        }
    }

    function AssignSkinFromString ($data) {
        if ($this->image != NULL) {
            imagedestroy($this->image);
        }
        if(($this->image = imagecreatefromstring($data)) == False) {

            throw new Exception("Could not load image data from string.");
        }
        if(!$this->Valid()) {
            throw new Exception("Invalid skin image.");
        }
    }

    function Width () {
        if($this->image != NULL) {
            return imagesx($this->image);
        } else {
            throw new Exception("No skin loaded.");
        }
    }

    function Height () {
        if($this->image != NULL) {
            return imagesy($this->image);
        } else {
            throw new Exception("No skin loaded.");
        }
    }
 
    function Valid () {
        return ($this->Width() != 64 || $this->Height() != 32) ? False : True;
    }
 
    function FrontImage ($scale = 1, $r = 255, $g = 255, $b = 255) {
        $newWidth = 16 * $scale;
        $newHeight = 32 * $scale;
 
        $newImage = imagecreatetruecolor(16, 32);
        $background = imagecolorallocate($newImage, $r, $g, $b);
        imagefilledrectangle($newImage, 0, 0, 16, 32, $background);
 
        imagecopy($newImage, $this->image, 4, 0, 8, 8, 8, 8);
         $this->imagecopyalpha($newImage, $this->image, 4, 0, 40, 8, 8, 8, imagecolorat($this->image, 63, 0));
        imagecopy($newImage, $this->image, 4, 8, 20, 20, 8, 12);
        imagecopy($newImage, $this->image, 8, 20, 4, 20, 4, 12);
        imagecopy($newImage, $this->image, 4, 20, 4, 20, 4, 12);
        imagecopy($newImage, $this->image, 12, 8, 44, 20, 4, 12);
        imagecopy($newImage, $this->image, 0, 8, 44, 20, 4, 12);
 
        if($scale != 1) {
            $resize = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresized($resize, $newImage, 0, 0, 0, 0, $newWidth, $newHeight, 16, 32);
            imagedestroy($newImage);
            return $resize;
        }
 
        return $newImage;
    }
 
    function BackImage ($scale = 1, $r = 255, $g = 255, $b = 255) {
        $newWidth = 16 * $scale;
        $newHeight = 32 * $scale;
 
        $newImage = imagecreatetruecolor(16, 32);
        $background = imagecolorallocate($newImage, $r, $g, $b);
        imagefilledrectangle($newImage, 0, 0, 16, 32, $background);
 
        imagecopy($newImage, $this->image, 4, 0, 24, 8, 8, 8);
        $this->imagecopyalpha($newImage, $this->image, 4, 0, 56, 8, 8, 8, imagecolorat($this->image, 63, 0));
        imagecopy($newImage, $this->image, 4, 8, 32, 20, 8, 12);
        imagecopy($newImage, $this->image, 8, 20, 12, 20, 4, 12);
        imagecopy($newImage, $this->image, 4, 20, 12, 20, 4, 12);
        imagecopy($newImage, $this->image, 12, 8, 52, 20, 4, 12);
        imagecopy($newImage, $this->image, 0, 8, 52, 20, 4, 12);
 
        if($scale != 1) {
            $resize = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresized($resize, $newImage, 0, 0, 0, 0, $newWidth, $newHeight, 16, 32);
            imagedestroy($newImage);
            return $resize;
        }
 
        return $newImage;
    }
 
    function CombinedImage ($scale = 1, $r = 255, $g = 255, $b = 255) {
        $newWidth = 37 * $scale;
        $newHeight = 32 * $scale;
 
        $newImage = imagecreatetruecolor(37, 32);
        $background = imagecolorallocate($newImage, $r, $g, $b);
        imagefilledrectangle($newImage, 0, 0, 37, 32, $background);
 
        imagecopy($newImage, $this->image, 4, 0, 8, 8, 8, 8);
        $this->imagecopyalpha($newImage, $this->image, 4, 0, 40, 8, 8, 8, imagecolorat($this->image, 63, 0));
        imagecopy($newImage, $this->image, 4, 8, 20, 20, 8, 12);
        imagecopy($newImage, $this->image, 8, 20, 4, 20, 4, 12);
        imagecopy($newImage, $this->image, 4, 20, 4, 20, 4, 12);
        imagecopy($newImage, $this->image, 12, 8, 44, 20, 4, 12);
        imagecopy($newImage, $this->image, 0, 8, 44, 20, 4, 12);
 
        imagecopy($newImage, $this->image, 25, 0, 24, 8, 8, 8);
        $this->imagecopyalpha($newImage, $this->image, 25, 0, 56, 8, 8, 8, imagecolorat($this->image, 63, 0));
        imagecopy($newImage, $this->image, 25, 8, 32, 20, 8, 12);
        imagecopy($newImage, $this->image, 29, 20, 12, 20, 4, 12);
        imagecopy($newImage, $this->image, 25, 20, 12, 20, 4, 12);
        imagecopy($newImage, $this->image, 33, 8, 52, 20, 4, 12);
        imagecopy($newImage, $this->image, 21, 8, 52, 20, 4, 12);
 
        if($scale != 1) {
            $resize = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresized($resize, $newImage, 0, 0, 0, 0, $newWidth, $newHeight, 37, 32);
            imagedestroy($newImage);
            return $resize;
        }
 
        return $newImage;
    }

    function imagecopyalpha($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $w, $h, $bg) {
        for($i = 0; $i < $w; $i++) {
            for($j = 0; $j < $h; $j++) {
 
                $rgb = imagecolorat($src, $src_x + $i, $src_y + $j);
 
                if(($rgb & 0xFFFFFF) == ($bg & 0xFFFFFF)) {
                    $alpha = 127;
                } else {
                    $colors = imagecolorsforindex($src, $rgb);
                    $alpha = $colors["alpha"];
                }
                imagecopymerge($dst, $src, $dst_x + $i, $dst_y + $j, $src_x + $i, $src_y + $j, 1, 1, 100 - (($alpha / 127) * 100));
            }
        }
    }
}
?><?php
$path = $_GET[skinpath];
$test = new Skin2d();
$test->AssignSkinFromFile($path);
 
header('Content-type: image/png' || 'Content-type: image/x-png');
$img = $test->CombinedImage(5);
imagepng($img);
imagedestroy($img);
?>