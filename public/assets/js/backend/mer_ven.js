define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mer_ven/index' + location.search,
                    add_url: 'mer_ven/add',
                    edit_url: 'mer_ven/edit',
                    del_url: 'mer_ven/del',
                    multi_url: 'mer_ven/multi',
                    import_url: 'mer_ven/import',
                    table: 'mer_ven',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'type', title: __('Type'), searchList: {"Zelle":__('Zelle'),"Cash":__('Cash'),"Venmo":__('Venmo'),"Square":__('Square')}, formatter: Table.api.formatter.normal},
                        // {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'ven_id', title: __('Ven_id')},
                        {field: 'admin1.username', title: __('供应商名称'), operate: 'LIKE'},
                        {field: 'admin.username', title: __('商户名称'), operate: 'LIKE'},
                        {field: 'account', title: __('Account'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'name', title: __('Name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
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
