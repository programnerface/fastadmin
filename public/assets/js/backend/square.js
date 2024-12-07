define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'square/index' + location.search,
                    add_url: 'square/add',
                    edit_url: 'square/edit',
                    del_url: 'square/del',
                    multi_url: 'square/multi',
                    import_url: 'square/import',
                    table: 'square',
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
                        {field: 'ID', title: __('ID')},
                        {field: 'merchant_name', title: __('Merchant_name'),visible: name, operate: 'LIKE', table: table, class: 'autocontent', formatter: function (value, row, index) {
                                value =row.admin.username
                                return value
                            }},
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'order_date', title: __('Order_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'payer', title: __('Payer'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'product_type_ids', title: __('Product_type_ids')},
                        {field: 'type.name', title: __('产品类型'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',formatter: function (value, row, index){
                                return "$"+value
                            }},
                        // {field: 'country_id', title: __('Country_id')},
                        // {field: 'zone_id', title: __('Zone_id')},
                        {field: 'country.name', title: __('国家'), operate: 'LIKE'},
                        {field: 'zone.name', title: __('州/省'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'payment_address', title: __('Payment_address'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'payment_address2', title: __('Payment_address2'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'confirm_code', title: __('Confirm_code'), operate: 'LIKE'},
                        {field: 'fees', title: __('Fees'), operate:'BETWEEN',formatter: function (value, row, index) {
                                if(value == null){
                                    value = row.admin.square_fees
                                }else{
                                    value = value
                                }
                                value=(value * 100).toFixed(2) + '%'
                                return value;
                            }},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN',formatter: function (value, row, index) {
                                value =row.price*(1-row.fees)
                                return "$"+value
                            }},
                        {field: 'payment_img', title: __('Payment_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        {field: 'waybill_num', title: __('Waybill_num'), operate: 'LIKE'},
                        {field: 'order_status', title: __('Order_status'), searchList: {"未确认":__('未确认'),"已入账":__('已入账'),"已发货":__('已发货')},custom:{"未确认":'danger'}, formatter: Table.api.formatter.status},
                        {field: 'order_check', title: __('Order_check'), searchList: {"待审核":__('待审核'),"审核通过":__('审核通过')},custom:{"待审核":'danger'},formatter: Table.api.formatter.normal},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'admin.username', title: __('Admin.username'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
