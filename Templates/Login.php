<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <title>登录</title>
        <?php require_once 'Header.php' ?>
        <style>
            .loginBox {
                width: 380px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="loginBox">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="input-group">
                            <span class="input-group-addon" id="loginBox-password">密码</span>
                            <input id="loginBox-password" type="password" class="form-control" aria-describedby="loginBox-password">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="button" id="login">Go</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'Footer.php' ?>
        <script>
            buttonId = "button#login";
            
            $(buttonId).click(function(){
                $(buttonId).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/login/ajaxLogin",
                    data: {
                        "password": $("input#loginBox-password").val()
                    },
                    success: function(data, textStatus, jqXHR){
                        if(data.code == '0'){
                            Cookies.set("password", $("input#loginBox-password").val());
                            location.href = "<?php echo APP_URL ?>/index.php";
                        }else{
                            textOld = $(buttonId).text();
                            $(buttonId).text(data.msg);
                            setTimeout(function(){
                                $(buttonId).text(textOld);
                                $(buttonId).removeAttr("disabled");
                            }, 2000);
                        }
                    },
                    dataType: "json"
                });
            });
        </script>
    </body>
</html>