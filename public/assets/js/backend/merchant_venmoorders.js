define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant_venmoorders/index' + location.search,
                    add_url: 'merchant_venmoorders/add',
                    edit_url: 'merchant_venmoorders/edit',
                    del_url: 'merchant_venmoorders/del',
                    multi_url: 'merchant_venmoorders/multi',
                    import_url: 'merchant_venmoorders/import',
                    table: 'merchant_venmoorders',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                fixedColumns: true,
                fixedRightNumber: 1,
                sortOrder: 'asc',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID'),visible:false},
                        {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'order_date', title: __('Order_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'payer', title: __('Payer'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'product_name', title: __('Product_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',formatter: function (value, row, index) {
                                return "$"+value
                            }},
                        {field: 'Identifier', title: __('Identifier'), operate: 'LIKE'},
                        {field: 'fees', title: __('Fees'), operate:'BETWEEN',formatter: function (value, row, index) {
                                value=(value * 100).toFixed(2) + '%'
                                return value;
                            }},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN',formatter: function (value, row, index) {
                                return "$"+value
                            }},
                        {field: 'payment_img', title: __('Payment_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        {field: 'carrier', title: __('Carrier'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'waybill_num', title: __('Waybill_num'), operate: 'LIKE'},
                        {field: 'order_status', title: __('Order_status'), searchList: {"未确认":__('未确认'),"已入账":__('已入账'),"已发货":__('已发货'),"已退款":__('已退款')}, formatter: Table.api.formatter.status,custom:{"未确认":'danger'},class: 'order-status'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,visible:false}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
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
