<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Resource;

class ImageService
{
    private $dst_image;

    private $dst_path;

    private $dst_width;

    private $dst_height;

    private $src_image;

    private $src_path;

    private $src_width;

    private $src_height;

    private $radius_dst;

    private $radius_src;

    private $ttf;

    public function __construct($image_path, $ttf = null)
    {
        $this->dst_path = $image_path;
        $this->dst_image = $this->init($this->dst_path);
        list($this->dst_width, $this->dst_height) = getimagesize($this->dst_path);
        
        if (is_null($ttf)) {
            $this->ttf = public_path('fonts/msyh.ttf');
        }
    }

    public function __destruct()
    {
        imagedestroy($this->dst_image);
//        imagedestroy($this->src_image);
    }

    public function initSrcImage($image_path)
    {
        $this->src_image = $this->init($image_path);
        list($this->src_width, $this->src_height) = getimagesize($image_path);

        return $this;
    }

    public function initSrcImageFormResource($image)
    {
        $this->src_image = imagecreatefromstring($image);
//        list($this->src_width, $this->src_height) = getimagesize($image);
        list($this->src_width, $this->src_height) = getimagesizefromstring($image);
//        $this->src_width = 132;
//        $this->src_height = 132;

        return $this;
    }

    public function setSrcImage($image)
    {
        $this->src_image = $image;
        $this->src_width = imagesx($image);
        $this->src_height = imagesy($image);

        return $this;
    }

    public function init($image_path)
    {
        if (is_file($image_path)) {
            if (file_exists($image_path)) {
                $mime_tpye = getimagesize($image_path)['mime'];;
                $file_type = explode('/', $mime_tpye)[1];
                switch ($file_type) {
                    case 'jpg':
                    case 'jpeg':
                        $image_path = imagecreatefromjpeg($image_path);
                        break;
                    case 'gif':
                        $image_path = imagecreatefromgif($image_path);
                        break;
                    case 'png':
                        $image_path = imagecreatefrompng($image_path);
                        break;
                    default:
                        break;
                }
            } else {

            }
        } else {
            $image_path = imagecreatefromstring($image_path);
        }

        
        return $image_path;
    }
    
    public function text($text, array $text_rgb = [0, 0, 0],  $position_x, $position_y, $font_size = 20)
    {
        list($red, $green, $blue) = $text_rgb;
//        dd($this->dst_width.'_'.$this->dst_height);
//        $im = imagecreatetruecolor($this->dst_width, $this->dst_height);
//        $im = imagecreate($this->dst_width, $this->dst_height);
//        imagecolorallocate($im, $red, $green, $blue);
//        imagecolorallocate($im, $red, $green, $blue);
//        imagecolorallocate($im, $red, $green, $blue);
//        $text_color = imagecolorallocate($im, $red, $green, $blue);
//        dd($this->getRGB(16777215));
        imagettftext($this->dst_image, $font_size, 0, $this->dst_width * $position_x, $this->dst_height * $position_y, 17, $this->ttf, $text);
        
        return $this;
    }

    public function addSrcImage($position_x, $position_y, $src_width = 1, $src_height = 1, $pct = 100)
    {
        imagecopymerge($this->dst_image, $this->src_image, $this->dst_width * $position_x, $this->dst_height * $position_y, 0, 0, $this->src_width * $src_width, $this->src_height * $src_height, $pct);

        return $this;
    }

    public function addSrcRadiusImage($position_x, $position_y, $src_width = 1, $src_height = 1, $pct = 100)
    {
        imagecopymerge($this->dst_image, $this->radius_src, $this->dst_width * $position_x, $this->dst_height * $position_y, 0, 0, $this->src_width * $src_width, $this->src_height * $src_height, $pct);

        return $this;
    }

