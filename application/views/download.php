<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>下载</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<script type="text/javascript">
var android_down_url = '<?= $androidUrl ?>';  
var ios_down_url = '<?= $iphoneUrl ?>';  


var ua = navigator.userAgent.toLowerCase();    
if (ua.indexOf('micromessenger') > 0 && ua.indexOf('android') > 0) {                      //需对所有 ios 系统 UA 信息进行判断  
	//document.body.style.background="url(download_wx_android.jpg) no-repeat center center";
	//document.body.style.backgroundSize = "cover";
}
else if (ua.indexOf('micromessenger') > 0 && ua.indexOf('iphone') > 0) {                      //需对所有 ios 系统 UA 信息进行判断  
    //document.body.style.background="url(download_wx_ios.jpg) no-repeat center center";
    //document.body.style.backgroundSize = "cover";
}

else if (ua.indexOf('android') > 0) {              //需对所有 android 系统 UA 信息进行判断  
    window.location.href = android_down_url;  
}
else if (ua.indexOf('iphone') > 0) {              //微信 
    window.location.href = ios_down_url;  
}
</script>
</head>
<body>
<a href="<?= $androidUrl ?>">点击下载 安卓版“中维合众”</a><br/><br/>
<a href="<?= $iphoneUrl ?>">点击下载 苹果版“中维合众”</a>
</body>
</html>