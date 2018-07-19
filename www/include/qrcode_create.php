<?php
    include('phpqrcode.php');

    function qr_code_create($content,                           //  text string to encode
                            $width_height = 100,                     //  (optional) qrcode image width and height
                            $outfile=null, // 	(optional) output file name, if false outputs to browser with required headers
                            $margin = 2,                             //  (optional) code margin (silent zone) in 'virtual' pixels
                            $level = QR_ECLEVEL_L,                   // 	(optional) error correction level QR_ECLEVEL_L, QR_ECLEVEL_M, QR_ECLEVEL_Q or QR_ECLEVEL_H
                            $saveandprint = false,                   //  (optional) if true code is outputed to browser and saved to file, otherwise only saved to file. It is effective only if $outfile is specified.
                            $size = 7                                //	(optional) pixel size, multiplier for each 'virtual' pixel
    )
    {
        if(!defined('IMAGE_WIDTH')) {
            define('IMAGE_WIDTH', $width_height);
            define('IMAGE_HEIGHT', $width_height);
        }
        if($outfile==null)
            $outfile = '/var/www/yan.eeseetech.cn/upload/image/'.date('Ymd').'/'.time().gen_rand_num().'.png';
        $image_file = '/var/www/yan.eeseetech.cn/upload/image/'.date('Ymd');

        if(!file_exists($image_file)){
            mkdir($image_file,0777,true);
        }
        QRcode::png($content,$outfile,$level,$size,$margin,$saveandprint);
        return $outfile;
    }
