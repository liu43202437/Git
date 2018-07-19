<?php
require_once('analytics.php');

function getIPAddress()
{
	$analytics = new Analytics();
	return $analytics->Getip();
}

function getCountryFromIP($ip) 
{
    $analytics = new Analytics();
    $ipadds = $analytics->Getaddress($ip);

    if (empty($ipadds[0])) {
        return '中国';
    }

    $country = htmlentities($ipadds[0][0], ENT_COMPAT, "UTF-8");
    return $country;
}

function getProvinceFromIP($ip) 
{
    $analytics = new Analytics();
    $ipadds = $analytics->Getaddress($ip);

    if (empty($ipadds[0]) || empty($ipadds[0][1])) {
        return '辽宁';
    }

    $province = htmlentities($ipadds[0][1], ENT_COMPAT, "UTF-8");
    return $province;
}

function getCityFromIP($ip)
{
    $analytics = new Analytics();
    $ipadds = $analytics->Getaddress($ip);

    if (empty($ipadds[0]) || empty($ipadds[0][2])) {
        return '沈阳';
    }

    $city= htmlentities($ipadds[0][2], ENT_COMPAT, "UTF-8");
    return $city;
}

function getValueByDefault($value, $default) 
{
    if (!is_array($value)) {
        $whiteList = array();
        if (is_array($default)) {
            $whiteList = $default;
            $default = isset($default[0]) ? $default[0] : $default;
        } elseif ($value == '') {
            return $default;
        }

        if (is_float($default)) {
            $value = floatval($value);
        } elseif (is_int($default)) {
            $value = intval($value);
        } elseif (is_array($default)) {
            if ($value == '') {
                return $default;
            }
            $value = (array)$value;
        } else {
            $value = trim($value);
        }

        if ($whiteList && !in_array($value, $whiteList)) {
            $value = $default;
        }

    } else {
        foreach ($value as $key => $val) {
            $t = isset($default[$key]) ? $default[$key] : '';
            $value[$key] = getValueByDefault($value[$key], $t);
        }
        if (is_array($default)) {
            $value += $default;
        }
    }

    return $value;
}

// get chinese string
function GBK($str) 
{
    return iconv('utf-8','gb2312//TRANSLIT//IGNORE', $str);
    //return iconv('utf-8', 'gb2312', $str);
}

