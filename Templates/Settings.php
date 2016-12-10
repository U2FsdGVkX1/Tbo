<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <title>设置</title>
        <?php require_once 'Header.php' ?>
    </head>
    <body>
        <?php require_once 'Sidebar.php' ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12" style="margin-top: 10px">
                    <div class="input-group">
                        <span class="input-group-addon" id="botName">Bot 名称</span>
                        <input id="botName" type="text" class="form-control" aria-describedby="botName" value="<?php echo BOTNAME ?>">
                        <span class="input-group-btn">
                            <button id="getUsername" class="btn btn-secondary" type="button">自动获取 Bot 名称</button>
                        </span>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="botToken">Token</span>
                        <input id="botToken" type="text" class="form-control" aria-describedby="botToken" value="<?php echo TOKEN ?>">
                        <span class="input-group-btn">
                            <button id="setWebhook" class="btn btn-secondary" type="button">重新设置 Bot 回调</button>
                        </span>
                    </div>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="password">管理员密码</span>
                        <input id="password" type="text" class="form-control" aria-describedby="password">
                    </div>
                    <br>
                    <label class="custom-control custom-checkbox">
                        <input id="debug" type="checkbox" class="custom-control-input" <?php if (DEBUG) echo 'checked' ?>>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">开启 Debug 功能</span>
                    </label>
                    <div id="debugAlert" <?php if (!DEBUG) echo 'style="display: none"' ?> class="alert alert-info" role="alert">
                        <strong>勾选此选项将会在出现错误或异常时发送报告</strong>
                        <br>
                        <br>
                        PS：要取得错误报告需要<del>先肛了果果</del>加入这个<a href="https://telegram.me/Tencent_QQ" target="_blink">群组</a>
                    </div>
                    <br>
                    <button id="save" style="float: right" type="button" class="btn btn-success">确定</button>
                </div>
            </div>
        </div>
        <?php require_once 'Footer.php' ?>
        <script>
            $("button#save").click(function(){
                buttonThis = $(this);
                $(buttonThis).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/settings/ajaxSave",
                    data: {
                        "botName": $("input#botName").val(),
                        "botToken": $("input#botToken").val(),
                        "password": $("input#password").val(),
                        "debug": $("input#debug").prop("checked")
                    },
                    success: function(data, textStatus, jqXHR){
                        if(data.code == '0'){
                            textOld = $(buttonThis).text();
                            $(buttonThis).text("设置已保存");
                            setTimeout(function(){
                                $(buttonThis).text(textOld);
                                $(buttonThis).removeAttr("disabled");
                            }, 2000);
                        }
                    },
                    dataType: "json"
                });
            });
            $("button#setWebhook").click(function(){
                buttonThis = $(this);
                $(buttonThis).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/settings/setWebhook",
                    data: {
                    	"botToken": $("input#botToken").val()
                    },
                    success: function(data, textStatus, jqXHR){
                        textOld = $(buttonThis).text();
                        if(data.code == '0'){
                            $(buttonThis).text("已 Reset Webhook");
                        }else{
                            $(buttonThis).text("失败：" + data.msg);
                        }
                        setTimeout(function(){
                            $(buttonThis).text(textOld);
                            $(buttonThis).removeAttr("disabled");
                        }, 2000);
                    },
                    dataType: "json"
                });
            });
            $("button#getUsername").click(function(){
                buttonThis = $(this);
                $(buttonThis).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/settings/getUsername",
                    data: {
                    	"botToken": $("input#botToken").val()
                    },
                    success: function(data, textStatus, jqXHR){
                        textOld = $(buttonThis).text();
                        if(data.code == '0'){
                            $("input#botName").val(data.username);
                        }else{
                            $(buttonThis).text("失败：" + data.msg);
                        }
                        setTimeout(function(){
                            $(buttonThis).text(textOld);
                            $(buttonThis).removeAttr("disabled");
                        }, 2000);
                    },
                    dataType: "json"
                });
            });
            $("input#debug").change(function(){
                $("div#debugAlert").fadeToggle();
            })
        </script>
    </body>
</html>