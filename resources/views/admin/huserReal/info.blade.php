@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">用户手机号或邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="account" autocomplete="off" placeholder="" class="layui-input" value="{{$result->account}}" disabled>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-block">
                <input type="text" name="email" autocomplete="off" placeholder="" class="layui-input" value="{{$result->name}}" disabled>
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">护照号码</label>
            <div class="layui-input-block">
                <input type="text" name="passport_id" autocomplete="off" placeholder="" class="layui-input" value="{{$result->passport_id}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">驾驶证号码</label>
            <div class="layui-input-block">
                <input type="text" name="jiashi_id" autocomplete="off" placeholder="" class="layui-input" value="{{$result->jiashi_id}}">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">手持证件照</label>
            <div class="layui-input-block">

                <img src="@if(!empty($result->front_pic)){{$result->front_pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->front_pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">

            </div>
        </div>
         <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">护照正面照</label>
            <div class="layui-input-block">

                <img src="@if(!empty($result->passport_pic)){{$result->passport_pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->reverse_pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">

            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">手持护照</label>
            <div class="layui-input-block">

                <img src="@if(!empty($result->passport_hand_pic)){{$result->passport_hand_pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->reverse_pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">

            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">驾驶证正面</label>
            <div class="layui-input-block">

                <img src="@if(!empty($result->jiashi_pic)){{$result->jiashi_pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->reverse_pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">

            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">手持驾驶证</label>
            <div class="layui-input-block">

                <img src="@if(!empty($result->jiashi_hand_pic)){{$result->jiashi_hand_pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->reverse_pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">

            </div>
        </div>


    </form>

@endsection

