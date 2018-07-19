<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '/var/www/api/Extend/PHPMailer/PHPMailerAutoload.php';
// require_once 'D:\www\eesee\Extend\PHPMailer\PHPMailerAutoload.php';
class Checking extends Base_MobileController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('redeem_model');
        $this->load->model("receipt_model");
        $this->load->model("UserRedeem_model");
        $this->savePath = get_instance()->config->config['log_path_file'];
        // $this->savePath = 'D:/image/';
    }
    public function sendMsg(){
        $rs = [];
        $type = $this->post_input('type');
        if(empty($type)){
            $type = $this->get_input('type');
        }
        if($type == 'redeem'){
            $sendArr = array('1309895696@qq.com','liumeng@eeseetech.com','hanli@eeseetech.com');
            $title = '兑奖提现对账';
            $file = $this->redeemCheck();
            $mail_arr = $sendArr;
            $this->mailto($title,$file,$mail_arr);
        }
        elseif($type == 'order'){

        }
        elseif($type == 'all'){

        }
        
    }
    public function sendArr(){
        
    }
    public function redeemCheck(){
        //日对账功能
        set_time_limit(120);
        $rs = [];
        $yesterday = date('Y-m-d 00:00:00');
        $today = date('Y-m-d 00:00:00');
        $sql = "SELECT a.*,d.name,c.*,b.prize,b.stationId FROM ( SELECT user_id,province,city,time,SUM(prize) as total FROM tbl_redeem WHERE ret=1301 and time <'$yesterday' GROUP BY user_id ) as a LEFT JOIN tbl_user as b on a.user_id=b.id LEFT JOIN ( SELECT user_id as user_id2,SUM(amount)/100 as withdraw ,transfer_name FROM tbl_receipt_order WHERE `status`=1 AND transfer_time<'$yesterday' GROUP BY user_id ) as c on b.id=c.user_id2 LEFT JOIN tbl_club as d on b.id=d.user_id ";
        $info = $this->redeem_model->queryAll($sql);
        $sql2 = "SELECT SUM(prize) as total,user_id From tbl_redeem WHERE ret=1301 AND time>='{$today}' GROUP by user_id ";
        $sql3 = "SELECT SUM(amount)/100 as withdraw,user_id From tbl_receipt_order WHERE `status`=1 AND transfer_time>='{$today}' GROUP by user_id";
        $todayRedeemInfo = $this->redeem_model->queryAll($sql2);
        $todayWithdrawInfo = $this->redeem_model->queryAll($sql3);
        $temp = [];
        foreach ($todayRedeemInfo as $key => $value) {
            $temp[$value['user_id']] = $value['total'];
        }
        $todayRedeemInfo = $temp;
        $temp = [];
        foreach ($todayWithdrawInfo as $key => $value) {
            $temp[$value['user_id']] = $value['withdraw'];
        }
        $todayWithdrawInfo = $temp;
        $clanList = $this->UserRedeem_model->fetchAll(array('status'=>1));
        $temp = [];
        foreach ($clanList as $key => $value) {
            $temp[$value['user_id']] = $value['name'];
        }
        $clanList = $temp;
        $exportData = [];
        foreach ($info as $key => $value) {
            $user_id = $value['user_id'];
            if(empty($value['name'])){
                $value['name'] = $clanList[$user_id];
            }   
            $exportData[$key][0] = $value['user_id'];
            $exportData[$key][1] = $value['name'];
            $exportData[$key][2] = $value['province'];
            $exportData[$key][3] = $value['city'];
            // $exportData[$key][4] = $value['address'];
            $exportData[$key][5] = $value['stationId'];
            $exportData[$key][6] = $value['total'];
            $exportData[$key][7] =  empty($value['withdraw']) ? '0' : $value['withdraw'];
            $exportData[$key][8] = !empty($todayRedeemInfo[$user_id]) ? $todayRedeemInfo[$user_id] : '0';
            $exportData[$key][9] = !empty($todayWithdrawInfo[$user_id]) ? $todayWithdrawInfo[$user_id] : '0';
            $exportData[$key][10] = $value['prize'];
            $exportData[$key][11] = ($value['total'] + $exportData[$key][8] - $value['withdraw'] - $exportData[$key][9]  == $value['prize']) ? '正常' : '异常';
        }
        $Lists = $exportData;
        $date = date("Y_m_d", time());
        $fileName = "兑奖提现对账_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['savePath'] = $this->savePath;
        $data['lineWidth'] = array('A'=>15);
        $data['headArr'] = array(
            'A1'=>'店主ID',
            'B1'=>'店主姓名',
            'C1'=>'省份',
            'D1'=>'城市',
            'E1'=>'站点号',
            'F1'=>'截至昨日累计兑奖金额',
            'G1'=>'截至昨日累计提现金额',
            'H1'=>'今日兑奖金额',
            'I1'=>'今日提现金额',
            'J1'=>'账户剩余金额',
            'K1'=>'状态',
        );
        $data['bodyArr'] =  $Lists;
        $save_path = $this->dump_excels($data);
        return $save_path;
    }
    public function mailto($title, $file,$mail_arr)
    {
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
                // $objActSheet->getRowDimension($i)->setRowHeight(round(strlen($hineValue[4])/2));
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
        // if(array_key_exists('fileName',$fun_arr)){
        //     if(!is_string($fun_arr['fileName']))return;
        //     $fileName = iconv("utf-8", "gb2312", $fun_arr['fileName']);
        // }else{
        //     return;
        // }
         $fileName = $fun_arr['fileName'];
         $savePath = $fun_arr['savePath'];
       // $save_path = '/mnt/nas/www/log/xls/'.$fileName;
        $save_path = $savePath.$fileName;
         // $save_path = iconv("utf-8", "gb2312", $save_path);
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($save_path); 
        return $save_path;
    }
    

}
