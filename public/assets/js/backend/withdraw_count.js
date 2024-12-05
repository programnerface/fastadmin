define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({});

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                extend: {
                    index_url: 'withdraw_count/index' + location.search,
                    add_url: 'withdraw_count/add',
                    edit_url: 'withdraw_count/edit',
                    del_url: 'withdraw_count/del',
                    multi_url: 'withdraw_count/multi',
                    import_url: 'withdraw_count/import',
                    table: 'withdrawn_count',
                },
                url: 'withdraw_count/index?addtabs=1',
                toolbar:'#toolbar',
                pk: 'ID',
                sortName: 'ID',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'total_amount', title: __('Total_amount'), operate:'BETWEEN'},
                        {field: 'set_amount', title: __('Set_amount'), operate:'BETWEEN'},
                        {field: 'withdrawn_amount', title: __('Withdrawn_amount'), operate:'BETWEEN'},
                        {field: 'refund_amount', title: __('Refund_amount'), operate:'BETWEEN'},
                        {field: 'unset_amount', title: __('Unset_amount'), operate:'BETWEEN'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);


            var table2 = $("#table2");

            // 初始化表格
            table2.bootstrapTable({
                extend: {
                    index_url: 'withdraw_log/index' + location.search,
                    add_url: 'withdraw_log/add',
                    edit_url: 'withdraw_log/edit',
                    del_url: 'withdraw_log/del',
                    multi_url: 'withdraw_log/multi',
                    import_url: 'withdraw_log/import',
                    table: 'withdrawn_log',
                },
                url: 'withdraw_log/index?addtabs=1',
                toolbar:'#toolbar2',
                pk: 'ID',
                sortName: 'ID',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        {field: 'withdrawal_date', title: __('Withdrawal_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'withdrawal_usamount', title: __('Withdrawal_usamount'), operate:'BETWEEN'},
                        {field: 'withdrawal_cnamount', title: __('Withdrawal_cnamount'), operate:'BETWEEN'},
                        {field: 'withdrawal_way', title: __('Withdrawal_way'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'withdrawal_img', title: __('Withdrawal_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table2);

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
