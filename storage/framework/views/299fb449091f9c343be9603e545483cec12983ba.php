<!DOCTYPE html>
<html lang="ZH-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登录系统</title>
    <link rel="stylesheet" href="admin/plugins/layui/css/layui.css">
    <link rel="stylesheet" href="admin/src/css/login.css">
</head>

<body>
<div class="kit-login">
    <div class="kit-login-bg"></div>
    <div class="kit-login-wapper">
        <h2 class="kit-login-slogan"> <br></h2>
        <div class="kit-login-form">
            <h4 class="kit-login-title">欢迎登录</h4>
            <form class="layui-form">
                <div class="kit-login-row">
                    <div class="kit-login-col">
                        <i class="layui-icon">&#xe612;</i>
                        <span class="kit-login-input">
                <input type="text" name="loginName" lay-verify="required" placeholder="用户名" />
              </span>
                    </div>
                    <div class="kit-login-col"></div>
                </div>
                <div class="kit-login-row">
                    <div class="kit-login-col">
                        <i class="layui-icon">&#xe64c;</i>
                        <span class="kit-login-input">
                <input type="password" name="password" lay-verify="required" placeholder="密码" />
              </span>
                    </div>
                    <div class="kit-login-col"></div>
                </div>





                <div class="kit-login-row">
                    <button class="layui-btn kit-login-btn" lay-submit="submit" lay-filter="login_hash">登录系统</button>
                </div>
                <div class="kit-login-row" style="margin-bottom:0;">
                    <a href="javascript:;" style="color: rgb(153, 153, 153); text-decoration: none; font-size: 13px;" id="forgot">忘记密码</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="admin/plugins/polyfill.min.js"></script>
<script src="admin/plugins/layui/layui.js"></script>
<script>
    layui.use(['layer', 'form'], function() {
        var form = layui.form,
            $ = layui.jquery;

        $('#forgot').on('click', function() {
            layer.msg('请联系管理员.');
        });
        //监听提交
        $('.kit-login-btn').click(function(data) {
            var username = $('input[name="loginName"]').val();
            var password = $('input[name="password"]').val();
            $.ajax({
                url:'/admin/login',
                type:'post',
                dataType:'json',
                data:{
                    username:username,
                    password:password
                },
                success:function(result){
                    if (result.type == 'ok'){
                        window.location.href="/winadmin/index";
                    }else{
                        layer.msg(result.message)
                    }

                }
            });
            return false;
        // form.on('submit(login_hash)', )
            // layer.msg(JSON.stringify(data.field));
            // setTimeout(function(){
            //     location.href='/';
            // },1000);
            // return false;
        });
    });
</script>
</body>

</html>
