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
            var issuper =Config.isSuperAdmin;

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
                        {field: 'refund_date', title: __('Refund_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false,formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'admin.username', title: __('商户'), operate: 'LIKE',visible: issuper,},
                        {field: 'wallets', title: __('Wallets'), searchList: {"Zelle":__('Zelle'),"Square":__('Square'),"Cash App":__('Cash App'),"Venmo":__('Venmo')}, formatter: Table.api.formatter.normal},
                        {field: 'refund_name', title: __('Refund_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},

                        // {field: 'order_type', title: __('订单类型'), operate:'BETWEEN'},

                        // {field: 'order_id', title: __('订单Id'), operate:'BETWEEN'},
                        {field: 'refund_amount', title: __('Refund_amount'), operate:'BETWEEN'},
                        // {field: 'refund_contact_info', title: __('Refund_contact_info'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'refund_img', title: __('Refund_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        // {field: 'refund_biz', title: __('Refund_biz'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},、
                        {field: 'refund_status', title: __('Refund_status'), searchList: {"待退款":__('待退款'),"已退款":__('已退款')},custom:{"待退款":'yellow'},formatter: Table.api.formatter.status},
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
