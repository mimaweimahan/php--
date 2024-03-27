@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">

        <div class="layui-form-item">
            <label class="layui-form-label">交易币</label>
            <div class="layui-input-inline">
                <div id="currency_ids">

                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">法币</label>
            <div class="layui-input-inline">
                <select name="legal_id" lay-filter="">
                    <option value=""></option>
                    @if(!empty($currencies))
                    @foreach($legals as $legal)
                        <option value="{{$legal->id}}" @if($legal->id == $result->legal_id) selected @endif>{{$legal->name}}</option>
                    @endforeach
                        @endif
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display: none;">
            <label class="layui-form-label">卖</label>
            <div class="layui-input-inline">
                <select name="sell" lay-filter="">
                    <option value="1" @if($result->sell == '1') selected @endif>开启</option>
                    <option value="0" @if($result->sell == '0') selected @endif>关闭</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display: none;">
            <label class="layui-form-label">买</label>
            <div class="layui-input-inline">
                <select name="buy" lay-filter="">
                    <option value="1" @if($result->buy == '1') selected @endif>开启</option>
                    <option value="0" @if($result->buy == '0') selected @endif>关闭</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每次价格下限（按市价浮动百分比）%</label>
            <div class="layui-input-inline">
                <input type="text" name="number_min" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="@if(!empty($result->min)){{$result->min}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每次价格上限（按市价浮动百分比）%</label>
            <div class="layui-input-inline">
                <input type="text" name="number_max" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="@if(!empty($result->max)){{$result->max}}@endif">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">成交频率(秒)</label>
            <div class="layui-input-inline">
                <input type="text" name="second" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="@if(!empty($result->second)){{$result->second}}@endif">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">挂单时长(分钟)</label>
            <div class="layui-input-inline">
                <input type="text" name="mult" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="@if(!empty($result->mult)){{$result->mult}}@endif">
            </div>
        </div>

        <input type="hidden" name="id" value="@if(!empty($result->id)){{$result->id}}@endif">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script src="/js/xm-select.js"></script>
    <script>


        layui.use(['form','laydate'],function () {
            let currencys;
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);

            currencys = xmSelect.render({
                el: '#currency_ids',
                filterable: true,
                language: 'zn',
                data: <?php echo json_encode($currencies)?>
            })

            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                data.currency_ids=currencys.getValue('valueStr');
                $.ajax({
                    url:'{{url('admin/robote/add')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection
