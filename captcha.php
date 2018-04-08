<?php
    function captcha($num=4,$size=20, $width=100,$height=40){
        // 画图像
        $im = imagecreatetruecolor($width,$height);
        // 画背景
 		$bg_color = imagecolorallocate($im, mt_rand(180,250), mt_rand(200,255), mt_rand(200,220));
        imagefilledrectangle($im,0,0,$width,$height,$bg_color);
        // 画边框
       	$bd_color = imagecolorallocate($im, mt_rand(0,200), mt_rand(0,120), mt_rand(0,120));
        imagerectangle($im,0,0,$width-1,$height-1,$bd_color);
        // 画干扰线
        for($i=0;$i<5;$i++){
            $font_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
            imagearc($im,mt_rand(-$width,$width),mt_rand(-$height,$height),mt_rand(30,$width*2),mt_rand(20,$height*2),mt_rand(0,360),mt_rand(0,360),$font_color);
        }
        // 画干扰点
        for($i=0;$i<300;$i++){
                $font_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
                imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$font_color);
        }
        // 画验证码
        $code = "";
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
        for ($i=0; $i<$num; $i++){
        	$text_color=imagecolorallocate($im, mt_rand(0,155), mt_rand(0,55), mt_rand(0,100));
                $tmp= $str[mt_rand(0, strlen($str)-1)];
                $code.=$tmp;
                 @imagefttext($im, $size , rand(-15,40), 13+21*$i, $size+10, $text_color, './font/code.otf',$tmp);
        }
        //存储验证码
        session_start();
        $_SESSION['code'] = $code;
        //展示图片
        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
    }
    captcha();


?>
