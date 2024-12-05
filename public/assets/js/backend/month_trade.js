define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'month_trade/index' + location.search,
                    add_url: 'month_trade/add',
                    edit_url: 'month_trade/edit',
                    del_url: 'month_trade/del',
                    multi_url: 'month_trade/multi',
                    import_url: 'month_trade/import',
                    table: 'month_trade_table',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        {field: 'month', title: __('Month'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'payment_amount', title: __('Payment_amount'), operate:'BETWEEN'},
                        {field: 'order_count', title: __('Order_count')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
