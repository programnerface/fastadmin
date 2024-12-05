define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    // 确保页面加载完后运行
    $(document).ready(function () {

        $('#one .commonsearch-table').hide()
        $('#one .fixed-table-toolbar').hide()

        $('#third .commonsearch-table').hide()
        $('#third .fixed-table-toolbar').hide()
        // 监听输入框值的实时变化
        $('#order_date-min, #order_date-max,#year-min,#year-max').on('input change blur', function () {
            var startDate = $('#order_date-min').val(); // 获取起始日期的值
            var endDate = $('#order_date-max').val();   // 获取结束日期的值
            var yearstartDate = $('#year-min').val(startDate);   // 获取结束日期的值
            var yearendDate = $('#year-max').val(endDate);   // 获取结束日期的值

            var monthstartDate = $('#month-min').val(startDate);   // 获取结束日期的值
            var monthendDate = $('#month-max').val(endDate);

            console.log("day起始日期:", startDate);
            console.log("day结束日期:", endDate);
            console.log("year起始日期:", yearstartDate);
            console.log("year结束日期:", yearendDate);
            console.log("month起始日期:", monthstartDate);
            console.log("month结束日期:", monthendDate);
        });
        $('#first button[type="submit"]').on('click', function (e) {

            console.log('First button clicked');

            // 手动触发 #one 按钮的点击事件
            $('#one button[type="submit"]').click();
            $('#third button[type="submit"]').click();
        });
    });

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showFooter: true,
            });
            //每日交易统计
            var table = $("#table");
// console.log($.fn.bootstrapTable.defaults.extend.index_url)
            // 初始化表格
            table.bootstrapTable({
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
                toolbar:'#toolbar',
                pk: 'ID',
                sortName: 'ID',
                searchFormVisible: true,

                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID'),operate:false},
                        {field: 'order_date', title: __('Order_date'),  addclass:'datetimepicker',operate: 'BETWEEN', autocomplete:false,data: 'data-date-format="YYYY-MM-DD"'},
                        {field: 'payment_amount', title: __('Payment_amount'), operate:false},
                        {field: 'order_count', title: __('Order_count'),operate:false},
                        {field: 'order_zelle', title: __('Order_zelle'),operate:false},
                        {field: 'order_cash', title: __('Order_cash'),operate:false},
                        {field: 'order_venmo', title: __('Order_venmo'),operate:false},
                        {field: 'order_square', title: __('Order_square'),operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);



            //年度交易统计
            var table2 = $("#table2");
// console.log($.fn.bootstrapTable.defaults.extend.index_url)
            // 初始化表格
            table2.bootstrapTable({
                extend: {
                    index_url: 'year_trade/index' + location.search,
                    add_url: 'year_trade/add',
                    edit_url: 'year_trade/edit',
                    del_url: 'year_trade/del',
                    multi_url: 'year_trade/multi',
                    import_url: 'year_trade/import',
                    table: 'year_trade_table',
                },
                // url: $.fn.bootstrapTable.defaults.extend.index_url,
                // url: 'day_trade/index?addtabs=1',
                url: 'year_trade/index?addtabs=1',
                toolbar:'#toolbarb',
                pk: 'ID',
                sortName: 'ID',
                searchFormVisible: true,

                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        {field: 'year', title: __('年份'),  addclass:'datetimepicker',operate: 'BETWEEN', autocomplete:false,data: 'data-date-format="YYYY-MM-DD"',visible:false},
                        {field: 'order_type', title: __('类型'), operate: 'LIKE',footerFormatter: function (data) {
                                return '合计';
                            }},
                        {field: 'payment_amount', title: __('Payment_amount'), operate:'BETWEEN',footerFormatter: function (data) {
                                var field =this.field
                                var total_sum =data.reduce(function (sum,row) {
                                    return (sum) + (parseFloat(row[field]) || 0);
                                },0);
                                return total_sum.toFixed(2)                            }},
                        {field: 'order_count', title: __('Order_count'),footerFormatter: function (data) {
                                var field =this.field
                                var total_sum =data.reduce(function (sum,row) {
                                    return (sum) + (parseFloat(row[field]) || 0);
                                },0);
                                return total_sum                            }},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table2);

            //每月交易统计
            var table3 = $("#table3");
// console.log($.fn.bootstrapTable.defaults.extend.index_url)
            // 初始化表格
            table3.bootstrapTable({
                extend: {
                    index_url: 'month_trade/index' + location.search,
                    add_url: 'month_trade/add',
                    edit_url: 'month_trade/edit',
                    del_url: 'month_trade/del',
                    multi_url: 'month_trade/multi',
                    import_url: 'month_trade/import',
                    table: 'month_trade_table',
                },
                // url: $.fn.bootstrapTable.defaults.extend.index_url,
                // url: 'day_trade/index?addtabs=1',
                url: 'month_trade/index?addtabs=1',
                toolbar:'#toolbarc',
                pk: 'ID',
                sortName: 'ID',
                searchFormVisible: true,

                columns: [
                    [
                        {checkbox: true},
                        {field: 'ID', title: __('ID')},
                        {field: 'month', title: __('Month'),  addclass:'datetimepicker',operate: 'BETWEEN', table: table, class: 'autocontent', formatter: Table.api.formatter.content,formatter:function (value){
                                var chineseNumbers = ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"];
                                var date = new Date(value);
                                var month = date.getMonth() + 1;
                                var chineseMonth = chineseNumbers[month - 1] + "月";
                                return chineseMonth
                            }},
                        {field: 'payment_amount', title: __('Payment_amount'), operate:'BETWEEN'},
                        {field: 'order_count', title: __('Order_count')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table3);


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
