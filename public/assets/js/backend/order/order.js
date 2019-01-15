define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index',
                    add_url: 'order/order/add',
                    edit_url: 'order/order/edit',
                    verify_url: 'order/order/verify',
                    del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    table: 'order',
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
                        // {field: 'itemid', title: __('Itemid'),visible: false},
                        {field: 'item', title: __('Item')},
                        {field: 'order', title: __('Order')},
                        // {field: 'payerid', title: __('Payerid'),visible: false},
                        {field: 'payer', title: __('Payer')},
                        // {field: 'referid', title: __('Referid'),visible: false},
                        {field: 'refer', title: __('Refer')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',visible: false},
                        {field: 'pay', title: __('Pay'), operate:'BETWEEN'},
                        {field: 'rate', title: __('Rate'), operate:'BETWEEN'},
                        {field: 'shares', title: __('Shares'), operate:'BETWEEN'},
                        {field: 'description', title: __('Description'),visible: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4')}, formatter: Table.api.formatter.status,visible: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh'),visible: false},
                        {
                            field: 'operate', 
                            title: __('Operate'), 
                            table: table, 
                            events: Table.api.events.operate, 
                            buttons:[
                                {
                                    name: 'verify',
                                    title: __('订单审核'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-check',
                                    url: 'order/order/verify',
                                    hidden:function(row){
                                        if(row.status >= 1){
                                            return true;
                                        }
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            //审核
            // $(document).on("click", ".btn-verify", function () {
            //     alert(1);
            //     table.bootstrapTable('getSelections');
            //     Backend.api.open('order/order/verify/ids/' + $(this).data('id'), __('Verify'));
            // });
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