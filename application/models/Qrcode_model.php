<?php
class Qrcode_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
    }
    /*功能灵活导出Excel数据
     *@param 关键字传参 参数说明 array('fileName'=>'','lineWidth'=>array(),'headArr'=>array(),'bodyArr'=>array(),'fontColor'=>array())
     * fileName string 文件名字 后缀名 .xls 必须
     * lineWidth array 列间距 eg  array('A'=>'20','B'=>'30'.....) 非必需
     *
     * headArr array 首行标题 eg  array('序号',''.......)  非必需
     *bodyArr array 导出的数据 eg array(array('1','2','3',......))必须
     *
     * */
    public function dumpExcel()
    {
        $fun_arr = func_get_args()[0];
        if(empty($fun_arr)) return;


        //引入PHPExcel对象
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        //创建PHPExcel对象
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties();
        $objActSheet = $objPHPExcel->getActiveSheet();
        if(!isset($fun_arr['headArr']) || !isset($fun_arr['bodyArr'])) return;

        if(array_key_exists('headArr',$fun_arr)){
            if(!is_array($fun_arr['headArr']))return;
            $span = ord("A");
            foreach ($fun_arr['headArr'] as  $item){
                $j = chr($span);
                $objActSheet->setCellvalue($j.'1',$item);
                if(isset($fun_arr['lineWidth']) && array_key_exists($j,$fun_arr['lineWidth'])){
                    $objActSheet->getColumnDimension($j)->setWidth($fun_arr['lineWidth'][$j]);
                }else{
                    $objActSheet->getColumnDimension($j)->setWidth('20');
                }
                $span++;
            }
        }
        if(array_key_exists('bodyArr',$fun_arr)){
            if(!is_array($fun_arr['bodyArr']))return;
            $i = 2;
            foreach ($fun_arr['bodyArr'] as $hineValue){
                $span = ord("A");
                $objActSheet->getRowDimension($i)->setRowHeight(round(strlen($hineValue[4])/2));
                foreach ($hineValue as $lineValue){
                    $j = chr($span);
                    if(!empty($fun_arr['fontColor'])){

                        if(array_key_exists($j,$fun_arr['fontColor']) ){
                            $objRichText2 = new PHPExcel_RichText();
                            $objRichText2->createText("");
                            $objRed = $objRichText2->createTextRun($lineValue);
                            if(array_key_exists($lineValue,$fun_arr['fontColor'][$j])){
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );

                            }else{
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );
                            }
                            $objPHPExcel->getActiveSheet()->getCell($j .$i)->setValue($objRichText2);
                            $objPHPExcel->getActiveSheet()->getStyle($j .$i)->getAlignment()->setWrapText(true);
                        }else{
                            $objActSheet->setCellvalue($j.$i,$lineValue);
                        }
                    }else{
                        $objActSheet->setCellvalue($j.$i,$lineValue);
                    }
                    if($j == 'E'){
                        $objActSheet->getStyle('E'.$i)->getAlignment()->setWrapText(true);
                    }
                    $objActSheet->getStyle($j.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objActSheet->getStyle($j.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $span++;
                }
                $i++;
            }
        }

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); //清除缓冲区,避免乱码
        if(array_key_exists('fileName',$fun_arr)){
            if(!is_string($fun_arr['fileName']))return;
            $fileName = iconv("utf-8", "gb2312", $fun_arr['fileName']);
        }else{
            return;
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

        exit();
    }

    public function dump_excels(){
        $fun_arr = func_get_args()[0];
        if(empty($fun_arr)) return;


        //引入PHPExcel对象
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        //创建PHPExcel对象
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties();
        $objActSheet = $objPHPExcel->getActiveSheet();
        if(!isset($fun_arr['headArr']) || !isset($fun_arr['bodyArr'])) return;

        if(array_key_exists('headArr',$fun_arr)){
            if(!is_array($fun_arr['headArr']))return;
            $span = ord("A");
            foreach ($fun_arr['headArr'] as  $item){
                $j = chr($span);
                $objActSheet->setCellvalue($j.'1',$item);
                if(isset($fun_arr['lineWidth']) && array_key_exists($j,$fun_arr['lineWidth'])){
                    $objActSheet->getColumnDimension($j)->setWidth($fun_arr['lineWidth'][$j]);
                }else{
                    $objActSheet->getColumnDimension($j)->setWidth('20');
                }
                $span++;
            }
        }
        if(array_key_exists('bodyArr',$fun_arr)){
            if(!is_array($fun_arr['bodyArr']))return;
            $i = 2;
            foreach ($fun_arr['bodyArr'] as $hineValue){
                $span = ord("A");
                $width = round(strlen($hineValue[5])/2);
                $width = $width>100?$width:120;
                $objActSheet->getRowDimension($i)->setRowHeight($width);
                foreach ($hineValue as $lineValue){
                    $j = chr($span);
                    if(!empty($fun_arr['fontColor'])){

                        if(array_key_exists($j,$fun_arr['fontColor']) ){
                            $objRichText2 = new PHPExcel_RichText();
                            $objRichText2->createText("");
                            $objRed = $objRichText2->createTextRun($lineValue);
                            if(array_key_exists($lineValue,$fun_arr['fontColor'][$j])){
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );

                            }else{
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );
                            }
                            $objPHPExcel->getActiveSheet()->getCell($j .$i)->setValue($objRichText2);
                            $objPHPExcel->getActiveSheet()->getStyle($j .$i)->getAlignment()->setWrapText(true);
                        }else{
                            $objActSheet->setCellvalue($j.$i,$lineValue);
                        }
                    }else{
                        if($j == 'B' && $lineValue != ''){
                            $img = new PHPExcel_Worksheet_Drawing();
                            $img->setPath($lineValue);//写入图片路径
                            $img->setHeight(100);//写入图片高度
                            $img->setWidth(100);//写入图片宽度
                            $img->setOffsetX(20);//写入图片在指定格中的X坐标值
                            $img->setOffsetY(20);//写入图片在指定格中的Y坐标值
                            $img->setRotation(1);//设置旋转角度
                            $img->getShadow()->setVisible(true);//
                            $img->getShadow()->setDirection(50);//
                            $img->setCoordinates($j.$i);//设置图片所在表格位置
                            $img->setWorksheet($objActSheet);//把图片写到当前的表格中
                        }else{
                            $objActSheet->setCellvalue($j.$i,$lineValue);
                        }

                    }
                    if($j == 'H'){
                        $objActSheet->getStyle('H'.$i)->getAlignment()->setWrapText(true);
                    }
                    if($j == 'F'){
                        $objActSheet->getStyle('F'.$i)->getAlignment()->setWrapText(true);
                    }

                    $objActSheet->getStyle($j.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objActSheet->getStyle($j.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $span++;
                }
                $i++;
            }
        }

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); //清除缓冲区,避免乱码
        if(array_key_exists('fileName',$fun_arr)){
            if(!is_string($fun_arr['fileName']))return;
            $fileName = iconv("utf-8", "gb2312", $fun_arr['fileName']);
        }else{
            return;
        }

        $save_path = get_instance()->config->config['log_path_file'].$fileName;

        //  $save_path = iconv("utf-8", "gb2312", $save_path);
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($save_path);

    }

    public function mailto($title, $file,$mail_arr)
    {

        require_once '/var/www/api/Extend/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // 设置邮件使用SMTP
        $mail->Host = 'smtp.mxhichina.com';                     // 邮件服务器地址
        $mail->SMTPAuth = true;                               // 启用SMTP身份验证
        $mail->CharSet = "UTF-8";                             // 设置邮件编码
        $mail->setLanguage('zh_cn');                          // 设置错误中文提示
        $mail->Username = 'eesee@eeseetech.com';              // SMTP 用户名，即个人的邮箱地址
        $mail->Password = 'A82i#39szy';                        // SMTP 密码，即个人的邮箱密码
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        //$mail->SMTPSecure = 'tls';                            // 设置启用加密，注意：必须打开 php_openssl 模块
        $mail->Priority = 3;                                  // 设置邮件优先级 1：高, 3：正常（默认）, 5：低
        $mail->From = 'eesee@eeseetech.com';                 // 发件人邮箱地址
        $mail->FromName = '上海意视信息科技有限公司';                     // 发件人名称


        // 添加接受者
        foreach($mail_arr as $one_mail){
            $mail->addAddress($one_mail);
        }

        // $mail->addAddress('wangxinmei@eeseetech.com', 'wangxinmei');     // 添加接受者
        //$mail->addAddress('ellen@example.com');               // 添加多个接受者
        //$mail->addReplyTo('info@example.com', 'Information'); // 添加回复者
        //$mail->addCC('mail2@sina.com');                // 添加抄送人
        //$mail->addCC('mail3@qq.com');                     // 添加多个抄送人
        //$mail->ConfirmReadingTo = 'liruxing@wanzhao.com';     // 添加发送回执邮件地址，即当收件人打开邮件后，会询问是否发生回执
        //$mail->addBCC('mail4@qq.com');                    // 添加密送者，Mail Header不会显示密送者信息
        $mail->WordWrap = 50;                                 // 设置自动换行50个字符

        $mail->addAttachment($file,
            substr($file, strrpos($file, "/")+1, strlen($file)));      // "application/zip"
        $mail->isHTML(true);                                  // 设置邮件格式为HTML
        $mail->Subject = $title;
        $mail->Body    = $title;
        $mail->AltBody = $title;

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }

        echo 'Message has been sent';
    }




}
