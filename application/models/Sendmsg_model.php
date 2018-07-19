<?php
require_once "application/config/main_config.php";
class Sendmsg_model extends CI_Model
{
    private $memcache = '';
    private $token = '';
    public function __construct()
    {
        parent::__construct();


        $this->memcache = memcache_connect('localhost', 11211);
        $this->token = $this->memcache->get('token');
            //$this->test = $this->memcache->get('test');

    }

    public function passFtip($openid = '',$name,$phone,$shopname){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'DRt7Re6X6hFPg1p1Vm8va0OCGeIUyVTh4VlOuta4hQQ',
            'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的店铺申请已审核通过',"color"=>"#173177"),
                'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                'keyword2'=>array('value'=>$phone),
                'keyword2'=>array('value'=>$shopname),
                'remark'=>array('value'=>'你可以在店铺中申领票卷了',"color"=>"#173177")
            )
        ];



        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'DRt7Re6X6hFPg1p1Vm8va0OCGeIUyVTh4VlOuta4hQQ',
                'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'您的店铺申请已审核通过',"color"=>"#173177"),
                    'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                    'keyword2'=>array('value'=>$phone),
                    'keyword2'=>array('value'=>$shopname),
                    'remark'=>array('value'=>'你可以在店铺中申领票卷了',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;


    }
    public function refuseshop($openid = '',$content){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的店铺审核不通过！',"color"=>"#173177"),
                'keyword1'=>array('value'=>'零售店注册',"color"=>"#173177"),
                'keyword2'=>array('value'=>$content),
                'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];



        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'您的店铺审核不通过！',"color"=>"#173177"),
                    'keyword1'=>array('value'=>'零售店注册',"color"=>"#173177"),
                    'keyword2'=>array('value'=>$content),
                    'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;


    }
    public function passShop($openid = '',$name,$phone){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'YoB9xGNdUeDY1921HHSDG8qge5Lzv-1gclIqaCJbrrA',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的店铺申请已经通过客户经理审核，正在办理代销证，请耐心等待',"color"=>"#173177"),
                'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                'keyword2'=>array('value'=>$phone,"color"=>"#173177"),
                'keyword3'=>array('value'=>'普通店铺',"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'YoB9xGNdUeDY1921HHSDG8qge5Lzv-1gclIqaCJbrrA',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'您的店铺申请已经通过客户经理审核，正在办理代销证，请耐心等待',"color"=>"#173177"),
                    'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                    'keyword2'=>array('value'=>$phone,"color"=>"#173177"),
                    'keyword3'=>array('value'=>'普通店铺',"color"=>"#173177")
                    // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;


    }
    public function passShopByAdmin($openid = '',$name,$phone){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'YoB9xGNdUeDY1921HHSDG8qge5Lzv-1gclIqaCJbrrA',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'【北京中维】您的店铺申请已经通过审核，您现在可以登录微信进行店铺操作了。',"color"=>"#173177"),
                'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                'keyword2'=>array('value'=>$phone,"color"=>"#173177"),
                'keyword3'=>array('value'=>'普通店铺',"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'YoB9xGNdUeDY1921HHSDG8qge5Lzv-1gclIqaCJbrrA',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'【北京中维】您的店铺申请已经通过审核，您现在可以登录微信进行店铺操作了。',"color"=>"#173177"),
                    'keyword1'=>array('value'=>$name,"color"=>"#173177"),
                    'keyword2'=>array('value'=>$phone,"color"=>"#173177"),
                    'keyword3'=>array('value'=>'普通店铺',"color"=>"#173177")
                    // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;


    }
    public function passManager($openid = ''){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $time = date('Y-m-d H:i:s');
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的客户经理审核通过，获得公益分10分',"color"=>"#173177"),
                'keyword3'=>array('value'=>$time,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
            'touser'=>$openid,
            'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的客户经理审核通过，获得公益分10分',"color"=>"#173177"),
                'keyword3'=>array('value'=>$time,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;
    }
    public function refuseManager($openid = '',$content){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $time = date('Y-m-d H:i:s');
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'很抱歉，您的客户经理审核失败',"color"=>"#173177"),
                'keyword1'=>array('value'=>'客户经理认证',"color"=>"#173177"),
                'keyword2'=>array('value'=>$content,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'很抱歉，您的客户经理审核失败',"color"=>"#173177"),
                    'keyword1'=>array('value'=>'客户经理认证',"color"=>"#173177"),
                    'keyword2'=>array('value'=>$content,"color"=>"#173177")
                    // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;
    }
    public function passAreaManager($openid = ''){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $time = date('Y-m-d H:i:s');
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的市场经理审核通过',"color"=>"#173177"),
                'keyword3'=>array('value'=>$time,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
            'touser'=>$openid,
            'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的区域经理审核通过',"color"=>"#173177"),
                'keyword3'=>array('value'=>$time,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;
    }
    public function refuseAreaManager($openid = '',$content){

        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $time = date('Y-m-d H:i:s');
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'很抱歉，您的区域经理审核失败',"color"=>"#173177"),
                'keyword1'=>array('value'=>'区域经理认证',"color"=>"#173177"),
                'keyword2'=>array('value'=>$content,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'6PhM7kHnejAVrSq1zj5uMFD0Cqmt44sUNOUE0v-n6ss',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'很抱歉，您的区域经理审核失败',"color"=>"#173177"),
                    'keyword1'=>array('value'=>'区域经理认证',"color"=>"#173177"),
                    'keyword2'=>array('value'=>$content,"color"=>"#173177")
                    // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;
    }

    public function passBazaarManager($openid = ''){
        $data = [];
        if($openid == ''){
            $data['code'] = 1;
            $data['errormsg'] = 'openid不能为空!';
            echo json_encode($data);
            return;
        }
        if($this->token == ''){


            $this->memcache->set('token',$this->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
        }

        //   echo $this->token;die();
        //echo  Wxconfig::APPID;die();
        //echo Wxconfig::TID;die();
        $time = date('Y-m-d H:i:s');
        $this->load->model('sendmsg_model');
        $data=[
            'touser'=>$openid,
            'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
            // 'url'=>base_url().'mobile/pointmall?appId=31',
            'topcolor'=>"#FF0000",
            'data'=>array(
                'first'=>array('value'=>'您的区域经理审核通过',"color"=>"#173177"),
                'keyword3'=>array('value'=>$time,"color"=>"#173177")
                // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
            )
        ];

        $re = $this->curl_post_send_information($this->token,json_encode($data));
        $ret = json_decode($re);
        if($ret->errcode == 40001 || $ret->errcode == 41006){
            $this->memcache->set('token',$this->sendmsg_model->get_token(),0,7200);
            $this->token = $this->memcache->get('token');
            $data=[
                'touser'=>$openid,
                'template_id'=>'dhCpHIkIccC8UT1fA0ei1yR-TQCAHTuLJcQOY0AYSmU',
                // 'url'=>base_url().'mobile/pointmall?appId=31',
                'topcolor'=>"#FF0000",
                'data'=>array(
                    'first'=>array('value'=>'您的区域经理审核通过',"color"=>"#173177"),
                    'keyword3'=>array('value'=>$time,"color"=>"#173177")
                    // 'remark'=>array('value'=>'请您重新填写注册信息',"color"=>"#173177")
                )
            ];



            $re = $this->curl_post_send_information($this->token,json_encode($data));
            return $re;
        }
        return $re;
    }


    /*
     *获取access_token
     *有效时长7200s
     *
     */
    public function get_token(){
        $appid = MainConfig::APPID;
        $appsec = MainConfig::APPSECRET; 
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsec}";
        $chs = curl_init();                              //initialize curl handle
        curl_setopt($chs, CURLOPT_URL, $url);            //set the url
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
        // curl_setopt($chs, CURLOPT_GET, 1);
        $responses = curl_exec($chs);
        curl_close($chs);
        $return_info = json_decode($responses, true);
        return $return_info['access_token'];

    }


    /*
     *推送信息
     *
     *
     */

    public function curl_post_send_information( $token,$vars,$second=120,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        curl_setopt($ch,CURLOPT_URL,'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            curl_close($ch);
            return $error;
        }
    }

    /*
     *构造模板
     *$data=[
          'touser'=>用户openid,
          'template_id'=>模板id,
          'url'=>'链接url',
          'topcolor'=>"#FF0000",
          'data'=>array(
              'toName'=>array('value'=>内容1,"color"=>"#173177"),
              'gift'=>array('value'=>内容2<span style="font-family: Arial, Helvetica, sans-serif;">,"color"=>"#173177"),</span>
              'time'=>array('value'=>date("Y-m-d h:i:s",time()),"color"=>"#173177"),
              'remark'=>array('value'=>内容3,"color"=>"#173177")
          )
      ];
     *
     */


    /*
     *
    * 发送示例
    $result = curl_post_send_information(access_token,json_encode($data));
    响应示例
    {
    "errcode":0,
    "errmsg":"ok",
    "msgid":200228332
    }

    */



}