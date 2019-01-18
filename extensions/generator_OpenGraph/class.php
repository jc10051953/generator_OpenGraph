<?php

/**
 * Generator_OpenGraph - class generation thumbnail
 */

class Generator_OpenGraph {

    const FONT = EXTENSION_FOLDER_PATH.'/fonts/'.FONT_FAMILY;
    const FONT_COMPANY = EXTENSION_FOLDER_PATH.'/fonts/'.FONT_FAMILY_COMPANY_NAME;

    private $width_thumbnail = PICTURE_WIDTH;
    private $height_thumbnail = PICTURE_HEIGHT;

    private static $instance = NULL;

    private function __construct(){}
    private function __clone(){}




    /**
     * createThumbnail
     * @param string $sourceImage path to source JPEG/PNG image
     * @param string $new_image
     * @param string $thumbnail
     * @param string $type
     * @param string $imageCreateFrom
     * @param string $title
     * @return bool
     */
    private function createThumbnail($image, $new_image, $thumbnail, $type, $imageCreateFrom, $title) {

        if (!file_exists($new_image)) {

            $this->resizeImage($image, $new_image, $this->width_thumbnail, $this->height_thumbnail);
            $size=getimagesize($new_image);
            $this->width_thumbnail = (int)$size[0];
            $this->height_thumbnail = (int)$size[1];
        }

        $function_create = 'image' . $type;

        $image = $imageCreateFrom($new_image);

        $background = @imagecreatetruecolor($this->width_thumbnail, $this->height_thumbnail) or die('Невозможно инициализировать GD поток');
        $transparency = imagecolorallocatealpha($background, 0, 0, 0, PICTURE_BACKGROUND_TRANSPERENT);
        imagefill($background, 0, 0, $transparency);
        imagesavealpha($background, TRUE);
        imagecopyresampled($image, $background, 0, 0, 0, 0, $this->width_thumbnail, $this->height_thumbnail, imagesx($image), imagesy($image));

        $this->drawTitle($image, $title);
        $this->drawNameCompany($image);
        $this->drawLogoCompany($image);

        $function_create($image, $thumbnail, PICTURE_QUALITY);

        imagedestroy($image);
        imagedestroy($background);

        return TRUE;

    }


    /**
     * drawNameCompany
     * @param resource &img
     * @return bool
     */
    private function drawNameCompany(&$img){

        $box = imagettfbbox(FONT_SIZE_COMPANY_NAME, 0, self::FONT_COMPANY, COMPANY_NAME);

        $x = 20;
        $y = $this->height_thumbnail-($box[3]-$box[5]);
        list($r,$g,$b) = explode(',', FONT_COLOR_COMPANY_NAME);

        $color = imageColorAllocate($img, $r, $g, $b); //color

        imagettftext($img, FONT_SIZE_COMPANY_NAME, 0, $x, $y, $color, self::FONT_COMPANY, COMPANY_NAME);

        return TRUE;
    }

    /**
     * drawLogoCompany
     * @param resource &img
     * @return bool
     */
    private function drawLogoCompany(&$img){

        $path_logo = $_SERVER['DOCUMENT_ROOT'].COMPANY_LOGO;

        $type = $this->getTypeImage($path_logo);
        $imageCreateFrom = 'imageCreateFrom'.$type;

        $name = basename($path_logo, '.' . $type);
        $path_folder_img = $_SERVER['DOCUMENT_ROOT'] . GENERAL_PATH_FOLDER_THUMBNAIL;
        $path_new_logo = $path_folder_img . $name  . '.' . $type;
        if(!file_exists($path_new_logo)) {
            $max_h =  ceil($this->height_thumbnail * .15);
            $this->resizeImage($path_logo, $path_new_logo, 0, $max_h);
        }
        $logo = $imageCreateFrom($path_new_logo);
        $size=getimagesize($path_new_logo);
        $w=(int)$size[0];
        $h=(int)$size[1];

        $x = $this->width_thumbnail-$w-20;
        $y = $this->height_thumbnail-$h - 20;

        imagecopyresampled($img, $logo, $x, $y, 0, 0, $w, $h, $w, $h);

        imagedestroy($logo);

        return TRUE;
    }

