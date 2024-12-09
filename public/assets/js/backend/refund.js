define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'refund/index' + location.search,
                    add_url: 'refund/add',
                    edit_url: 'refund/edit',
                    del_url: 'refund/del',
                    multi_url: 'refund/multi',
                    import_url: 'refund/import',
                    table: 'refund',
                }
            });

            var table = $("#table");
            var name = Config.refund_name;
            console.log(name)
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ID',
                sortName: 'ID',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        // {field: 'admin_id', title: __('Admin_id'),visible: name},
                        {field: 'refund_date', title: __('Refund_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'refund_name', title: __('Refund_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'admin.username', title: __('商户名'), operate: 'LIKE'},
                        {field: 'order_type', title: __('订单类型'), operate:'BETWEEN'},
                        {field: 'order_id', title: __('订单Id'), operate:'BETWEEN'},
                        {field: 'refund_amount', title: __('Refund_amount'), operate:'BETWEEN'},
                        {field: 'refund_contact_info', title: __('Refund_contact_info'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'refund_status', title: __('Refund_status'), searchList: {"处理中":__('处理中'),"退款失败":__('退款失败'),"退款成功":__('退款成功')}, formatter: Table.api.formatter.status},
                        {field: 'refund_img', title: __('Refund_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        {field: 'refund_biz', title: __('Refund_biz'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            if (!Config.isSuperAdmin){
                $('#refund_date').hide();
                $('#refund_status').hide();
            }
            Controller.api.bindevent();
        },
        edit: function () {
            if (!Config.isSuperAdmin){
                $('#refund_date').hide();
                $('#refund_status').hide();
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
