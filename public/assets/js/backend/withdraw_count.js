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
                        // {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'admin.username', title: __('商户'), operate: 'LIKE'},
                        {field: 'groups_text', title: __('Group'), visible: false,operate:false, formatter: function (value,row,index) {
                                value = row.groups_text

                                return '<span class="label label-primary">' + value + '</span>';
                            }},
                        {field: 'total_amount', title: __('Total_amount'), operate:'BETWEEN'},
                        {field: 'fees', title: __('手续费'), operate:false},
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
            var issuper =Config.isSuperAdmin;


            // 初始化表格
            // table2.bootstrapTable({
            //     extend: {
            //         index_url: 'withdraw_log/index' + location.search,
            //         add_url: 'withdraw_log/add',
            //         edit_url: 'withdraw_log/edit',
            //         del_url: 'withdraw_log/del',
            //         multi_url: 'withdraw_log/multi',
            //         import_url: 'withdraw_log/import',
            //         table: 'withdrawn_log',
            //     },
            //     url: 'withdraw_log/index?addtabs=1',
            //     toolbar:'#toolbar2',
            //     pk: 'ID',
            //     sortName: 'ID',
            //     columns: [
            //         [
            //             {checkbox: true},
            //             {field: 'ID', title: __('ID')},
            //             {field: 'withdrawal_date', title: __('提现日期'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
            //             // {field: 'merchant_name', title: __('Merchant_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
            //             {field: 'admin.username', title: __('商户名称'), operate: 'LIKE'},
            //             {field: 'groups_text', title: __('Group'), operate:false, formatter: function (value,row,index) {
            //                     value = row.groups_text
            //
            //                     return '<span class="label label-primary">' + value + '</span>';
            //                 }},
            //             // {field: 'admin_id', title: __('Admin_id')},
            //             {field: 'withdrawal_usamount', title: __('提现金额($)'), operate:'BETWEEN'},
            //             {field: 'withdrawal_cnamount', title: __('提现金额(￥)'), operate:'BETWEEN'},
            //             {field: 'withdrawal_way', title: __('渠道'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
            //             {field: 'withdrawal_img', title: __('截图'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
            //             {field: 'withdrawal_status', title: __('提现状态'), searchList: {"处理中":__('处理中'),"已打款":__('已打款'),"已到账":__('已到账')}, custom:{"处理中":'danger'},formatter: Table.api.formatter.status},
            //             {field: 'withdrawal_check', title: __('提现审核'), searchList: {"待审核":__('待审核'),"审核通过":__('审核通过')},custom:{"待审核":'danger'}, formatter: Table.api.formatter.normal},
            //             {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate,visible: issuper,}
            //         ]
            //     ]
            // });

            table2.bootstrapTable({
                extend: {
                    index_url: 'day_trade/index' + location.search,
                    add_url: 'day_trade/add',
                    edit_url: 'day_trade/edit',
                    del_url: 'day_trade/del',
                    multi_url: 'day_trade/multi',
                    import_url: 'day_trade/import',
                    table: 'day_trade_table',
                },
                // url: $.fn.bootstrapTable.defaults.extend.index_url,
                // url: 'day_trade/index?addtabs=1',
                url: 'day_trade/index?addtabs=1',
                toolbar:'#toolbar2',
                pk: 'ID',
                sortName: 'ID',
                sortOrder: 'asc',
                searchFormVisible: true,
                //启用固定列
                fixedColumns: true,
                //固定右侧列数
                fixedNumber: 2,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID'),operate:false},
                        {field: 'wallets', title: __('支付类型'),operate:false, searchList: {"Zelle":__('Zelle'),"Square":__('Square'),"Cash App":__('Cash App'),"Venmo":__('Venmo')}, formatter: Table.api.formatter.normal},
                        {field: 'order_date', title: __('日期'), visible: false, addclass:'datetimepicker',operate: 'BETWEEN', autocomplete:false,data: 'data-date-format="YYYY-MM-DD"'},
                        {field: 'payment_amount', title: __('总额'), operate:false},
                        {field: 'order_count', title: __('笔数'),operate:false},
                        {field: 'fees', title: __('手续费'), operate:false},
                        {field: 'payment_due', title: __('应结算'), operate:false},
                        // {field: 'admin.username', title: __('商户名称'), operate: 'LIKE',},
                        {field: 'admin.username', title: __('商户'),visible: false,addclass:'selectpage',data:'data-source="auth/admin/index" data-primary-key="username" data-field="username"',formatter: Table.api.formatter.search},
                        // {field: 'order_zelle', title: __('Order_zelle'),operate:false},
                        // {field: 'order_cash', title: __('Order_cash'),operate:false},
                        // {field: 'order_venmo', title: __('Order_venmo'),operate:false},
                        // {field: 'order_square', title: __('Order_square'),operate:false},
                        {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            })


            // 为表格绑定事件
            Table.api.bindevent(table2);
            // 监听下拉列表改变的事件
            $(document).on('change', 'select[name=admin]', function () {
                $("input[name='admin_id']").val($(this).val());
            });


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
