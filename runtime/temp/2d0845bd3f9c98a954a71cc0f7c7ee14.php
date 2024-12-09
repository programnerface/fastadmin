<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:91:"D:\website\phpstudy_pro\WWW\fastadmin.com\public/../application/admin\view\refund\edit.html";i:1733707385;s:84:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\layout\default.html";i:1732090683;s:81:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\meta.html";i:1732090683;s:83:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\script.html";i:1732090683;}*/ ?>
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

    <div class="form-group" id="refund_date">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_date'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_date" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[refund_date]" type="text" value="<?php echo $row['refund_date']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_name" data-rule="required"  class="form-control" name="row[refund_name]" type="text" value="<?php echo htmlentities($row['refund_name'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('订单类型'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_type" data-rule="required" class="form-control" step="0.01" name="row[order_type]" type="text" value="<?php echo htmlentities($row['order_type'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('订单id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_id" data-rule="required" class="form-control" step="0.01" name="row[order_id]"type="text" value="<?php echo htmlentities($row['order_id'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_amount" data-rule="required" class="form-control" step="0.01" name="row[refund_amount]" type="number" value="<?php echo htmlentities($row['refund_amount'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_contact_info'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_contact_info" data-rule="required" class="form-control" name="row[refund_contact_info]" type="text" value="<?php echo htmlentities($row['refund_contact_info'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group" id="refund_status">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($refundStatusList) || $refundStatusList instanceof \think\Collection || $refundStatusList instanceof \think\Paginator): if( count($refundStatusList)==0 ) : echo "" ;else: foreach($refundStatusList as $key=>$vo): ?>
            <label for="row[refund_status]-<?php echo $key; ?>"><input id="row[refund_status]-<?php echo $key; ?>" name="row[refund_status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['refund_status'])?$row['refund_status']:explode(',',$row['refund_status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_img'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-refund_img" class="form-control" size="50" name="row[refund_img]" type="text" value="<?php echo htmlentities($row['refund_img'] ?? ''); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-refund_img" class="btn btn-danger faupload" data-input-id="c-refund_img" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" data-preview-id="p-refund_img"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-refund_img" class="btn btn-primary fachoose" data-input-id="c-refund_img" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-refund_img"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-refund_img"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Refund_biz'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-refund_biz" class="form-control" name="row[refund_biz]" type="text" value="<?php echo htmlentities($row['refund_biz'] ?? ''); ?>">
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
