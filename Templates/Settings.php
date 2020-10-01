<?php $pageTitle = '对接设置'; require_once 'Header.php'; ?>
  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
      <div class="layui-col-md12">
        <div class="layui-card">
          <div class="layui-card-header">对接设置</div>
          <div class="layui-card-body" pad15>
            
            <div class="layui-form" wid100 lay-filter="">
<!--              <fieldset class="layui-elem-field layui-field-title"><legend style="color:#FF6633">Telegram Bot 设定</legend></fieldset> -->
              <div class="layui-form-item">
                <label class="layui-form-label">Bot Name</label>
                <div class="layui-input-inline" style="width: 45%;">
                  <input type="text" name="botName" id="botName" class="layui-input" placeholder="机器人的 Username（不是显示出来的名称" value="<?php echo BOTNAME ?>">
                </div>
                <div class="layui-input-inline layui-input-company"><button type="button" id="getUsername" class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal">自动获取</button></div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">Bot Token</label>
                <div class="layui-input-inline" style="width: 45%;">
                  <input type="text" name="botToken" id="botToken" class="layui-input" placeholder="机器人のToken" value="<?php echo TOKEN ?>">
                </div>
                <div class="layui-input-inline layui-input-company"><button type="button" id="setWebhook" class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal">重置回调</button></div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">Master ID</label>
                <div class="layui-input-inline">
                  <input type="text" name="master" class="layui-input" placeholder="主人ID" value="<?php echo MASTER ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">可通过启用 Whoami 插件之后向机器人发送 /whoami 获取</div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">Fast Login</label>
                <div class="layui-input-inline">
                  <input type="checkbox" name="fastLogin" lay-skin="switch" lay-text="TRUE|FALSE" <?php if (FASTLOGIN) echo 'checked' ?>>
                </div>
                <div class="layui-form-mid layui-word-aux">是否开启快速登入</div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">Debug Modle</label>
                <div class="layui-input-inline">
                  <input type="checkbox" name="debug" lay-skin="switch" lay-text="TRUE|FALSE" <?php if (DEBUG) echo 'checked' ?>>
                </div>
                <div class="layui-form-mid layui-word-aux">开启此选项将会在出现错误或异常时发送报告</div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">Login Paswd</label>
                <div class="layui-input-inline">
                  <input type="text" name="password" class="layui-input" placeholder="登入密码">
                </div>
                <div class="layui-form-mid layui-word-aux">非空则重置登入密码</div>
              </div>
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <button class="layui-btn" lay-submit lay-filter="api_submit">确认保存</button>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../Templates/assets/layui/layui.js"></script>  
  <script>
	layui.use(['layer','form'], function() {
		var $ = layui.jquery, form = layui.form;
    
    form.on('submit(api_submit)', function(obj){
      layer.msg('保存中');
      obj.field.debug = (obj.field.debug == 'on').toString();
      obj.field.fastLogin = (obj.field.fastLogin == 'on').toString();
      $.ajax({
        type: "POST",
        url: "<?php echo APP_URL ?>/index.php/settings/ajaxSave",
        data: obj.field,
        dataType: "json",
        success: function(data, textStatus, jqXHR){
          if(data.code == '0'){
            layer.msg('保存成功', {icon: 1});
          }else{
            layer.msg(data.msg, {icon: 2});
          }
        },
      });
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
  
  
  </script>

<?php require_once 'Footer.php' ?>