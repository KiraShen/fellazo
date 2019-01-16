define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/report/daily/index',
                    add_url: 'finance/report/daily/add',
                    edit_url: 'finance/report/daily/edit',
                    del_url: 'finance/report/daily/del',
                    multi_url: 'finance/report/daily/multi',
                    table: 'report_daily',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'report', title: __('Report')},
                        {field: 'ordersum', title: __('Ordersum')},
                        {field: 'orderincome', title: __('Orderincome'), operate:'BETWEEN'},
                        {field: 'sharessum', title: __('Sharessum')},
                        {field: 'sharesamount', title: __('Sharesamount')},
                        {field: 'payinterest', title: __('Payinterest'), operate:'BETWEEN'},
                        {field: 'payreward', title: __('Payreward'), operate:'BETWEEN'},
                        {field: 'startdate', title: __('Startdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'finishdate', title: __('Finishdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh')},
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