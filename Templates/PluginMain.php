<?php
    $code = file_get_contents (APP_PATH . '/Plugins/' . $this->param[0] . '/' . $this->param[0] . '.class.php');
    $code = htmlspecialchars ($code);
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <title>设置</title>
        <?php require_once 'Header.php' ?>
        <style>
            #code {
                position: absolute;
                top: 0px;
                left: 0px;
                width: 100%;
                height: 750px;
            }
        </style>
    </head>
    <body>
        <?php require_once 'Sidebar.php' ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12" style="margin-top: 10px">
                    <div id="code"><?php echo $code ?></div>
                </div>
                <div class="col-xs-12" style="margin-top: 760px">
                    <button id="save" style="float: right" type="button" class="btn btn-success">编辑</button>
                </div>
            </div>
        </div>
        <?php require_once 'Footer.php' ?>
        <script src="<?php echo $this->loadSource ("assets/ace/ace.js") ?>" type="text/javascript" charset="utf-8"></script>
        <script>
            var editor = ace.edit("code");
            editor.getSession().setMode("ace/mode/php");
            editor.setTheme("ace/theme/clouds");
            editor.setFontSize(16);
            editor.setShowPrintMargin(false);
            
            $("button#save").click(function(){
                buttonThis = $(this);
                $(buttonThis).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/PluginMain/ajaxSave",
                    data: {
                        "pcn": "<?php echo $this->param[0] ?>",
                        "code": editor.getValue()
                    },
                    success: function(data, textStatus, jqXHR){
                        if(data.code == '0'){
                            textOld = $(buttonThis).text();
                            $(buttonThis).text("已保存");
                            setTimeout(function(){
                                $(buttonThis).text(textOld);
                                $(buttonThis).removeAttr("disabled");
                            }, 2000);
                        }
                    },
                    dataType: "json"
                });
            })
        </script>
    </body>
</html>