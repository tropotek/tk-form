<?php
try {
    $sitePath = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
    $siteUrl = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname($_SERVER['PHP_SELF']))))))));

    // Init Tk Framework
    include_once($sitePath . '/vendor/autoload.php');
    if (class_exists('\Ext\Config')) {
        $config = \Ext\Config::getInstance($sitePath, $siteUrl);
    }
    $config = \Tk\Config::getInstance($sitePath, $siteUrl);


    $session = $config->getSession();
    $request = $config->getRequest();

	$width  = 100;
	if ($request->exists('w')) {
	    $width = (int)$request->get('w');
	}
	$height =  40;
	if ($request->exists('h')) {
	    $height = (int)$request->get('h');
	}
	$length =   4;
	if ($request->exists('l')) {
	    $length = (int)$request->get('l');
	}
	if ($length < 4) {
	    $length = 4;
	}

    //$baseList = '23456789abcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	$baseList = '23456789abcdfghjkmnpqrstvwxyz';

    $sid = \Form\Field\Captcha\Basic::SID . $request->get('id');
	$code = "";
    //$counter = 0;
	if ($request->exists('r')) {
		$session->delete($sid);
	}
	if ($session->exists($sid)) {
		$code = $session->get($sid);
	}

    if (!$code) {
        for($i=0; $i<$length; $i++) {
            $actChar = substr($baseList, rand(0, strlen($baseList)-1), 1);
            $code .= strtolower($actChar);
        }
    }
    $session->set($sid, $code);


    generateImage($code, $width, $height);

    exit();
}  catch (\Exception $e) {
    vd("image.php: \n" . $e->__toString());
    exit();
}


function generateImage($text, $width=200, $height=50) {
    // constant values
    $backgroundSizeX = 800;
    $backgroundSizeY = 150;
    $sizeX = $width;
    $sizeY = $height;
    $fontFile = dirname(__FILE__) . "/verdana.ttf";
    $textLength = strlen($text);

    // generate random security values
    $backgroundOffsetX = rand(0, $backgroundSizeX - $sizeX - 1);
    $backgroundOffsetY = rand(0, $backgroundSizeY - $sizeY - 1);
    $angle = rand(-5, 5);
    $fontColorR = rand(0, 127);
    $fontColorG = rand(0, 127);
    $fontColorB = rand(0, 127);

    $fontSize = rand(16, 22);
    $textX = rand(0, (int)($sizeX - 0.9 * $textLength * $fontSize)); // these coefficients are empiric
    $textY = rand((int)(1.25 * $fontSize), (int)($sizeY - 0.2 * $fontSize)); // don't try to learn how they were taken out

    $gdInfoArray = gd_info();
    if (! $gdInfoArray['PNG Support']) {
        return false;
    }

    // create image with background
    $src_im = imagecreatefrompng(dirname(__FILE__) . "/background.png");
    if (function_exists('imagecreatetruecolor')) {
        // this is more qualitative function, but it doesn't exist in old GD
        $dst_im = imagecreatetruecolor($sizeX, $sizeY);
        $resizeResult = imagecopyresampled($dst_im, $src_im, 0, 0, $backgroundOffsetX, $backgroundOffsetY, $sizeX, $sizeY, $sizeX, $sizeY);
    } else {
        // this is for old GD versions
        $dst_im = imagecreate( $sizeX, $sizeY );
        $resizeResult = imagecopyresized($dst_im, $src_im, 0, 0, $backgroundOffsetX, $backgroundOffsetY, $sizeX, $sizeY, $sizeX, $sizeY);
    }

    if (! $resizeResult) {
        return false;
    }

    // write text on image
    if (!function_exists('imagettftext')) {
        return false;
    }
    $color = imagecolorallocate($dst_im, $fontColorR, $fontColorG, $fontColorB);
    imagettftext($dst_im, $fontSize, -$angle, $textX, $textY, $color, $fontFile, $text);

    // output header
    header("Content-Type: image/png");

    // output image
    imagepng($dst_im);

    // free memory
    imagedestroy($src_im);
    imagedestroy($dst_im);

    return true;
}