<?php /*a:1:{s:66:"E:\phpstudy_pro\WWW\think5\application\index\view\login\login.html";i:1596781636;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" href="/static/login/css/login.css">
</head>
<body class="login-box">
    <main>
        <div class="login-left">
            <img src="" alt="">
        </div>
        <form action="<?php echo url('login/dologin'); ?>" method="post">
            <div class="login-right">
                <div class="login-top">
                    hello,
                    <div>欢迎登录</div>
                </div>
                <div class="login-username">
                    <input type="text" placeholder="请输入您的账号" id="username" name="username">
                </div>
                <div class="login-pwd">
                    <input type="password" placeholder="请输入您的密码" id="pwd" name="pwd">
                </div>
                <input type="submit" class="login-btn-login" value="登录">
                <div class="login-bottom">
                    <span style="color: #7e8795">还没有账号？</span>
                    <a href="register.html" class="login-btn-register">免费注册</a>
                </div>
            </div>
        </form>
    </main>
</body>
</html>