/* backup the db OR just a table */
function backup_tables($host, $user, $pass, $name, $tables = '*') 
{
    $link = mysql_connect($host,$user,$pass);
    mysql_select_db($name,$link);
    //get all of the tables
    if($tables == '*') {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    $return = '';
    //cycle through
    foreach($tables as $table) {
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);

        $return.= 'DROP TABLE '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while($row = mysql_fetch_row($result)) {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace('/\n/', '\\n', $row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j < ($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }
    //save file
    $handle = fopen('d-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
    fwrite($handle,$return);
    fclose($handle);
}

/* ============================== delete_directory ============================== */
function delete_directory($dirname)
{
    $dir_handle = "";
    if (is_dir($dirname))
        $dir_handle = opendir($dirname);
    if (!$dir_handle)
        return false;
    while($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname."/".$file))
                unlink($dirname."/".$file);
            else
                delete_directory($dirname.'/'.$file);
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

/* ============================== LOG ============================== */
function log_info($data)
{
    $data = "[T-ONE][IP:".$_SERVER['REMOTE_ADDR']."]".$data;
    log_message('error', $data);
}

/* ============================== ERROR ============================== */
function log_error($file, $func, $line, $disp = "")
{
    $data = "[T-ONE][IP:".$_SERVER['REMOTE_ADDR']."]\tThe error is as follows.\n";
    $data = $data."[FilePath:".$file."],";
    $data = $data."[Line:".$line."],";
    $data = $data."[Function:".$func."],";
    $data = $data."[Description:".$disp."]";

    log_message('error', $data);
}

/* ============================== JSON CAPSULE ============================== */
function json_capsule($arr)
{
	$json_result = "";
    $data = json_encode($arr);
    //$json_result = "{\"result\":[";
    $json_result .= $data;
    //$json_result .= "]}";
    //log_info($json_result);

    return $json_result;
}

/* ============================== GO PAGE ============================== */
function gopage($url)
{
    //echo "<script>document.location.replace('".$url."'); </script>";
    header("Location: ".$url);
}

function show_errorpage(){
    gopage(base_url() . "pagenotfound");
}

function show_noauth(){
    gopage(base_url() . "noauth");
}


/* ============================== String Utils ========================== */
function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function ellipseStr($str, $length, $suffix = " ...")
{
	if (mb_strlen($str) > $length) {
		return mb_substr($str, 0, $length) . $suffix;
	}
	return $str;
}

/*
|--------------------------------------------------------------------------
| Date/Time Management Functions
|--------------------------------------------------------------------------
|
| Define Date/Time management functions.
|
*/
date_default_timezone_set("Asia/Hong_Kong");

function now()
{
    return date("Y-m-d H:i:s");
}
function nownum()
{
    return date("YmdHis");
}
function nowdate()
{
    return date("Y-m-d");
}
function nowdate2()
{
    return date("Ymd");
}
function nowtime()
{
    return date("H:i:s");
}
function nowtime2()
{
    return date("His");
}
function nowYearMonth()
{
    return date("Y-m");
}
function nowyear()
{
    return date("Y");
}
function nowmonth()
{
    return date("n");
}
function nowday()
{
    return date("j");
}
function today_start()
{
    return date("Y-m-d 00:00:00");
}
function today_end()
{
    return date("Y-m-d 23:59:59");
}
function day_before($d, $from="")
{
    if ($from=="") $time = time();
    else $time = strtotime($from);

    return date("Y-m-d", $time-$d*24*60*60);
}
function day_after($d, $from="")
{
    if ($from=="") $time = time();
    else $time = strtotime($from);

    return date("Y-m-d", $time+$d*24*60*60);
}
function time_before($d, $from="")
{
    if ($from=="") $time = time();
    else $time = strtotime($from);

    return date("Y-m-d H:i:s", $time-$d*24*60*60);
}
function time_after($d, $from="")
{
    if ($from=="") $time = time();
    else $time = strtotime($from);

    return date("Y-m-d H:i:s", $time+$d*24*60*60);
}
function datetime_start()
{
    return date("Y-m-d H:i:s", 0);
}
function day_start()
{
    return date("Y-m-d", 0);
}
function t2d($date)
{
    return date("Y-m-d", strtotime($date));
}
function t2t($date)
{
    return date("H:i:s", strtotime($date));
}
function t2dt($time)
{
    return date("Y-m-d H:i:s", $time);
}
function d2bt($date)
{
    return t2d($date)." 00:00:00";
}
function d2et($date)
{
    return t2d($date)." 23:59:59";
}
function d2y($date)
{
    return date("Y", strtotime($date));
}
function d2dtns($date)
{
    return date("Y-m-d H:i", strtotime($date));
}

function firstOfMonth() 
{
	return date("m/d/Y", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
}

function lastOfMonth() 
{
	return date("m/d/Y", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
}

function month_before($m)
{
    $str = "-".$m." month";
    return date('Y-m-1', strtotime($str));
}
function prev_month_start()
{
    return date('Y-m-1', strtotime("-1 month"));
}
function prev_month_end()
{
    $year = date('Y', strtotime("-1 month"));
    $month = date('m', strtotime("-1 month"));
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    return $year . "-" . $month . "-" . $days;
}
function prev_week_start()
{
    $prev_week_end = prev_week_end();
    return date('Y-m-d', strtotime('-6 days', strtotime($prev_week_end)));
}

if (!defined('CAL_GREGORIAN'))
    define('CAL_GREGORIAN', 1); 

if (!function_exists('cal_days_in_month'))
{
    function cal_days_in_month($calendar, $month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }
} 


function month_start_date($date = null)
{
	if ($date = null) {
		$date = now(); 
	}
    return date('Y-m-1', strtotime($date));
}

function month_end_date($date = null)
{
	if ($date = null) {
		$date = now(); 
	}
    return date('Y-m-1', strtotime($date));
}

function month_start_time($date = null)
{
	if ($date == null) {
		$date = nowYearMonth();
	}
    return date('Y-m-1 00:00:00', strtotime($date));
}
function month_end_time($date = null)
{
	if ($date == null) {
		$date = nowYearMonth();
	}
	$year = date('Y', strtotime($date));
    $month = date('m', strtotime($date));
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    return date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . $days));
}


function prev_week_end()
{
    return date('Y-m-d', strtotime('this Saturday -1 week'));
}
function stime_type($timeType)
{
    $stime = '';
    if($timeType == 'today') {
        $stime = today_start();                 // today 00:00
    } else if($timeType == 'yesterday') {
        $stime = date("Y-m-d 00:00:00", time() - 24*60*60); // yesterday 00:00
    } else if($timeType == 'week') {
        $stime = date("Y-m-d 00:00:00", time() - 7*24*60*60); // week before 00:00
		//$stime = d2bt(day_before(7, now()));
    } else if($timeType == 'month') {
        $stime = date("Y-m-d 00:00:00", time() - 30*24*60*60); // month before 00:00
        //$stime = d2bt(day_before(30, now()));
    } else {
        $stime = today_start();
    }
    return $stime;
}
function etime_type($timeType)
{
    $etime = '';
    if($timeType == 'today') {
        $etime = today_end();           // today 23:59:59
	} else {
        $etime = date("Y-m-d 23:59:59", time() - 24*60*60); // yesterday 23:59:59
		// $etime = d2et(day_before(1, now()));
	}
    return $etime;
}


function timespan_format($date, $from = null)
{
	if ($from == null) {
		$from = time();
	}
	$seconds = $from - strtotime($date);
	$minutes = round($seconds / 60);
	$hours = round($seconds / 3600);
	$rslt = "";
	if ($seconds < 60) {
		$rslt = '1分钟前';
	} else if ($minutes < 60) {
		$rslt = $minutes . '分钟前';
	} else if ($hours < 24) {
		$rslt = $hours . '小时前';
	} else {
		$rslt = $date;
	}
	return $rslt; 
}

/* =================== Calculate Distance Between GPS Position ==================== */
function getDistance($lng1, $lat1, $lng2, $lat2)
{  
	$earthRadius = 6367000; //approximate radius of earth in meters  

	// Convert these degrees to radians to work with the formula 
	$lat1 = (floatval($lat1) * pi() ) / 180;  
	$lng1 = (floatval($lng1) * pi() ) / 180;  

	$lat2 = (floatval($lat2) * pi() ) / 180;  
	$lng2 = (floatval($lng2) * pi() ) / 180;  

	// calculate the distance 
	$calcLongitude = $lng2 - $lng1;  
	$calcLatitude = $lat2 - $lat1;  
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);    
	$stepTwo = 2 * asin(min(1, sqrt($stepOne)));  
	$calculatedDistance = $earthRadius * $stepTwo;  

	return $calculatedDistance;
	//return round($calculatedDistance);  
}

/* =================== Get Address Info from GPS Position ==================== */
function getAddress($ak, $lng, $lat)
{
	try {
		$url = "http://api.map.baidu.com/geocoder/v2/?ak=$ak&location=$lat,$lng&output=json";
		$result = file_get_contents($url);
		if (empty($result)) {
			return false;
		}
		$result = json_decode($result, true);
		if ($result['status'] != 0) {
			return false;
		}
		$info = $result['result']['addressComponent'];
		$info['formatted_address'] = $result['result']['formatted_address'];
		return $info;

	} catch (Exception $e) {
		
	} 
	return false;
}

function getLocationFromAddr($ak, $addr, $city = null)
{
	//http://api.map.baidu.com/geocoder/v2/?ak=dZ5K1m0VW7ZEa9QQLgy1XjpxC7Y6Vgsy&&address=北京东城板桥南港17号&output=json
	
	try {		
		$url = "http://api.map.baidu.com/geocoder/v2/?ak=$ak&address=$addr&output=json";
		if ($city != null) {
			$url .= "&city=$city";
		}
		$result = file_get_contents($url);
		if (empty($result)) {
			return false;
		}
		$result = json_decode($result, true);
		if ($result['status'] != 0) {
			return false;
		}
		$info = $result['result']['location'];
		return $info;

	} catch (Exception $e) {
		
	} 
	return false;
}

function getLocationFromIP($ak, $ip)
{
	try {
		$url = "http://api.map.baidu.com/location/ip?ak=$ak&ip=$ip&coor=bd09ll";
		$result = file_get_contents($url);
		if (empty($result)) {
			return false;
		}
		$result = json_decode($result, true);
		if ($result['status'] != 0) {
			return false;
		}
	} catch (Exception $e) {
		
	} 
	return false;
}
 
/* ============================== Make Random String ============================== */
function gen_rand_str($length = 10)
{
	mt_srand((double) microtime() * 1000000);
	
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString = $randomString . $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function gen_rand_num($length = 8)
{
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString = $randomString . $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function get_order_sn()
{
    mt_srand((double) microtime() * 1000000);
    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/* ============================== HTML Text ============================== */
function escape_string($str)
{
    $ret_str = $str;
    $ret_str = str_replace('\"', '"', $ret_str);
    $ret_str = str_replace("\'", "'", $ret_str);
    $ret_str = str_replace("\<'", "<", $ret_str);
    $ret_str = str_replace("\>'", ">", $ret_str);
    $ret_str = str_replace("\\\\", "\\", $ret_str);
    $ret_str = str_replace('&', '&amp;', $ret_str);
    return $ret_str;
}


/***** ==================================== *****/
function getFullUrl($url)
{
	if (empty($url)) {
		return $url;
	}
	return base_url() . $url;
}

function getPortalUrl($id, $kind = PORTAL_KIND_CONTENT, $share = "web") 
{
    if ($share == "mobile")
        $share = "/mobile";
    else
        $share = "";


	if ($kind == PORTAL_KIND_CONTENT) {
		return base_url() . 'portal/contents/' . $id . $share;
	} else if ($kind == PORTAL_KIND_LINK) {
		return base_url() . 'portal/links/' . $id . $share;
	} else if ($kind == PORTAL_KIND_EVENT) {
		return base_url() . 'portal/events/' . $id . $share;
	} else if ($kind == PORTAL_KIND_MEMBER) {
		return base_url() . 'portal/members/' . $id . $share;
	} else if ($kind == PORTAL_KIND_CLUB) {
		return base_url() . 'portal/clubs/' . $id . $share;
	} else if ($kind == PORTAL_KIND_ORGAN) {
		return base_url() . 'portal/organs/' . $id . $share;
	}
	return '#';
}

function getUserGender($gender)
{
	global $Genders;
	if (isset($Genders[$gender])) {
		return $Genders[$gender];
	}
	return '';
}

function getOrderStatus($status)
{
	global $OrderStatus;
	if (isset($OrderStatus[$status])) {
		return $OrderStatus[$status];
	}
	return '';
}

function getOrderStatusDetail($os, $ps, $ss, $shipType = 1)
{
	if ($os == ORDER_STATUS_FAILED) {
		return '取消';
	} else if ($os == ORDER_STATUS_SUCCEED) {
		return '完成';
	} else {
		if ($ps == PAY_STATUS_UNPAID) {
			return '未支付';
		} else {
			if ($ss == SHIP_STATUS_UNSHIPPED) {
				if ($shipType == 1) {
					return '待发货';
				} else {
					return '待取';
				}
			} else {
				return '已发货';
			}
		}
	}
}

function getFeedbackStatus($status)
{
	global $FeedbackStatus;
	if (isset($FeedbackStatus[$status])) {
		return $FeedbackStatus[$status];
	}
	return '';
}

function getCommentStatus($status)
{
	global $CommentStatus;
	if (isset($CommentStatus[$status])) {
		return $CommentStatus[$status];
	}
	return '';
}

function getAuditKind($kind)
{
	global $AuditKinds;
	if (isset($AuditKinds[$kind])) {
		return $AuditKinds[$kind];
	}
	return '';
}

function getAuditStatus($status)
{
	global $AuditStatus;
	if (isset($AuditStatus[$status])) {
		return $AuditStatus[$status];
	}
	return '';
}

function getContentKind($kind)
{
	global $ContentKinds;
	if (isset($ContentKinds[$kind])) {
		return $ContentKinds[$kind];
	}
	return '';
}

function getEventKind($kind)
{
	global $EventKinds;
	if (isset($EventKinds[$kind])) {
		return $EventKinds[$kind];
	}
	return '';
}

function getMemberKind($kind)
{
	global $MemberKinds;
	if (isset($MemberKinds[$kind])) {
		return $MemberKinds[$kind];
	}
	return '';
}

function getBannerKind($kind)
{
	global $BannerKinds;
	if (isset($BannerKinds[$kind])) {
		return $BannerKinds[$kind];
	}
	return '';
}

function getAuditValueType($type)
{
	global $AuditValueTypes;
	if (isset($AuditValueTypes[$type])) {
		return $AuditValueTypes[$type];
	}
	return '';
}

function getPlayerWeightLevel($weight)
{
	$weight = intval($weight);
	global $PlayerWeightLevels;
	if (isset($PlayerWeightLevels[$weight])) {
		return $PlayerWeightLevels[$weight];
	}
	return '';
}
?>
