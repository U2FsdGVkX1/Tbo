<?php $pageTitle = '登 入'; require_once 'Header.php' ?>
  <div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">

    <div class="layadmin-user-login-main">
      <div class="layadmin-user-login-box layadmin-user-login-header">
        <h2>TboAdmin</h2>
        <p><?php echo !FASTLOGIN ? '请输入密码登入管理后台' : '你已开启快速登录，请在 Telegram 授权后完成登录'; ?></p>
      </div>
      <?php if (!FASTLOGIN) { ?>
      <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
        <div class="layui-form-item">
          <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
          <input type="password" name="password" id="LAY-user-login-password" lay-verify="required" placeholder="密码" class="layui-input">
        </div>
        <div class="layui-form-item">
          <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="LAY-user-login-submit">登 入</button>
        </div>
      </div>
      <?php } ?>
      
    </div>
    
    <div class="layui-trans layadmin-user-login-footer">
      <p>##</p>
    </div>
  </div>

  <script src="../../Templates/assets/layui/layui.js"></script>  
  <script>
  layui.config({
    base: '../../Templates/assets/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index', 'user'], function(){
    var $ = layui.$
    ,setter = layui.setter
    ,admin = layui.admin
    ,form = layui.form
    ,router = layui.router()
    ,search = router.search;

    form.render();
    <?php if (!FASTLOGIN) { ?>
    //提交
    form.on('submit(LAY-user-login-submit)', function(obj){
      layer.msg('正在登入');
      $.ajax({
        type: "POST",
        url: "<?php echo APP_URL ?>/index.php/login/ajaxLogin",
        data: obj.field,
        dataType: "json",
        success: function(data, textStatus, jqXHR){
          if(data.code == '0'){
            layer.msg('登入成功', {icon: 1},function(){
              location.href = "<?php echo APP_URL ?>/index.php";
            });
          }else{
            layer.msg(data.msg, {icon: 2});
          }
        },
      });
    });
    <?php } else { ?>
    $.ajax({
        type: "POST",
        url: "<?php echo APP_URL ?>/index.php/login/fastLogin",
        dataType: "json",
        success: function(data, textStatus, jqXHR){
          if(data.code == '0'){
            layer.msg('请在 Telegram 完成授权');
            var tt = setInterval(function(){
              $.ajax({
                type: "POST",
                url: "<?php echo APP_URL ?>/index.php/login/fastLoginVerify",
                success: function(data, textStatus, jqXHR){
                  if(data.code == '0'){
                    layer.msg('登入成功', {icon: 1},function(){
                      location.href = "<?php echo APP_URL ?>/index.php";
                    });
                  }else{
                    layer.msg(data.msg, {icon: 2});
                    window.clearInterval(tt);
                  }
                },
                dataType: "json"
              });
            }, 2000);
          }
        }
    });
    <?php } ?>
  });
  </script>
<?php require_once 'Footer.php' ?>