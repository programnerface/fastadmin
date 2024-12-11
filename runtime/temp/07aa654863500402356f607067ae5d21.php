<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:89:"D:\website\phpstudy_pro\WWW\fastadmin.com\public/../application/admin\view\zelle\add.html";i:1733822431;s:84:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\layout\default.html";i:1732090683;s:81:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\meta.html";i:1732090683;s:83:"D:\website\phpstudy_pro\WWW\fastadmin.com\application\admin\view\common\script.html";i:1732090683;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

<!--    <div class="form-group" id="merchant_name" style="display: none">-->
<!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_name'); ?>:</label>-->
<!--        <div class="col-xs-12 col-sm-8">-->
<!--            <input id="c-merchant_name" class="form-control" name="row[merchant_name]" type="text">-->
<!--        </div>-->
<!--    </div>-->
        <div class="form-group" id="vender" >
            <label class="control-label col-xs-12 col-sm-2"><?php echo __('Vender'); ?>:</label>
            <div class="col-xs-12 col-sm-8">
                <input id="c-vender" class="form-control" name="row[vender]" type="text">
            </div>
        </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_num'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_num" data-rule="required" class="form-control" name="row[order_num]" type="text">
        </div>
    </div>
    <div class="form-group" id="order_date">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_date'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_date" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" data-use-current="true" name="row[order_date]" type="text" value="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Payer'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-payer" class="form-control" name="row[payer]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Product_type_ids'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-product_type_ids" data-rule="required" data-source="product_type/index"  class="form-control selectpage" name="row[product_type_ids]" type="text" value="">
        </div>
        <label class="control-label col-xs-12 col-sm-1"><?php echo __('数量'); ?>:</label>
        <div class="col-xs-12 col-sm-3">
            <input id="c-quantity" data-rule="required" class="form-control " name="row[quantity]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Price'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-price" class="form-control" step="0.01" name="row[price]" type="number" >
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Country_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-country_id" data-rule="required" data-source="country/index" data-primary-key="country_id" data-field="name" class="form-control selectpage" name="row[country_id]" type="text" value="223">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Zone_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-zone_id" data-rule="required" data-source="zone/index" data-primary-key="zone_id" data-field="name" class="form-control selectpage" name="row[zone_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('City'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-city" class="form-control" name="row[city]" type="text" value="<?php echo htmlentities($row['city'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Payment_address'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-payment_address" class="form-control" name="row[payment_address]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Payment_address2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-payment_address2" class="form-control" name="row[payment_address2]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Postcode'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-postcode" class="form-control" name="row[postcode]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Confirm_code'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-confirm_code" class="form-control" name="row[confirm_code]" type="text">
        </div>
    </div>
    <div class="form-group" id="shipping_name">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('收件人'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-shipping_name" class="form-control" name="row[shipping_name]" type="text">
        </div>
    </div>
    <div class="form-group" id="fees">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Fees'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-fees" class="form-control" step="0.01" name="row[fees]" type="number" value="<?php echo htmlentities($row['fees'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group" id="amount">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-amount" class="form-control" step="0.01" name="row[amount]" type="number" value="<?php echo htmlentities($row['fees'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Payment_img'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-payment_img" class="form-control" size="50" name="row[payment_img]" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-payment_img" class="btn btn-danger faupload" data-input-id="c-payment_img" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" data-preview-id="p-payment_img"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-payment_img" class="btn btn-primary fachoose" data-input-id="c-payment_img" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-payment_img"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-payment_img"></ul>
        </div>
    </div>
    <div class="form-group" >
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Waybill_num'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-waybill_num" class="form-control" name="row[waybill_num]" type="text" value="">
        </div>
    </div>
    <div class="form-group" id="vendor_invoice">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('发票'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-vendor_invoice" class="form-control" name="row[vendor_invoice]" type="text">
        </div>
    </div>
    <div class="form-group" id="order_status" >
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($orderStatusList) || $orderStatusList instanceof \think\Collection || $orderStatusList instanceof \think\Paginator): if( count($orderStatusList)==0 ) : echo "" ;else: foreach($orderStatusList as $key=>$vo): ?>
            <label for="row[order_status]-<?php echo $key; ?>"><input id="row[order_status]-<?php echo $key; ?>" name="row[order_status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"未确认"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group" id="order_check">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_check'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-order_check" class="form-control selectpicker" name="row[order_check]">
                <?php if(is_array($orderCheckList) || $orderCheckList instanceof \think\Collection || $orderCheckList instanceof \think\Paginator): if( count($orderCheckList)==0 ) : echo "" ;else: foreach($orderCheckList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
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
