<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>中维合众皇冠之战查询系统</title>
    <meta name="author" content="STSOFT Team"/>
    <meta name="copyright" content="T-One"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <script type="text/javascript" src="<?= base_url() ?>resources/js/jquery.min.js"></script>

    <script type="text/javascript" src="<?= base_url() ?>resources/js/share.js"></script>
    <link href="<?= base_url() ?>resources/css/share.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        #container {
            max-width: 1000px;
            _max-width: 640px;
            margin: 20px auto;
        }

        .publish {
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        p.top {
            text-align: center;
            background: #4E473D;
            background: -webkit-linear-gradient(#020202, #a0a0a0);
            background: -moz-linear-gradient(#020202, #a0a0a0);
            background: linear-gradient(#020202, #a0a0a0);
            padding: 30px 20px;
        }


    </style>
    <script type="text/javascript">
        $().ready(function () {

            $("#container img").lazyload({
                threshold: 100,
                effect: "fadeIn",
                skip_invisible: false
            });

        });

        function onBtnDown() {
            var app_url = "http://47.92.37.141/upload/binary/20170704/1499183841.apk";
            if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i)) {
                app_url = "https://itunes.apple.com/us/app/中维合众/id1220720568?l=zh&ls=1&mt=8";
            }

            window.location = app_url;
        }
    </script>
</head>
<body>
<?php if (!empty($mobile) && $mobile == 1): ?>
    <section class="share_banner">
        <a href="javascript:onBtnDown()">
            <img src="<?= base_url() ?>resources/images/logo.png" width="50px">
            <ul class="share_slogan">
                <li class="ss_logo"><img src="<?= base_url() ?>resources/images/tone.png" width="70px"></li>
                <li class="ss_slogan"><span>世界级跆拳道职业联赛</span></li>
            </ul>
            <div class="share_down"><span>立即下载</span></div>
        </a>
    </section>
<?php endif; ?>

<div class="navbar navbar-fixed-top">
    <div class="container">
    </div>
</div>

<div class="container">
    <div class="page-header" id="banner">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="text-center">
                    <img class="img-circle" width="200px" src="<?= base_url() . 'resources/images/logo_new.png' ?> ">
                    <h3 class="">中维合众皇冠之战查询系统</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="search">
        <div class="row">
            <div class="col-lg-12">
                <div class="">
                    <form class="bs-component" method="post">
                        <?php if (is_null($item)): ?>
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <p>无查询结果</p>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($item)): ?>
                            <div class="bs-component">
                                <?php if (is_array($item)): ?>

                                    <p class="top">
                                        <img src="<?= getFullUrl($item['image']) ?>" alt="" style="/*border-radius:50%;*/ max-width:200px; max-height:200px;">
                                        <img style="float: right;" width="50" height="30" class="flag" src="<?= base_url() . 'resources/images/flags/' . $item['country_id'] . '.jpg' ?>" alt="">
                                    </p>
                                    
                                    <h3 class="text-center">
                                        <?= $item['name'] ?>
                                        <?php if (!empty($item['en_name'])): ?>
                                            (<?= $item['en_name'] ?>)
                                        <?php endif; ?>

                                    </h3>
                                    <?php if ($item['kind'] == MEMBER_KIND_PLAYER): ?>
                                        <p class="text-center">
                                            <?= intval($item['score_win']) ?>胜 - <?= intval($item['score_loss']) ?>败
                                            - <?= intval($item['score_draw']) ?>平 <?= intval($item['score_ko']) ?>KO
                                        </p>
                                    <?php endif; ?>
                                    <table class="table table-striped table-hover">
                                        <tbody>
                                        <thead>

                                        </thead>

                                        <tr class="">
                                            <th>姓别</th>
                                            <th><?= getUserGender($item['gender']) ?></th>
                                        </tr>
                                        <tr>
                                            <td>身高</td>
                                            <th><?= $item['height'] ?>cm</th>
                                        </tr>
                                        <tr>
                                        <?php if ($item['kind'] == MEMBER_KIND_PLAYER): ?>
                                            <td>量级</td><td><?= getPlayerWeightLevel($item['weight']) ?></td>
                                        <?php else: ?>
                                            <td>体重</td><td><?= $item['weight'] ?>kg</td>
                                        <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <td>证书编号</td>
                                            <th><?= isset($item['cert_number']) ? $item['cert_number'] : '无' ?></th>
                                        </tr>
                                        <tr>
                                            <td>通过时间</td>
                                            <td><?= $item['create_date'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>身份证</td>
                                            <th><?= isset($item['idcard']) ? $item['idcard'] : '无' ?></th>
                                        </tr>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group ">
                            <!--<label class="col-lg-2 control-label"></label>-->
                            <div class="">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" id="optionsRadios1" value="cert_number"
                                                   checked="">
                                            证书编号
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" id="optionsRadios2"
                                                   value="idcard">
                                            身份证号
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <!--<label class="control-label" for="inputDefault">编号</label>-->
                            <input type="text" name="num" placeholder="请输入编号" class="form-control" id="num">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">提交</button>
                        </div>
                    </form>
                </div>


            </div>
            
        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>
    </footer>
</div>

</div>


<?php if (!empty($mobile) && $mobile == 1): ?>
    <table class="downbar3">
        <tbody>
        <tr>
            <td width="1"><img src="<?= base_url() ?>resources/images/logo3.png"></td>
            <td>更多精彩尽在中维合众APP</td>
            <td width="1" align="right">
                <button onclick="javascript:onBtnDown()">立即下载</button>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>

