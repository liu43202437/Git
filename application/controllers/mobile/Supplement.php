<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Supplement extends Base_MobileController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model('Area_manager_model');
        $this->load->model('Bazaar_manager_model');
        $this->load->model('ticket_model'); 
        $this->load->model('userCredits_model');
    }
    public function Supple(){
        //取所有订单
        $userStr = '201112,201115,201149,201188,201195,201205,201203,201280,201277,201305,201322,201310,201328,201325,201336,201340,201344,201355,201444,201420,201402,201400,201452,201446,201423,201458,201353,201494,201496,201473,201497,201517,201519,201525,201528,201530,201534,201440,201544,201545,201549,201557,201520,201564,201579,201591,201587,201504,201598,201451,201612,201615,201619,201536,201347,201655,201266,201627,201648,201660,201629,201580,201511,201383,201419,201721,201449,201719,201567,201718,201703,201724,201732,201726,201740,201748,201800,201806,201808,201802,201846,201850,201854,201856,201609,201078,201866,201920,201922,201925,201289,201460,201932,201934,201652,201333,201278';
        $sql = "select * from tbl_ticket_order where user_id in ({$userStr}) and order_status=2";
        $orderInfo = $this->user_model->queryAll($sql);
        //取所有店铺信息
        $clubInfo = $this->club_model->fetchAll();
        $temp = [];
        foreach ($clubInfo as $key => $value) {
            $temp[$value['user_id']] = $value;
        }
        $clubInfo = $temp;
        //取所有客户经理信息
        $managerInfo = $this->manager_model->fetchAll('tbl_consumer');
        $temp = [];
        foreach ($managerInfo as $key => $value) {
            $temp[$value['phone']] = $value;
        }
        $managerInfo = $temp;
        //取所有市场经理信息
        $areaManagerInfo = $this->Area_manager_model->fetchAll('tbl_area_manager');
        $temp = [];
        foreach ($areaManagerInfo as $key => $value) {
            $temp[$value['phone']] = $value;
        }
        $areaManagerInfo = $temp;
        //取所有市场经理信息
        $BazaarInfo = $this->Bazaar_manager_model->fetchAll('tbl_bazaar_manager');
        $temp = [];
        foreach ($BazaarInfo as $key => $value) {
            $temp[$value['phone']] = $value;
        }
        $BazaarInfo = $temp;
        //处理零售店信息
        $insertData = [];
        foreach ($orderInfo as $key => $value) {
            $insertData[$key]['user_id'] = $value['user_id'];
            $insertData[$key]['trade_no'] = $value['trade_no'];
            $insertData[$key]['name'] = $value['name'];
            $insertData[$key]['update_time'] = strtotime($value['update_date']);
            $insertData[$key]['create_date'] = $value['create_date'];
            $insertData[$key]['address'] = $value['area'].$value['address'];
            $insertData[$key]['credits'] = 30;
            $insertData[$key]['status'] = 1;
            $insertData[$key]['type'] = 1;
            $insertData[$key]['add_time'] = time();
        }
        if($insertData){
            $flag = $this->userCredits_model->insertBatch($insertData);
        }
        //处理客户经理信息
        $insertData = [];
        foreach ($orderInfo as $key => $value) {
            $user_id = $value['user_id'];
            $club = $clubInfo[$user_id];
            $managerPhone = $club['manager_id'];
            if(array_key_exists($managerPhone, $managerInfo)){
                $insertData[$key]['user_id'] = $managerInfo[$managerPhone]['consumer_userid'];
                $insertData[$key]['trade_no'] = $value['trade_no'];
                $insertData[$key]['name'] = $value['name'];
                $insertData[$key]['update_time'] = strtotime($value['update_date']);
                $insertData[$key]['create_date'] = $value['create_date'];
                $insertData[$key]['address'] = $value['area'].$value['address'];
                $insertData[$key]['credits'] = 15;
                $insertData[$key]['status'] = 1;
                $insertData[$key]['type'] = 2;
                $insertData[$key]['add_time'] = time();
            }
        }
        if($insertData){
            $flag = $this->userCredits_model->insertBatch($insertData);
        }
        //处理市场经理信息
        $insertData = [];
        foreach ($orderInfo as $key => $value) {
            $user_id = $value['user_id'];
            $club = $clubInfo[$user_id];
            $managerPhone = $club['manager_id'];
            if(array_key_exists($managerPhone, $managerInfo)){
                $areaManagerPhone = $managerInfo[$managerPhone]['area_managerid'];
                if(array_key_exists($areaManagerPhone, $areaManagerInfo)){
                    $insertData[$key]['user_id'] = $areaManagerInfo[$areaManagerPhone]['user_id'];
                    $insertData[$key]['trade_no'] = $value['trade_no'];
                    $insertData[$key]['name'] = $managerInfo[$managerPhone]['name'];
                    $insertData[$key]['update_time'] = strtotime($value['update_date']);
                    $insertData[$key]['create_date'] = $value['create_date'];
                    $insertData[$key]['address'] = $value['area'].$value['address'];
                    $insertData[$key]['credits'] = 10;
                    $insertData[$key]['status'] = 1;
                    $insertData[$key]['type'] = 4;
                    $insertData[$key]['add_time'] = time();
                }
            }
        }
        if($insertData){
            $flag = $this->userCredits_model->insertBatch($insertData);
        }
        //处理区域经理信息
        $insertData = [];
        foreach ($orderInfo as $key => $value) {
            $user_id = $value['user_id'];
            $club = $clubInfo[$user_id];
            $managerPhone = $club['manager_id'];
            if(array_key_exists($managerPhone, $managerInfo)){
                $areaManagerPhone = $managerInfo[$managerPhone]['area_managerid'];
                if(array_key_exists($areaManagerPhone, $areaManagerInfo)){
                    $BazaarPhone = $areaManagerInfo[$areaManagerPhone]['bazaar_phone'];
                    if(array_key_exists($BazaarPhone, $BazaarInfo)){
                        $insertData[$key]['user_id'] = $BazaarInfo[$BazaarPhone]['user_id'];
                        $insertData[$key]['trade_no'] = $value['trade_no'];
                        $insertData[$key]['name'] = $areaManagerInfo[$areaManagerPhone]['name'];
                        $insertData[$key]['update_time'] = strtotime($value['update_date']);
                        $insertData[$key]['create_date'] = $value['create_date'];
                        $insertData[$key]['address'] = $value['area'].$value['address'];
                        $insertData[$key]['credits'] = 10;
                        $insertData[$key]['status'] = 1;
                        $insertData[$key]['type'] = 5;
                        $insertData[$key]['add_time'] = time();
                    }
                }
            }
        }
        if($insertData){
            $flag = $this->userCredits_model->insertBatch($insertData);
        }
        
    }
    public function test(){
        set_time_limit(0);
        $sql ="SELECT user_id FROM `tbl_club` WHERE name in ('王明林','何丽丽','黄克辉','甘敬华','滕永治','覃光金','何夏','李奔宾','陈海燕','黄秋花','何柳瑜','蒋芳明','阳晓宾','罗秋红','雷玉兰')";
        $info = $this->user_model->queryAll($sql);
        $str ='';
        foreach ($info as $key => $value) {
            $str .= $value['user_id'].",";
        }
        // var_dump($str);
        $sql = "select * from tbl_session where user_id in (201319,201535,201550,201596,201650,201582,201651,201578,201575,201805,201892,202067,201144,202246,202290,202425,202427,202281,202484,202358,202710,202719,202797,202908,201354,202909,202911,202916,202917,202920,202923,202925,202921,202933,202935,202943,202905,202955,202957,202963,202930,202754,202755,202761,202765,202099,202762,202763,202769,202770,202771,202779,202775,202781,202777,202764,202785,202786,202780,202792,202788,202795,202796,202793,202799,202801,202800,202794,202807,202806,202791,202798,202809,202430,202810,202811,202814,202815,202816,202818,202819,202820,202767,202821,202822,202823,202813,202828,202829,202831,202838,202830,202832,202852,202850,202851,202854,202858,202849,202860,202855,202864,202863,202868,202869,202866,202862,202872,202875,202876,202783,202878,202817,202865,202827,202608,202488,202291,201938,202882,202789,202853,202885,202886,202890,202835,202887,202893,202895,202861,202894,202857,202898,202900,202797,202908) ";
        $sessionList = $this->user_model->queryAll($sql);
        // echo $str;
        // var_dump($sessionList);die;
        $temp = [];
        foreach ($sessionList as $key => $value) {
            $temp[] = $value['session_id'];
        }
        // var_dump($temp);
        $url = "http://yan.bjzwhz.cn/mobile/wechat/apply_welfare_ticket";
        foreach ($temp as $key => $value) {
            // $postData['sid'] = $value;
            $postData['sid'] = 'bce32e85c56acbe6f68344fd42744898';
            $postData['data'] = '[{"id":"32","num":2,"name":"美梦成真"},{"id":"33","num":2,"name":"争分夺秒"},{"id":"34","num":1,"name":"富贵有余2"},{"id":"36","num":2,"name":"北京印象"},{"id":"51","num":2,"name":"戊戌狗"}]';
            $res = $this->post($url,$postData);
            // var_dump($postData);
            var_dump($res);
            die;
        }
        // var_dump($sessionList);
    }
    public function post($url,$postData){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1); //设置为post请求类型
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);  //设置具体的post数据  文件上传
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //跳过https验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //跳过https验证 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  //超时时间
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function cl(){
        $str = "唐佳思,吴德胜,刘春林,覃梅,戴一杰,周礼勇,吕家燕,曾凤花,田蓉芳,欧启盛,唐浪阳,梁剑钊,谢国恩,黄达吉,江其胜,蔡春海,韦伟金,谢永辉,王家红,张丽,李焕雄,韦智申,周培挺,陆月华,邓园园,邓兰香,韩彦峰,黄文东,邓振旋,陈少莲,谢彩虹,周刚,郭银萍,陈艺文,谢德双,韦丽云,陈品良,玉永华,谭伶香,王明林,李如翠,阮富汉,莫蒲葵,钟文飞,蒙美苗,韦四美,陈礼明,方冰,潘梅丹,李建强,滕武,丘英,兰佩琴,陆益云,黎念香,陈祯兰,廖伟南,周永兴,卢裕玲,黄芳,许氏叶,林万荣,覃继业,陈华宏,钟海丽,张领绍,许中兵,杨春燕,奚发文,陆永盟,赵石林,余玖凤,李子木,陆金生,黄守利,梁善干,雷康,张金虎,韦艳芬,范千,卜群敏,苏晓华,黄世银,赵子拾,闫丹丹,章丽琴,曾令勤,黄颇梅,蒙小秋,黄红,梁尚喜,张明,刘忠铭,谢建萍,林春来,莫忠珍,吴兰萍,谭卫克,张大卫,蓝继东,莫国炎,陆春桃,路喜兰,徐秋兰,王冬振,温波,吴楚新,龙丽萍,李秀叶,杨绍辉,黄腾兴,苏丽,张荣深,龚林花,李秋林,刘春燕,詹启荣,向秀芳,韦华,王海云,赖静华,梁芳,蔡苏秦,李丹丹,伍勇政,陈雪梅,杨楚珠,梁军,庞栋,阳志香,何益华,韦日红,陈文滔,申社林,王俏凡,杨朴升,吴大达,莫灿,雷雯,刘文典,黄丽娜,韦世韦华,刘文哲,黄祥新,沈洁,郭丽华,苏连肖,龙赐喜,吴东,林仕伟,雷玉兰,罗秋红,阳晓宾,梁晓,熊智勤,黄浩样,韦婷,邓孟群,刘迎喜,梁金,江伟敷,刘志辉,蓝宏胜,唐倩,黄丽荣,刘浪,农军,陆光义,付庆华,覃雪芬,凌召桂,钟国坚,蒋芳明,韦荣婷,郑俊杰,崔丽花,陈世昌,雷春樱,何柳瑜,黄槐喜,马海敏,滕次带,李玉兰,陈夫,杨春媚,张德良,滕艳婷,黄秋花,彭权,周磊,陈海燕,李奔宾,陆志曾,林志刚,何金艳,黄建明,韦长先,李贵初,蔡骏,罗伯带,尹中华,梁繁桂,余焕,张兰花,黄鹏,黄国良,黄觉来,邹崇阶,袁晓羽,韦鲜英,黄雪梅,陈冠奇,黄振新,阙宁,李卫初,刘奔,蓝梦宇,黄俏灵,肖瑞平,莫明星,陈爱春,何夏,黄伟,翟才坚,朱晓敏,李斌榆,唐国艳,玉荣还,卢儒福,李青秀,黄惠宁,黄常贝,梁文,李单,黄秀团,韦菊周,韦矿,龚毅卿,黄华东,刘庆娣,陆烽,覃汉柳,李荣延,王静,陆愈炫,凌鉴仲,玉锦弟,陆鸣林,卢晓丹,温艳明,朱明芳,覃文华,李庆德,熊荣华,陆翠林,向觉庆,葛少华,周莲沙,陆红燕,杨翠才,黄月桂,韦之秋,杨振斌,黄国秀,黄宝府,马青才,黄英德,农史关,李丽丽,梁开献,潘振帮,张绍梅,何桂满,邓小运,覃毅,张德均,覃光金,覃姣姣,张瑛,吴世友,管飞龙,韦岸江,滕丕宣,肖小妹,陈芬,周新建,许路叶,谭欢莉,申雪珍,钟明坤,张海玲,杨芝庆,滕永治,谢声龙,龙海军,马小玲,成丽红,梁愈杰,凌斌,成天晴,张德沛,马胜升,赖雪珊,余金华,周祖茂,张启平,苏积卷,梁丽娟,方安桥,屈进明,韦力鹏,农乾巍,韦付兴,李毕崇,曹秀碧,黎承毅,梁娴,李桂先,刘伟健,苏红春,唐秀锦,熊发,杨茜,周顺,雷德菁,欧楠楠,梁清安,沈雪玲,丘春游,王大破,彭燕,邱和勇,张我行,李雪湖,黄婷,阮茂光,覃才,孙朝杨,谢振涛,陆晖春,彭迪,李雄,王敏英,李贵元,庞炜,陈美玉,徐军,严广明,黄金泉,周玲玲,于大宇,李海玮,赖耀宸,钟金东,吴秀芳,黄凤玲,韦小青,詹兆合,磨美双,刘丽环,廖旭勋,韦世琼,罗艳萍,石堂功,刘君艳,尹雪英,黄好缨,甘敬华,王文益,陈秀桥,苏泰臣,潘会连,李春兰,钱若海,潘利柏,磨练虎,陆清丽,陶浩海,潘艳兰,叶宁,韦菊元,罗宁,罗宁,欧桂梅,陈桂枝,韦继颜,苏焕顺,韦兰庭,黄永凤,潘永淑,阮三军,韦情,潘文军,廖若顺,覃大彪,军武,易志远,蔡云霄,蒋冬霞,罗杰,李燕,蔡丽纯,何济朝,李春香,陈艺文,蓝军,韦娟,陈大彪,朱汝霞,左瑞华,黄海英,粟丽惠,林海南,陈文虎,朱其锋,黄胤潮,陈文灿,李珍红,黄翠娟,苏雁,蓝宏彬,林霞,罗喜玲,覃瑾,张小灿,农卫红,王春,余新安,李达中,吴应龙,苏升官,罗荣记,陆嘉凌,李奕豪,邹伟国,李方坚,潘桂芳,林华,李育芳,许冲,杨雪华,韦民康,兰存,李绍梅,李红,庞震,丁宽,滕文荣,薛作君,王水秀,游焕鹏,黄铭基,蒙丽霞,党昱祺,孙海波,李惠琼,杨开茂,吴绍明,林崇伟,庞燕燕,周亮,蒙恩师,黄荣沙,陆健林,王进军,王龙军,樊远锋,覃金平,邓美琴,冯善雅,袁里平,韦玉流,庄创生,董永泉,石小云,覃永宁,谭玉婵,钟文通,陈秀娣,许高清,韦钟盛,赵丽萍,陈超燕,唐平格,邹培鸿,黄克辉,余春梅,欧维雄,郑少涛,陈涛,韦宝莲,黄英同,邵中旗,马海燕,王庆林,吕振华,钟爱萍,孙植团,蒙诚彪,李卓伟,林德容,李锦威,韦英丽,覃秋群,何丽丽,黄祥平,林青洁,谢翠琴,方思镇,梁燕梅,文娟,柯如青,韦少芬,招柳妹,黄铭杏,刘焕玲,廖强忠,陆华锋,卢直来,李惠明,赖政昆,管仕华,梁玉华,罗景,何朝东,甘瑞森,周姗,马言言,余显英,田娟,周丽,李金竹,蓝小琼,伍尚龙,曾甜春,刘碧球,滕居范,许创荣,汪辉平,何慧全,颜世立,班上斌,李红银,李玲带,杨植富,滕雪艳,何日强,颜世坚,玉雨佳,徐秋波,杜彪,周舟,宁燕英,余华坚";
        $userArr = explode(',', $str);
        // $userArr = array_unique($userArr);
        // var_dump($userArr);die;
        $sql = "select * from tbl_club";
        $clubArr = $this->user_model->queryAll($sql);
        $allUserArr = [];
        foreach ($clubArr as $key => $value) {
            $allUserArr[] = trim($value['name']);
        }
        $rs = [];
        foreach ($userArr as $key => $value) {
            if(!in_array($value, $allUserArr)){
                $rs[] = $value;
            }
        }
        var_dump($rs);
    }
    public function test2(){
        $str = "201319,201535,201550,201596,201650,201582,201651,201578,201575,201805,201892,202067,201144,202246,202290,202425,202427,202281,202484,202358,202710,202719,202797,202908,201354,202909,202911,202916,202917,202920,202923,202925,202921,202933,202935,202943,202905,202955,202957,202963,202930,202754,202755,202761,202765,202099,202762,202763,202769,202770,202771,202779,202775,202781,202777,202764,202785,202786,202780,202792,202788,202795,202796,202793,202799,202801,202800,202794,202807,202806,202791,202798,202809,202430,202810,202811,202814,202815,202816,202818,202819,202820,202767,202821,202822,202823,202813,202828,202829,202831,202838,202830,202832,202852,202850,202851,202854,202858,202849,202860,202855,202864,202863,202868,202869,202866,202862,202872,202875,202873,202876,202783,202878,202817,202865,202827,202608,202488,202291,201938,202882,202789,202853,202885,202886,202890,202835,202887,202893,202895,202861,202894,202857,202898,202900,202797,202908";
        $user_idArr = explode(',', $str);
        // $user_idArr = array_unique($user_idArr);
        // var_dump($user_idArr);
        $temp = [];
        $rs =[];
        foreach ($user_idArr as $key => $value) {
            if(!in_array($value, $temp)){
                $temp[] = $value;
            }
            else{
                $rs[] = $value;
            }
        }
        var_dump($rs);
    }
}
