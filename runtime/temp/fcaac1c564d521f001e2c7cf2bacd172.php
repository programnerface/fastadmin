<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:97:"D:\website\phpstudy_pro\WWW\fastadmin.com\public/../application/admin\view\withdraw_log\edit.html";i:1733739793;s:84:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\layout\default.html";i:1732090683;s:81:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\meta.html";i:1732090683;s:83:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\script.html";i:1732090683;}*/ ?>
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

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_date'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-withdrawal_date" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[withdrawal_date]" type="text" value="<?php echo $row['withdrawal_date']; ?>">
        </div>
    </div>
    <div class="form-group" style="display: none">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-merchant_name" class="form-control" name="row[merchant_name]" type="text" value="<?php echo htmlentities($row['merchant_name'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group"  id="merchant_name">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('商户名称'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-admin_id" class="form-control selectpage" data-source="auth/admin/index" data-primary-key="id" data-field="username" name="row[admin_id]" type="text" value="<?php echo htmlentities($row['admin_id'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_usamount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-withdrawal_usamount" class="form-control" step="0.01" name="row[withdrawal_usamount]" type="number" value="<?php echo htmlentities($row['withdrawal_usamount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_cnamount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-withdrawal_cnamount" class="form-control" step="0.01" name="row[withdrawal_cnamount]" type="number" value="<?php echo htmlentities($row['withdrawal_cnamount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_way'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-withdrawal_way" class="form-control" name="row[withdrawal_way]" type="text" value="<?php echo htmlentities($row['withdrawal_way'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_img'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-withdrawal_img" class="form-control" size="50" name="row[withdrawal_img]" type="text" value="<?php echo htmlentities($row['withdrawal_img'] ?? ''); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-withdrawal_img" class="btn btn-danger faupload" data-input-id="c-withdrawal_img" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" data-preview-id="p-withdrawal_img"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-withdrawal_img" class="btn btn-primary fachoose" data-input-id="c-withdrawal_img" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-withdrawal_img"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-withdrawal_img"></ul>
        </div>
    </div>
    <div class="form-group" id="withdrawal_status">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($withdrawalStatusList) || $withdrawalStatusList instanceof \think\Collection || $withdrawalStatusList instanceof \think\Paginator): if( count($withdrawalStatusList)==0 ) : echo "" ;else: foreach($withdrawalStatusList as $key=>$vo): ?>
            <label for="row[withdrawal_status]-<?php echo $key; ?>"><input id="row[withdrawal_status]-<?php echo $key; ?>" name="row[withdrawal_status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['withdrawal_status'])?$row['withdrawal_status']:explode(',',$row['withdrawal_status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group" id="withdrawal_check">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawal_check'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-withdrawal_check" class="form-control selectpicker" name="row[withdrawal_check]">
                <?php if(is_array($withdrawalCheckList) || $withdrawalCheckList instanceof \think\Collection || $withdrawalCheckList instanceof \think\Paginator): if( count($withdrawalCheckList)==0 ) : echo "" ;else: foreach($withdrawalCheckList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['withdrawal_check'])?$row['withdrawal_check']:explode(',',$row['withdrawal_check']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-primary btn-embossed disabled"><?php echo __('OK'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version'] ?? ''); ?>"></script>
    </body>
</html>
