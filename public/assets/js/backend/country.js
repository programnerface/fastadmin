define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'country/index' + location.search,
                    add_url: 'country/add',
                    edit_url: 'country/edit',
                    del_url: 'country/del',
                    multi_url: 'country/multi',
                    import_url: 'country/import',
                    table: 'country',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'country_id',
                sortName: 'country_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'country_id', title: __('Country_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'iso_code_2', title: __('Iso_code_2'), operate: 'LIKE'},
                        {field: 'iso_code_3', title: __('Iso_code_3'), operate: 'LIKE'},
                        {field: 'postcode_required', title: __('Postcode_required')},
                        {field: 'status', title: __('Status')},
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
