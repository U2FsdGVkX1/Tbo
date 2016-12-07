<?php
    $code = file_get_contents (APP_PATH . '/Plugins/' . $this->param[0] . '/' . $this->param[0] . '.class.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <title>设置</title>
        <?php require_once 'Header.php' ?>
        <style>
            .code {
                width: 100%;
            }
        </style>
    </head>
    <body>
        <?php require_once 'Sidebar.php' ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12" style="margin-top: 10px">
                    <textarea id="code" class="code" rows="50"><?php echo $code ?></textarea>
                </div>
                <div class="col-xs-12">
                    <button id="save" style="float: right" type="button" class="btn btn-success">编辑</button>
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
                    url: "<?php echo APP_URL ?>/index.php/PluginMain/ajaxSave",
                    data: {
                        "pcn": "<?php echo $this->param[0] ?>",
                        "code": $("textarea#code").val()
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