    public function black2color(array $rgb = [0, 0, 0])
    {
        $dst_image = $this->dst_image;
        $width = $this->dst_width;
        $height = $this->dst_height;
        $image = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bg);
        $color_image = imagecreatetruecolor(1, 1);
        list($color_red,$color_green, $color_blue) = $rgb;
        $color_bg = imagecolorallocate($color_image, $color_red, $color_green, $color_blue);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgbColor = imagecolorat($dst_image, $x, $y);
                $red = ($rgbColor >> 16) & 0xFF;
                $green = ($rgbColor >> 8) & 0xFF;
                $blue = $rgbColor & 0xFF;
                if ($red < 20 & $green < 20 & $blue < 20) {
                    imagesetpixel($image, $x, $y, $color_bg);
                } else {
                    imagesetpixel($image, $x, $y, $rgbColor);
                }
            }
        }
        $this->dst_image = $image;

        return $this;
    }

    public function radius($image_resource, $image_width, $image_height, $radius = 15)
    {
        $w  = $image_width;
        $h  = $image_height;
//        $rgbColor = imagecolorat($image_resource, 45,45);
//        $red = ($rgbColor >> 16) & 0xFF;
//        $g = ($rgbColor >> 8) & 0xFF;
//        $b = $rgbColor & 0xFF;
//        dd($red.'_'.$g.'_'.$b);
//        dd($w.'_'.$h);
        // $radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
//                header('Content-Type:image/png');
//                imagepng($image_resource);
//                exit;
                $rgbColor = imagecolorat($image_resource, $x, $y);
                $red = ($rgbColor >> 16) & 0xFF;
                $g = ($rgbColor >> 8) & 0xFF;
                $b = $rgbColor & 0xFF;
//                var_dump($red.'_'.$g.'_'.$b);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                    //不在四角的范围内,直接画
                    imagesetpixel($img, $x, $y, $rgbColor);
                } else {
                    //在四角的范围内选择画
                    //上左
                    $y_x = $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //上右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下左
                    $y_x = $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }
        }
        return $img;
    }

    public function resizeSrc($max = 300)
    {
        $src = $this->src_image;
        $width = $this->src_width;
        $height = $this->src_height;
        if ($width > $height) {
            $this->src_width = $max;
            $this->src_height = $height * ($max/$width);
        } else {
            $this->src_height = $max;
            $this->src_width = $width * ($max/$height);
        }


        $image = imagecreate($this->src_width, $this->src_height);
        imagecopyresampled($image, $src, 0, 0, 0, 0, $this->src_width, $this->src_height, $width, $height);
        $this->src_image = $image;

        return $this;
    }

    public function radiusSrc()
    {
        $this->src_image = $this->radius($this->src_image, $this->src_width, $this->src_height, $this->src_width/2);

        return $this;
    }

    public function getSrc()
    {
        return $this->src_image;
    }

    public function radiusDst()
    {
        $this->radius_dst = $this->radius($this->dst_image, $this->dst_width, $this->dst_height, $this->dst_width/2);

        return $this->radius_dst;
    }
    
    public function addImage($image_path, $position_x, $position_y, $src_width = 1, $src_height = 1, $pct = 100)
    {
        $image = $this->init($image_path);
        list($image_width, $image_height) = getimagesize($image_path);
        imagecopymerge($this->dst_image, $image, $this->dst_width * $position_x, $this->dst_height * $position_y, 0, 0, $image_width * $src_width, $image_height * $src_height, $pct);
        ImageDestroy($image);

        return $this;
    }
    
    public function save($file_path)
    {
        imagepng($this->dst_image, $file_path);
        
        return $this->dst_image;
    }

    public function saveJPG($file_path)
    {
        imagejpeg($this->dst_image, $file_path);

        return $this->dst_image;
    }
    
    public function get()
    {
        return $this->dst_image;
    }

    public function getRGB($rgbColor)
    {
        $r = ($rgbColor >> 16) & 0xFF;
        $g = ($rgbColor >> 8) & 0xFF;
        $b = $rgbColor & 0xFF;

        return [$r, $g, $b];
    }

//    /**
//     * blog:http://www.zhaokeli.com
//     * 处理圆角图片
//     * @param  string  $imgpath 源图片路径
//     * @param  integer $radius  圆角半径长度默认为15,处理成圆型
//     * @return [type]           [description]
//     */
//    public function radius_img($imgpath = './t.png', $radius = 15) {
//        $ext     = pathinfo($imgpath);
//        $src_img = null;
//        switch ($ext['extension']) {
//            case 'jpg':
//                $src_img = imagecreatefromjpeg($imgpath);
//                break;
//            case 'png':
//                $src_img = imagecreatefrompng($imgpath);
//                break;
//        }
//        $wh = getimagesize($imgpath);
//        $w  = $wh[0];
//        $h  = $wh[1];
//        // $radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
//        $img = imagecreatetruecolor($w, $h);
//        //这一句一定要有
//        imagesavealpha($img, true);
//        //拾取一个完全透明的颜色,最后一个参数127为全透明
//        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
//        imagefill($img, 0, 0, $bg);
//        $r = $radius; //圆 角半径
//        for ($x = 0; $x < $w; $x++) {
//            for ($y = 0; $y < $h; $y++) {
//                $rgbColor = imagecolorat($src_img, $x, $y);
//                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
//                    //不在四角的范围内,直接画
//                    imagesetpixel($img, $x, $y, $rgbColor);
//                } else {
//                    //在四角的范围内选择画
//                    //上左
//                    $y_x = $r; //圆心X坐标
//                    $y_y = $r; //圆心Y坐标
//                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
//                        imagesetpixel($img, $x, $y, $rgbColor);
//                    }
//                    //上右
//                    $y_x = $w - $r; //圆心X坐标
//                    $y_y = $r; //圆心Y坐标
//                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
//                        imagesetpixel($img, $x, $y, $rgbColor);
//                    }
//                    //下左
//                    $y_x = $r; //圆心X坐标
//                    $y_y = $h - $r; //圆心Y坐标
//                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
//                        imagesetpixel($img, $x, $y, $rgbColor);
//                    }
//                    //下右
//                    $y_x = $w - $r; //圆心X坐标
//                    $y_y = $h - $r; //圆心Y坐标
//                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
//                        imagesetpixel($img, $x, $y, $rgbColor);
//                    }
//                }
//            }
//        }
//        return $img;
//    }

//    <?php
//
//    header("content-type:image/png");
//    $imgg = radius_img('./tt.png', 20);
//    imagepng($imgg);
//    imagedestroy($imgg);
}