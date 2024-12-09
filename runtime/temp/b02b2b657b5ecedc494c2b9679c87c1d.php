<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:91:"D:\website\phpstudy_pro\WWW\fastadmin.com\public/../application/admin\view\index\login.html";i:1733126225;s:81:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\meta.html";i:1732090683;s:83:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\script.html";i:1732090683;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config ?? ''); ?>
    };
</script>


    <style type="text/css">
        body {
            color: #999;
            background-color: #f1f4fd;
            background-size: cover;
        }

        a {
            color: #444;
        }


        .login-screen {
            max-width: 430px;
            padding: 0;
            margin: 100px auto 0 auto;

        }

        .login-screen .logon-tab > a {
            display: block;
            padding: 20px;
            float: left;
            width: 50%;
            font-size: 16px;
            text-align: center;
            color: #616161;
            background-color: #efefef;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .login-screen .logon-tab > a:hover {
            background-color: #fafafa;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .login-screen .logon-tab > a.active {
            background-color: #fff;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .login-screen .well {
            border-radius: 3px;
            -webkit-box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 1);
            border: none;
            /*overflow: hidden;*/
            padding: 0;
        }

        @media (max-width: 767px) {
            .login-screen {
                padding: 0 20px;
            }
        }

        .profile-img-card {
            width: 100px;
            height: 100px;
            display: block;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            margin: -93px auto 30px;
            border: 5px solid #fff;
        }

        .profile-name-card {
            text-align: center;
        }

        .login-head {
            background: #899fe1;
            border-radius: 3px 3px 0 0;
        }

        .login-form {
            padding: 40px 30px;
            position: relative;
            z-index: 99;
        }

        #login-form {
            margin-top: 20px;
        }

        #login-form .input-group {
            margin-bottom: 15px;
        }

        #login-form .form-control {
            font-size: 13px;
        }

    </style>
    <!--@formatter:off-->
    <?php if($background): ?>
    <style type="text/css">
        body{
            background-image: url('<?php echo $background; ?>');
        }
    </style>
    <?php endif; ?>
    <!--@formatter:on-->
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <div class="login-screen">
            <div class="well">
                <div class="logon-tab clearfix"><a class="active"><?php echo __('Sign in'); ?></a><a href="<?php echo url('index/register'); ?>?url=<?php echo htmlentities(urlencode($url ?? '') ?? ''); ?>" ><?php echo __('Sign up'); ?></a></div>
                <div class="login-head">
                    <img src="/assets/img/login-head.png" style="width:100%;"/>
                </div>
                <div class="login-form">
                    <img id="profile-img" class="profile-img-card" src="/assets/img/avatar.png"/>
                    <p id="profile-name" class="profile-name-card"></p>

                    <form action="" method="post" id="login-form">
                        <!--@AdminLoginFormBegin-->
                        <div id="errtips" class="hide"></div>
                        <?php echo token(); ?>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
                            <input type="text" class="form-control" id="pd-form-username" placeholder="<?php echo __('Username'); ?>" name="username" autocomplete="off" value="" data-rule="<?php echo __('Username'); ?>:required;username"/>
                        </div>

                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                            <input type="password" class="form-control" id="pd-form-password" placeholder="<?php echo __('Password'); ?>" name="password" autocomplete="off" value="" data-rule="<?php echo __('Password'); ?>:required;password"/>
                        </div>
                        <!--@CaptchaBegin-->
                        <?php if(\think\Config::get('fastadmin.login_captcha')): ?>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></div>
                            <input type="text" name="captcha" class="form-control" placeholder="<?php echo __('Captcha'); ?>" data-rule="<?php echo __('Captcha'); ?>:required;length(<?php echo \think\Config::get('captcha.length'); ?>)" autocomplete="off"/>
                            <span class="input-group-addon" style="padding:0;border:none;cursor:pointer;">
                                    <img src="<?php echo rtrim('/', '/'); ?>/index.php?s=/captcha" width="100" height="30" onclick="this.src = '<?php echo rtrim('/', '/'); ?>/index.php?s=/captcha&r=' + Math.random();"/>
                            </span>
                        </div>
                        <?php endif; ?>
                        <!--@CaptchaEnd-->
                        <?php if($keeyloginhours>0): ?>
                        <div class="form-group checkbox">
                            <label class="inline" for="keeplogin" data-toggle="tooltip" title="<?php echo __('The duration of the session is %s hours', $keeyloginhours); ?>">
                                <input type="checkbox" name="keeplogin" id="keeplogin" value="1"/>
                                <?php echo __('Keep login'); ?>
                            </label>
                            <div class="pull-right"><a href="<?php echo url('index/venderregister'); ?>?url=<?php echo htmlentities(urlencode($url ?? '') ?? ''); ?>" class="btn-forgot" ><?php echo __('Vendor Register'); ?></a></div>
                        </div>

                        <?php endif; ?>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg btn-block" style="background:#708eea;"><?php echo __('Sign in'); ?></button>
                            <a href="<?php echo url('index/register'); ?>?url=<?php echo htmlentities(urlencode($url ?? '') ?? ''); ?>" class="btn btn-default btn-lg btn-block mt-3 no-border"><?php echo __("Don't have an account? Sign up"); ?></a>
                        </div>
                        <!--@AdminLoginFormEnd-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version'] ?? ''); ?>"></script>

