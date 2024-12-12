define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mer_account/index' + location.search,
                    add_url: 'mer_account/add',
                    edit_url: 'mer_account/edit',
                    del_url: 'mer_account/del',
                    multi_url: 'mer_account/multi',
                    import_url: 'mer_account/import',
                    table: 'mer_account',
                }
            });

            var table = $("#table");
            var issuper =Config.isSuperAdmin;
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                sortOrder: 'asc',
                showExport: issuper,
                showToggle: issuper,
                commonSearch: issuper,
                showColumns: issuper,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false,visible: false},
                        {field: 'ven.type', title: __('Type'), searchList: {"Zelle":__('Zelle'),"Cash":__('Cash'),"Venmo":__('Venmo'),"Square":__('Square')}, formatter: Table.api.formatter.normal},
                        // {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'account', title: __('Account'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'ven.account', title: __('Ven.account'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,visible: issuper}
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
