<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:99:"D:\website\phpstudy_pro\WWW\fastadmin.com\public/../application/admin\view\withdraw_count\edit.html";i:1732783846;s:84:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\layout\default.html";i:1732090683;s:81:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\meta.html";i:1732090683;s:83:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\script.html";i:1732090683;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-merchant_name" class="form-control" name="row[merchant_name]" type="text" value="<?php echo htmlentities($row['merchant_name'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Total_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-total_amount" class="form-control" step="0.01" name="row[total_amount]" type="number" value="<?php echo htmlentities($row['total_amount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Set_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-set_amount" class="form-control" step="0.01" name="row[set_amount]" type="number" value="<?php echo htmlentities($row['set_amount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Withdrawn_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-withdrawn_amount" class="form-control" step="0.01" name="row[withdrawn_amount]" type="number" value="<?php echo htmlentities($row['withdrawn_amount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_amount" class="form-control" step="0.01" name="row[refund_amount]" type="number" value="<?php echo htmlentities($row['refund_amount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Unset_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-unset_amount" class="form-control" step="0.01" name="row[unset_amount]" type="number" value="<?php echo htmlentities($row['unset_amount'] ?? ''); ?>">
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