    /**
     * drawTitle
     * @param resource &img
     * @param string $text
     * @return bool
     */
    private function drawTitle(&$img, $text){

        $box = imagettfbbox(FONT_SIZE, 0, self::FONT, $text);

        $padding = 20;
        $width_text = $box[2]+$box[0];
        if($width_text > $this->width_thumbnail - $padding) {
            $s = substr_count($text,' ') / 2;
            $s = (int)$s;
            $offset = 0;
            $matches = array();
            while (($pos = strpos($text, ' ', $offset)) !== FALSE) {
                $offset   = $pos + 1;
                $matches[] = $pos;
            }
            $text = str_split($text);
            $text[$matches[$s-1]] = "\n";
            $text = implode('',$text);

            $box = imagettfbbox(FONT_SIZE, 0, self::FONT, $text);
        }


        $x = ($this->width_thumbnail/2)-($box[2]-$box[0])/2;
        $y = ($this->height_thumbnail/2)-($box[3]-$box[5])/2;

        list($r,$g,$b) = explode(',', FONT_COLOR_TITLE);

        $color = imageColorAllocate($img, $r, $g, $b); //color

        if(FONT_BORDER == 'Y') {

            list($r,$g,$b) = explode(',', FONT_COLOR_BORDER);

            $shadow = imageColorAllocate($img, $r, $g, $b);

            imagettftext($img, FONT_SIZE, 0, $x + 2, $y, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x - 2, $y, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x, $y + 2, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x, $y - 2, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x + 1, $y, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x - 1, $y, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x, $y + 1, $shadow, self::FONT, $text);
            imagettftext($img, FONT_SIZE, 0, $x, $y - 1, $shadow, self::FONT, $text);
        }

        imagettftext($img, FONT_SIZE, 0, $x, $y, $color, self::FONT, $text);

        return TRUE;
    }

     /**
     * Resize image - preserve ratio of width and height.
     * @param string $sourceImage path to source JPEG/PNG image
     * @param string $targetImage path to final JPEG/PNG image file
     * @param int $maxWidth maximum width of final image (value 0 - width is optional)
     * @param int $maxHeight maximum height of final image (value 0 - height is optional)
     * @param int $quality quality of final image (0-100)
     * @return bool
     */
     private function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 75) {
         $isPNG = FALSE;
         $type = $this->getTypeImage($sourceImage);

         $imagecreatefrom = 'imagecreatefrom'.$type;
         $imagejpeg = 'image'.$type;

        // Obtain image from given source file.
        if (!$image = @$imagecreatefrom($sourceImage)) {
            return false;
        }
         if($type == 'png'){
             $isPNG = TRUE;
             $quality*=9/100;
         }
        // Get dimensions of source image.
        list($origWidth, $origHeight) = getimagesize($sourceImage);

        if ($maxWidth == 0) {
            $maxWidth  = $origWidth;
        }

        if ($maxHeight == 0) {
            $maxHeight = $origHeight;
        }

        // Calculate ratio of desired maximum sizes and original sizes.
        $widthRatio = $maxWidth / $origWidth;
        $heightRatio = $maxHeight / $origHeight;

        // Ratio used for calculating new image dimensions.
        $ratio = min($widthRatio, $heightRatio);

        // Calculate new image dimensions.
        $newWidth  = (int)$origWidth  * $ratio;
        $newHeight = (int)$origHeight * $ratio;

        // Create final image with new dimensions.
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if($isPNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage,true);
            $transparency = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newHeight, $newHeight, $transparency);
        }
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        $imagejpeg($newImage, $targetImage, $quality);

        // Free up the memory.
        imagedestroy($image);
        imagedestroy($newImage);

        return true;
    }

    /**
     * getTypeImage
     * @param string $image path to image
     * @return string $type
     */
    private function getTypeImage($image){

        $type = exif_imagetype($image);
        switch ($type) {
            case 2 :
                $type = 'jpeg';
                break;
            case 3 :
                $type = 'png';
                break;
        }

        return $type;
    }

    /**
     * getInstance
     * @return object
     */
    public static function getInstance() {

        if(is_null(self::$instance)){
            self::$instance = new Generator_OpenGraph();
        }
        return self::$instance;
    }

    /**
     * getPathImage
     * @param string $image path to image
     * @param string $title
     * @return string path to thumbnail
     */
    public function getPathImage($image, $title) {

        $type = $this->getTypeImage($image);
        $imageCreateFrom = 'imageCreateFrom'.ucfirst($type);
        $name = basename($image, '.' . $type);

        $path_folder_img = $_SERVER['DOCUMENT_ROOT'] . GENERAL_PATH_FOLDER_THUMBNAIL;
        $new_image = $path_folder_img . $name . '.' . $type;
        $thumbnail = $path_folder_img . 'th_' . $name . '.' . $type;

        if(!file_exists($thumbnail)) {
            $this->createThumbnail($image, $new_image, $thumbnail,$type, $imageCreateFrom, $title);
        }
        return GENERAL_PATH_FOLDER_THUMBNAIL .'th_' . $name . '.' . $type;
    }

}