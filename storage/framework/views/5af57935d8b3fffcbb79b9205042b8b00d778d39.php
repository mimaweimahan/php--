<?php $__env->startSection('page-head'); ?>
    <style type="text/css">

        table.gridtable {
            width:100%;
            font-family: verdana,arial,sans-serif;
            font-size:11px;
            color:#333333;
            border-width: 1px;
            border-color: #666666;
            border-collapse: collapse;
        }
        table.gridtable th {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #dedede;
        }
        table.gridtable td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #ffffff;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
    <table class="gridtable" id="userlist" lay-filter="userlist">
        <thead>
        <th>币种id</th>
        <th>币种</th>
        <th>入金</th>
        <th>出金</th>
        <th>现货余额</th>
        <th>杠杆余额</th>
        <th>秒交易余额</th>
        <th>持仓资金</th>
        <th>持仓手数</th>
        </thead>
        <tbody></tbody>
    </table>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

    <script type="text/javascript">
        $(function(){
            var user_id = $("input[name='user_id']").val();

            $.getJSON("<?php echo e(url('/agent/users_wallet_total')); ?>?user_id=" + user_id,function(obj){
                obj.data.forEach(x=>{
                    $('#userlist tbody').append(`
                <tr>
                <td>${x.id}</td>
                <td>${x.name}</td>
                <td>${x._ru}</td>
                <td>${x._chu}</td>
                <td>${x.change}</td>
                <td>${x.lever}</td>
                <td>${x.micro}</td>
                <td>${x._caution_money}</td>
                <td>${x.currency}</td>
                </tr>`);
                });
            });
        });
        window.onload = function() {

            layui.use(['element', 'form', 'layer', 'table'], function () {


                var form = layui.form;

                function tbRend(url) {
                    table.render({
                        elem: '#userlist'
                        ,url: url
                        ,page: true
                        ,limit: 20
                        ,toolbar: true
                        ,totalRow: true
                        ,height: 'full-100'
                        ,cols: [[
                            {field: 'id', title: '币种id', width: 70}
                            ,{field: 'name', title: '币种', width: 100, totalRowText: '小计'}

                            ,{field: '_ru', title: '入金', width: 150, totalRow: true}
                            ,{field: '_chu', title: '出金', width: 150, totalRow: true}
                            ,{field: '_caution_money', title: '持仓资金', width: 150, totalRow: true}

                        ]]
                    });
                }

                // tbRend();

            });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('agent.layadmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>