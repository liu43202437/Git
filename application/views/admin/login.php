<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>后台登录</title>
<meta http-equiv="expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jsbn.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/prng4.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/rng.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/rsa.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/base64.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript">
	$().ready( function() {
		
		var $loginForm = $("#loginForm");
		var $enPassword = $("#enPassword");
		var $email = $("#email");
		var $password = $("#password");
		var $captcha = $("#captcha");
		var $captchaImage = $("#captchaImage");
		var $isRememberUser = $("#isRememberUser");
		
		// 记住用户名
		if (getCookie("adminEMail") != null) {
			//$isRememberUser.prop("checked", true);
			$isRememberUser.iCheck('check')
			$email.val(getCookie("adminEMail"));
			$password.focus();
		} else {
			$isRememberUser.iCheck('uncheck')
			//$isRememberUser.prop("checked", false);
			$email.focus();
		}
		
		// 更换验证码
		$captchaImage.click( function() {
			$captchaImage.attr("src", "<?=base_url()?>admin/common/captcha?captchaId=<%=captchaId%>&timestamp=" + (new Date()).valueOf());
		});
		
		// 表单验证、记住用户名
		$loginForm.submit( function() {
			if ($email.val() == "") {
				$.message("warn", "请输入您的用户名");
				return false;
			}
			if ($password.val() == "") {
				$.message("warn", "请输入您的密码");
				return false;
			}
			/*if ($captcha.val() == "") {
				$.message("warn", "请输入您的验证码");
				return false;
			}*/
			if ($isRememberUser.prop("checked")) {
				addCookie("adminEMail", $email.val(), {expires: 7 * 24 * 60 * 60});
			} else {
				removeCookie("adminEMail");
			}
			
			/*var rsaKey = new RSAKey();
			rsaKey.setPublic(b64tohex("<%=modulus%>"), b64tohex("<%=exponent%>"));
			var enPassword = hex2b64(rsaKey.encrypt($password.val()));
			$enPassword.val(enPassword);*/
		});
		
		<?php if (isset($errorMessage)): ?>
			$.message("error", "<?= $errorMessage ?>");
		<?php endif; ?>
		
	});
</script>
</head>

<body class="login gray-bg">
    <div class="middle-box text-center animated fadeInDown">
        <div class="m-b-md">
            <img src="<?=base_url()?>resources/images/logo.png" alt="">
        </div>
        <h3>后台登录</h3>
        <form class="m-t" role="form" id="loginForm" action="<?=base_url()?>admin/do_login" method="post">
            <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" />
			<input type="hidden" name="redirect_url" value="<?= $redirectUrl ?>" />
		
            <div class="form-group">
                <input type="email" id="email" name="email" class="form-control" placeholder="邮箱" required="required">
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" class="form-control" placeholder="密码" required="required">
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>

            <div class="text-left checkbox">
                <label>
                	<input type="checkbox" class="i-check" id="isRememberUser"/> 记住账号
                </label>
	            <a class="pull-right m-r-sm hidden" href="register">注册一个新账号</a>
            </div>
        </form>
    </div>
    
</body>

</html>