define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            var operate =Config.operateedit;
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index' + location.search,
                    add_url: 'order/add',
                    merchantedit_url: 'order/merchantedit',
                    edit_url: operate,
                    del_url: 'order/del',
                    multi_url: 'order/multi',
                    import_url: 'order/import',
                    table: 'order',
                },
            });

            var table = $("#table");
            var issuper =Config.isSuperAdmin;

            // 初始化表格
            table.bootstrapTable({

                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),visible: issuper,},
                        {field: 'date', title: __('Date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        // {field: 'merchant', title: __('Merchant'),visible: issuper,operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'admin.username', title: __('商户'), operate: 'LIKE',visible: issuper,},
                        {field: 'wallets', title: __('Wallets'), searchList: {"Zelle":__('Zelle'),"Square":__('Square'),"Cash App":__('Cash App'),"Venmo":__('Venmo')}, formatter: Table.api.formatter.normal},
                        {field: 'customer', title: __('Customer'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'amount_total', title: __('Amount_total'), operate:'BETWEEN'},
                        {field: 'fee_rate', title: __('Fee_rate'), operate:'BETWEEN',visible: issuper,},
                        {field: 'fee', title: __('Fee'), operate:'BETWEEN',visible: issuper,},
                        {field: 'amount_due', title: __('Amount_due'), operate:'BETWEEN',visible: issuper,},
                        {field: 'payment_img', title: __('Payment_img'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.image},
                        {field: 'confirm_num', title: __('Confirm_num'), visible: issuper,operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'payment_method', title: __('Payment_method'),visible: issuper,operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'product_name', title: __('Product_name'),visible: issuper, operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'quantity', title: __('Quantity'),visible: issuper,},
                        {field: 'uint_price', title: __('Uint_price'), operate:'BETWEEN',visible: issuper,},
                        {field: 'shipping_cost', title: __('Shipping_cost'), operate:'BETWEEN',visible: issuper,},
                        {field: 'recipient_name', title: __('Recipient_name'), visible: issuper,operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'country_id', title: __('Country_id'),visible: issuper,},
                        {field: 'country.name', title: __('国家'), operate: 'LIKE',visible: issuper,},
                        {field: 'street_adress', title: __('Street_adress'),visible: issuper, operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'city', title: __('City'), operate: 'LIKE',visible: issuper, table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'zone_id', title: __('Zone_id'),visible: issuper,},
                        {field: 'zone.name', title: __('州/省'), operate: 'LIKE',visible: issuper,},
                        {field: 'zip', title: __('Zip'), operate: 'LIKE',visible: issuper, table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'shipping_address', title: __('Shipping_address'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'tracking_num', title: __('Tracking_num'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'invoice', title: __('发票'), operate: 'LIKE', table: table,  class: 'autocontent',formatter:function (value, row, index){

                                console.log(row)
                                console.log(row.zone)
                                if(row.zone = null){
                                    return '州/省 不能为空';
                                }else{
                                    value ='/sFGucSHOMQ.php/order/invoice.html?'
                                    value = Fast.api.escape(value);
                                    return '<a href="' + value + 'order_num=' + row.id + '" target="_blank">' + '查看' + '</a>';
                                }

                            }},
                        {field: 'order_state', title: __('Order_state'), searchList: {"Unconfirmed":__('未确认'),"Paid":__('已入账'),"Delivered":__('已签收')}, custom:{"Unconfirmed":'yellow',"Paid":'green',"Delivered":'blue'},formatter: Table.api.formatter.normal},
                        // {field: 'admin_id', title: __('Admin_id')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        merchantedit: function () {
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
