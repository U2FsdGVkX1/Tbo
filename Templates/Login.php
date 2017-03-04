<?php
    $pjax = isset ($_SERVER['HTTP_X_PJAX']);
    if ($pjax == true) {
        ?>
        <script>
            location.reload();
        </script>
        <?php
        exit ();
    }
?>
<?php if ($pjax == false) require_once 'Header1.php' ?>
<title>登录</title>
<?php if ($pjax == false) require_once 'Header2.php' ?>
<?php if ($pjax == false) echo '<div class="container" id="container">' ?>

<div class="row">
    <div class="loginBox">
        <div class="col-xs-12">
            <?php
                if (!FASTLOGIN) {
                    ?>
                        <div class="input-group">
                            <span class="input-group-addon" id="loginBox-password">密码</span>
                            <input id="loginBox-password" type="password" class="form-control" aria-describedby="loginBox-password">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="button" id="login">Go</button>
                            </span>
                        </div>
                    <?php
                } else {
                    ?>
                        你已开启快速登录，请在 Telegram 授权后完成登录
                    <?php
                }
            ?>
        </div>
    </div>
</div>
<style>
    .loginBox {
        width: 400px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
<script>
    <?php
        if (!FASTLOGIN) {
            ?>
                $("button#login").click(function(){
                    buttonThis = $(this);
                    $(buttonThis).attr("disabled", "disabled");
                    $.ajax({
                        type: "POST",
                        url: "<?php echo APP_URL ?>/index.php/login/ajaxLogin",
                        data: {
                            "password": $("input#loginBox-password").val()
                        },
                        success: function(data, textStatus, jqXHR){
                            if(data.code == '0'){
                                location.href = "<?php echo APP_URL ?>/index.php";
                            }else{
                                textOld = $(buttonThis).text();
                                $(buttonThis).text(data.msg);
                                setTimeout(function(){
                                    $(buttonThis).text(textOld);
                                    $(buttonThis).removeAttr("disabled");
                                }, 2000);
                            }
                        },
                        dataType: "json"
                    });
                });
            <?php
        } else {
            ?>
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/login/fastLogin",
                    success: function(data, textStatus, jqXHR){
                        if(data.code == '0'){
                            setInterval(function(){
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo APP_URL ?>/index.php/login/fastLoginVerify",
                                    success: function(data, textStatus, jqXHR){
                                        if(data.code == '0'){
                                            location.href = "<?php echo APP_URL ?>/index.php";
                                        }
                                    },
                                    dataType: "json"
                                });
                            }, 2000);
                        }
                    },
                    dataType: "json"
                });
            <?php
        }
    ?>
    
</script>

<?php if ($pjax == false) echo '</div>' ?>
<?php if ($pjax == false) require_once 'Footer.php' ?>