<script type="text/html" id="resetpwdtpl">
    <form id="resetpwd-form" class="form-horizontal form-layer" method="POST" action="<?php echo url('api/user/resetpwd'); ?>">
        <div class="form-body">
            <input type="hidden" name="action" value="resetpwd"/>
            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-3"><?php echo __('Type'); ?>:</label>
                <div class="col-xs-12 col-sm-8">
                    <div class="radio">
                        <label for="type-email"><input id="type-email" checked="checked" name="type" data-send-url="<?php echo url('api/ems/send'); ?>" data-check-url="<?php echo url('api/validate/check_ems_correct'); ?>" type="radio" value="email"> <?php echo __('Reset password by email'); ?></label>
                        <label for="type-mobile"><input id="type-mobile" name="type" type="radio" data-send-url="<?php echo url('api/sms/send'); ?>" data-check-url="<?php echo url('api/validate/check_sms_correct'); ?>" value="mobile"> <?php echo __('Reset password by mobile'); ?></label>
                    </div>
                </div>
            </div>
            <div class="form-group" data-type="email">
                <label for="email" class="control-label col-xs-12 col-sm-3"><?php echo __('Email'); ?>:</label>
                <div class="col-xs-12 col-sm-8">
                    <input type="text" class="form-control" id="email" name="email" value="" data-rule="required(#type-email:checked);email;remote(<?php echo url('api/validate/check_email_exist'); ?>, event=resetpwd, id=0)" placeholder="">
                    <span class="msg-box"></span>
                </div>
            </div>
            <div class="form-group hide" data-type="mobile">
                <label for="mobile" class="control-label col-xs-12 col-sm-3"><?php echo __('Mobile'); ?>:</label>
                <div class="col-xs-12 col-sm-8">
                    <input type="text" class="form-control" id="mobile" name="mobile" value="" data-rule="required(#type-mobile:checked);mobile;remote(<?php echo url('api/validate/check_mobile_exist'); ?>, event=resetpwd, id=0)" placeholder="">
                    <span class="msg-box"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="captcha" class="control-label col-xs-12 col-sm-3"><?php echo __('Captcha'); ?>:</label>
                <div class="col-xs-12 col-sm-8">
                    <div class="input-group">
                        <input type="text" name="captcha" class="form-control" data-rule="required;length(<?php echo \think\Config::get('captcha.length'); ?>);digits;remote(<?php echo url('api/validate/check_ems_correct'); ?>, event=resetpwd, email:#email)"/>
                        <span class="input-group-btn" style="padding:0;border:none;">
                            <a href="javascript:;" class="btn btn-primary btn-captcha" data-url="<?php echo url('api/ems/send'); ?>" data-type="email" data-event="resetpwd"><?php echo __('Send verification code'); ?></a>
                        </span>
                    </div>
                    <span class="msg-box"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="newpassword" class="control-label col-xs-12 col-sm-3"><?php echo __('New password'); ?>:</label>
                <div class="col-xs-12 col-sm-8">
                    <input type="password" class="form-control" id="newpassword" name="newpassword" value="" data-rule="required;password" placeholder="">
                    <span class="msg-box"></span>
                </div>
            </div>
        </div>
        <div class="form-group form-footer">
            <div class="col-xs-12 col-sm-8 col-sm-offset-3">
                <button type="submit" class="btn btn-md btn-primary"><?php echo __('Ok'); ?></button>
            </div>
        </div>
    </form>
</script>
</body>
</html>
