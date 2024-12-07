define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cash/index' + location.search,
                    add_url: 'cash/add',
                    edit_url: 'cash/edit',
                    del_url: 'cash/del',
                    multi_url: 'cash/multi',
                    import_url: 'cash/import',
                    table: 'cash',
                }
            });

            var table = $("#table");
            var name = Config.merchant_name;
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                fixedColumns: true,
                fixedRightNumber: 1,
                sortOrder: 'asc',
                showExport: name,
                showToggle: name,
                commonSearch: name,
                showColumns: name,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID'),visible:false},
                        {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'admin.username', title: __('商户名称'), operate: 'LIKE'},
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'order_date', title: __('Order_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'payer', title: __('Payer'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'type.name', title: __('产品名称'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',formatter: function (value, row, index) {
                                // console.log(table.bootstrapTable('getOptions'))

                                return "$"+value
                            }},
                        {field: 'country.name', title: __('国家'), operate: 'LIKE'},
                        {field: 'zone.name', title: __('州/省'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'payment_address', title: __('Payment_address'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'payment_address2', title: __('Payment_address2'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'confirm_code', title: __('Confirm_code'), operate: 'LIKE'},
                        {field: 'fees', title: __('Fees'), operate:'BETWEEN'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN',formatter: function (value, row, index) {
                                // console.log(table.bootstrapTable('getOptions'))
                                return "$"+value
                            }},
                        {field: 'payment_img', title: __('Payment_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        {field: 'waybill_num', title: __('Waybill_num'), operate: 'LIKE'},
                        {field: 'order_status', title: __('Order_status'), searchList: {"未确认":__('未确认'),"已入账":__('已入账'),"已发货":__('已发货')}, custom:{"未确认":'danger'},formatter: Table.api.formatter.status},
                        // {field: 'admin_id', title: __('商户名称')},
                        // {field: 'product_type_ids', title: __('Product_type_ids')},
                        // {field: 'country_id', title: __('Country_id')},
                        // {field: 'zone_id', title: __('Zone_id')},
                        {field: 'order_check', title: __('Order_check'), searchList: {"待审核":__('待审核'),"审核通过":__('审核通过')},custom:{"待审核":'danger'}, formatter: Table.api.formatter.normal},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            table.on('load-success.bs.table',function (e,data){
                if (!Config.isSuperAdmin){
                    // caigoujia就是采购价的字段名根据实际修改,需要隐藏的字段
                    table.bootstrapTable('hideColumn', 'merchant_name');
                }
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            if (!Config.isSuperAdmin){
                $('#merchant_name').hide();
                $('#order_date').hide();
                $('#fees').hide();
                $('#amount').hide();
            }
            Controller.api.bindevent();
        },
        edit: function () {
            if (!Config.isSuperAdmin){
                $('#merchant_name').hide();
                $('#order_date').hide();
                $('#fees').hide();
                $('#amount').hide();
            }
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
