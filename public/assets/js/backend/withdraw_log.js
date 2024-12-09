define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraw_log/index' + location.search,
                    add_url: 'withdraw_log/add',
                    edit_url: 'withdraw_log/edit',
                    del_url: 'withdraw_log/del',
                    multi_url: 'withdraw_log/multi',
                    import_url: 'withdraw_log/import',
                    table: 'withdrawn_log',
                }
            });

            var table = $("#table");
            var issuper =Config.isSuperAdmin;
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                fixedColumns: true,
                fixedRightNumber: 1,
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
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'withdrawal_status', title: __('Withdrawal_status'), searchList: {"已打款":__('已打款'),"已到账":__('已到账'),"处理中":__('处理中')}, formatter: Table.api.formatter.status},
                        {field: 'withdrawal_check', title: __('Withdrawal_check'), searchList: {"待审核":__('待审核'),"审核通过":__('审核通过')}, formatter: Table.api.formatter.normal},
                        {field: 'admin.username', title: __('Admin.username'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,visible: issuper,}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            if (!Config.isSuperAdmin){
                $('#withdrawal_status').hide();
                $('#withdrawal_check').hide();
                $('#merchant_name').hide();
            }
            Controller.api.bindevent();
        },
        edit: function () {
            if (!Config.isSuperAdmin){
                $('#withdrawal_status').hide();
                $('#withdrawal_check').hide();
                $('#merchant_name').hide();
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
