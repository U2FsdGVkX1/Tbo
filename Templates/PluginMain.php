<?php
if($this->param[1] == 'settings.html'){
  $file = $this->param[1];
  $mode = 'html';
}else{
  $file = $this->param[0] . $this->param[1];
  $mode = 'php';
}
$file = APP_PATH . '/Plugins/' . $this->param[0] . '/' . $file;
$code = (file_exists($file)) ? file_get_contents ($file) : '该插件木有 settings.html 文件' . PHP_EOL . '修改本文件将新建 settings.html';
$code = htmlspecialchars ($code);
?>
  <div id="code"><?php echo $code ?></div>
  <input style="display:none" lay-submit lay-submit lay-filter="save_pcn" id="save_pcn" value="编辑">
  <style>
    #code {
      position: absolute;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
    }
  </style>
  <script src="/Templates/assets/layui/layui.js"></script>
  <script src="/Templates/assets/ace/ace.js"></script>
  <script>
  var timer = setInterval(function(){
    if(typeof(ace) != "undefined"){
      clearInterval(timer);

      layui.use(['layer','form'], function() {
        var $ = layui.jquery, form = layui.form;
        var editor = ace.edit("code");
        editor.getSession().setMode("ace/mode/<?php echo $mode ?>");
        editor.setTheme("ace/theme/monokai");
        editor.setFontSize(16);
        editor.setShowPrintMargin(false);
        
        form.on('submit(save_pcn)', function(data){
          var index = parent.layer.getFrameIndex(window.name);
          parent.layer.msg('更新中...');
          $.ajax({
            type: "POST",
            url: "<?php echo APP_URL ?>/index.php/PluginMain/ajaxSave",
            dataType: "json",
            data: {
              "pcn": "<?php echo $this->param[0] ?>",
              "file": "<?php echo $this->param[1] ?>",
              "code": editor.getValue()
            },
            success: function(data, textStatus, jqXHR){
              if(data.code == '0'){
                parent.layer.msg('保存成功', {icon: 1});
                parent.layui.table.reload('plugins_table');
                parent.layer.close(index);
              }
            }
          });
        });
      })
    }
  }, 500);
  </script>


