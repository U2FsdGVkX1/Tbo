<?php
if(isset($_GET['_list'])){
  
  if (!function_exists("scandir")) {
  exit(json_encode(['code' => 1, 'msg' => "scandir 函数被禁用，将导致不能显示插件列表，请检查 php.ini", 'data' => []]));
  }
  
  $pluginModel = new PluginModel;
  $pluginList = $pluginModel->scan ();
  $pluginEnabledList = $pluginModel->getinfo ();
  $pluginInfo_f = [];
  foreach ($pluginList as $pluginList_d) {
    $pluginInfo = $pluginModel->getinfo_f ($pluginList_d);
    $pluginInfo['PluginName'] = isset($pluginInfo['PluginName']) ? $pluginInfo['PluginName'] : $pluginList_d;
    $pluginInfo['Author'] = isset($pluginInfo['Author']) ? $pluginInfo['Author'] : '-';
    $pluginInfo['Description'] = isset($pluginInfo['Description']) ? $pluginInfo['Description'] : '无描述';
    $pluginInfo['Version'] = isset($pluginInfo['Version']) ? $pluginInfo['Version'] : '';

    $pluginInfo['PluginURL'] = isset($pluginInfo['PluginURL']) ? $pluginInfo['PluginURL'] : '';
    $pluginInfo['AuthorURL'] = isset($pluginInfo['AuthorURL']) ? $pluginInfo['AuthorURL'] : '';
    $pluginInfo['UIarea'] = isset($pluginInfo['UIarea']) ? $pluginInfo['UIarea'] : '640px,640px';
    
    
    $pluginEnabledSub = NULL;
    foreach ($pluginEnabledList as $pluginEnabledList_i => $pluginEnabledList_d) {
      if ($pluginEnabledList_d['pcn'] == $pluginList_d) {
        $pluginEnabledSub = $pluginEnabledList_i;
        break;
      }
    }
    $pluginInfo['lasterror'] = '1970-01-01 08:08:08';
    if ($pluginEnabledSub !== NULL) {
    $lastError = $pluginEnabledList[$pluginEnabledSub]['lasterror'];
    if ($lastError != 0) {
      $pluginInfo['lasterror'] = date ('Y-m-d H:i:s', $lastError);
    }
    }
    
    $pluginInfo['priority'] = isset($pluginEnabledList[$pluginEnabledSub]['priority']) ? $pluginEnabledList[$pluginEnabledSub]['priority'] : '0';
    $pluginInfo['pcn'] = $pluginList_d;

    if ($pluginEnabledSub === NULL) {
    $pluginInfo['status'] = 0;//未安装
    }else{
    if ($pluginEnabledList[$pluginEnabledSub]['enabled'] == 0) {
      $pluginInfo['status'] = 1;//未启用
    } else {
      $pluginInfo['status'] = 2;//已启用
    }
    }
    
    $pluginInfo_f[] = $pluginInfo;
  }
  if(!empty($pluginInfo_f)){
  $pluginlists = ['code' => 0, 'msg' => "", 'data' => $pluginInfo_f];
  }else{
  $pluginlists = ['code' => 1, 'msg' => "插件列表为空", 'data' => []];
  }
  
  //print_r($pluginInfo_f);
  exit(json_encode($pluginlists));
}

$pageTitle = '插件列表'; require_once 'Header.php';

?>
  <div class="layui-fluid">   
  <div class="layui-card">
    <div class="layui-card-header">插件列表</div>
    <div class="layui-card-body">
    <div style="padding-bottom: 10px;">
      <button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal layuiadmin-btn-role" data-type="create">创建插件</button>
      <button class="layui-btn layui-btn-sm layui-btn-radius layuiadmin-btn-role" data-type="install">全部安装</button>
      <button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-danger layuiadmin-btn-role" data-type="uninstall">全部卸载</button>
      <button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-primary layuiadmin-btn-role" data-type="enable">全部启用</button>
      <button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-warm layuiadmin-btn-role" data-type="disable">全部禁用</button>
    </div>
      
    <table id="plugins_table" lay-filter="plugins_table"></table>  
    
    <script type="text/html" id="PluginName">
      {{#  if(d.PluginURL){ }}
      <div><a href="{{ d.PluginURL }}" target="_blank" class="layui-table-link">{{ d.PluginName }}</a> {{ d.Version }}</div>
      {{#  } else { }}
      {{ d.PluginName }} {{ d.Version }}
      {{#  } }}
    </script>
    <script type="text/html" id="Author">
      {{#  if(d.AuthorURL){ }}
      <div><a href="{{ d.AuthorURL }}" target="_blank" class="layui-table-link">{{ d.Author }}</div>
      {{#  } else { }}
      {{ d.Author }}
      {{#  } }}
    </script>
    <script type="text/html" id="status">
      {{# if (d.status == 0) { }}  
        <span class="layui-badge-rim">待安装</span>
      {{# } else if(d.status == 1) { }}  
        <span class="layui-badge layui-bg-cyan">待启用</span>
      {{# } else { }}  
        <span class="layui-badge layui-bg-blue">已启用</span>
      {{# } }}  
    </script>
    
    <script type="text/html" id="toolbar">
      {{# if (d.status == 0) { }}  
      <a class="layui-btn layui-btn-radius layui-btn-xs layuiadmin-btn-role" lay-event="install">&nbsp;&nbsp;安装&nbsp;&nbsp;</a>
      <a class="layui-btn layui-btn-radius layui-btn-xs layui-btn-danger" lay-event="remove">&nbsp;&nbsp;移除&nbsp;&nbsp;</a>
      {{# } else if(d.status == 1) { }}  
      <a class="layui-btn layui-btn-radius layui-btn-xs layui-btn-primary" lay-event="enable">&nbsp;&nbsp;启用&nbsp;&nbsp;</a>
      <a class="layui-btn layui-btn-radius layui-btn-xs layui-btn-danger" lay-event="uninstall">&nbsp;&nbsp;卸载&nbsp;&nbsp;</a>
      {{# } else { }}  
      <a class="layui-btn layui-btn-radius layui-btn-xs" style="background-color: #00CCFF;" lay-event="settings">&nbsp;&nbsp;设置&nbsp;&nbsp;</a>
      <a class="layui-btn layui-btn-radius layui-btn-xs layui-btn-warm" lay-event="disable">&nbsp;&nbsp;禁用&nbsp;&nbsp;</a>
      {{# } }}  
      <a class="layui-btn layui-btn-radius layui-btn-xs layui-btn-normal" lay-event="edit">&nbsp;&nbsp;编辑&nbsp;&nbsp;</a>
    </script>
    
    <script type="text/html" id="createTemp">
      <form>
      <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
        <div class="layui-form-item">
        <label class="layui-form-label">插件PCN</label>
        <div class="layui-input-inline">
          <input type="text" name="pcn" style="width: 400px;" placeholder="只能为英文" autocomplete="off" class="layui-input">
        </div>
        </div>
        <div class="layui-form-item">
        <label class="layui-form-label">插件名称</label>
        <div class="layui-input-inline">
          <input type="text" name="PluginName" style="width: 400px;" placeholder="可以是 百度插件,http://baidu.com 这样的格式" autocomplete="off" class="layui-input">
        </div>
        </div>
        <div class="layui-form-item">
        <label class="layui-form-label">插件作者</label>
        <div class="layui-input-inline">
          <input type="text" name="Author" style="width: 400px;" placeholder="可以是 作者昵称,http://blog.xxx.com 这样的格式" autocomplete="off" class="layui-input">
        </div>
        </div>
        <div class="layui-form-item">
        <label class="layui-form-label">版本号</label>
        <div class="layui-input-inline">
          <input type="text" name="Version" style="width: 400px;" placeholder="" autocomplete="off" class="layui-input" value="1.0">
        </div>
        </div>
        <div class="layui-form-item">
        <label class="layui-form-label">插件描述</label>
        <div class="layui-input-inline">
          <textarea name="Description" style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea"></textarea>
        </div>
        </div>
        <div class="layui-form-item">
        <label class="layui-form-label">设置界面</label>
        <div class="layui-input-inline">
          <input type="text" name="UIarea" style="width: 400px;" placeholder="插件设置界面宽高,支持640px,640px或50%,50%" autocomplete="off" class="layui-input" value="640px,640px">
        </div>
        </div>
      </div>
      </form>
    </script>
    <script type="text/html" id="editTemp">
  <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 20px 20px;">
      <button class="layui-btn layui-btn-sm layui-btn-primary" data-type="edit_php">__PCN__.class.php</button>
      <button class="layui-btn layui-btn-sm layui-btn-primary" data-type="edit_settings">settings.html</button>
  </div>
    </script>
    </div>
  </div>
  </div>

 <script src="../Templates/assets/layui/layui.js"></script>  
  <script>
  
  layui.use(['table'], function() {
    var $ = layui.jquery, table = layui.table;
  
  function sendPost(url,data){
    $.ajax({type: "POST",url: url,data:data,dataType: "json",
    success: function(result, textStatus, jqXHR){
      if(result.code == '0' && textStatus == 'success'){
      layer.msg('操作成功',{icon: 1});
      table.reload('plugins_table');
      }else{
      layer.msg('接口响应:' + textStatus, {icon: 1}, function(){});
      }
    },
    });
    return false;
  };
  
  function editPlugins(name,file){
    var index = layer.open({
    type: 2
    ,title: '编辑 [' + name + ']'
    ,content: 'PluginMain/edit/' + file
    ,btn: ['保存', '关闭']
    ,yes: function(index, layero){
      var submit = layero.find('iframe').contents().find("#save_pcn");
      submit.click();
    }
    });
    layer.full(index);
  };
  
  $.fn.serializeJson = function () {
    var serializeObj = {};
    $(this.serializeArray()).each(function () {
    serializeObj[this.name] = this.value;
    });
    return serializeObj;
  };
  
  table.render({
    elem: "#plugins_table",
    url: 'Plugins?_list',
    cols: [[
      //{type: "checkbox",align: "left"},
      {field: "PluginName",title: "插件名称",width: 220,templet: '#PluginName'},
      {field: "Author",title: "插件作者",width: 150,templet: '#Author'},
      {field: "Description",title: "插件描述",minWidth: 250,},
      {field: "priority",title: "优先级",edit:'text',width: 80,sort: true},
      {field: "status",title: "插件状态",width: 100,templet: '#status'},
      {field: "lasterror",title: "最后崩溃",width: 170,sort: true},
      {title: "操作",width: 190,align: "center",fixed: "right",toolbar: "#toolbar"}
      ]],
    text: "插件列表为空！"
    });
    
    //监听单元格编辑
    table.on('edit(plugins_table)', function(obj){
      var pcn = obj.data.pcn,status = obj.data.status,newPriority = obj.value;
      //console.log(obj);
      if(status !== 2){
      table.reload('plugins_table');
      return layer.msg('请先启用插件', {icon: 2});
      }
      sendPost('plugins/priority',{pcn:pcn,newPriority:newPriority});
    });
    
    //监听操作按钮
    table.on('tool(plugins_table)', function(obj){
    var data = obj.data;
    var event = obj.event;
    if(event == 'remove'){
    layer.confirm('确定删除该插件全部文件吗？', function(index) {
      sendPost('plugins/' + event,{pcn:data.pcn});
    });
    }else if(event == 'edit'){
    var file = '';
    layer.confirm('请选择[' + data.PluginName +']需要编辑的文件', {
      btn: [data.pcn + '.class.php','settings.html']
    }, function(){
      
      editPlugins(data.PluginName, data.pcn + '/' + '.class.php');
      layer.closeAll('dialog');
    }, function(){
      editPlugins(data.PluginName, data.pcn + '/' + 'settings.html'); 
    });


    }else if(event == 'settings'){
      $.ajax({type: "POST",url: 'plugins/' + event,data:{pcn:data.pcn},
        success: function(result, textStatus, jqXHR){
        if(textStatus == 'success'){
          if(!result) return layer.msg('该插件无设置界面', {icon: 2});
          var index = layer.open({
          type: 1
          ,title: data.PluginName + ' 设置'
          ,content: result.replace('__PCN__',data.pcn)
          ,maxmin: true
          ,area: data.UIarea.split(',')
          ,btn: ['保存', '取消']
          ,yes: function(index, layero){
            var submit = layero.find("#saveSettings");
            submit.click();
          }
          });
        }else{
          layer.msg('接口响应:' + textStatus, {icon: 2});
        }
        },
      });

    }else{
    sendPost('plugins/' + event,{pcn:data.pcn});
    }
  });
  
  var active = {
    edit_php: function(){
    layui.msg(edit_php)
    },
    
    install: function(){
    sendPost('plugins/installAll',{});
    },
    
    uninstall: function(){
    sendPost('plugins/uninstallAll',{});
    },

    enable: function(){
    sendPost('plugins/enableAll',{});
    },

    disable: function(){
    sendPost('plugins/disableAll',{});
    },

    create: function(){
      var index = layer.open({
      type: 1
      ,title: '初始插件信息'
      ,content: $("#createTemp").html()
      ,area: ['550px', '550px']
      ,btn: ['下一步', '取消']
      ,yes: function(index, layero){
      var data = layero.find("form").serializeJson();
      var __PCN__ = data.pcn,
        __PluginName__ = data.PluginName,
        __PluginURL__ = '',
        __Author__ = data.Author,
        __AuthorURL__ = '',
        __Version__ = data.Version,
        __Description__ = data.Description,
        __UIarea__ = data.UIarea;
        
      if(!/^[a-z]+$/ig.test(__PCN__)){
        return layer.msg('插件PCN只能是英文字符!!!', {icon: 2});
      }
      if(__PluginName__ === ''){
        return layer.msg('插件名称不能为空!!!', {icon: 2});
      }

      if(__PluginName__.indexOf(",") != -1){
        __PluginName__ = data.PluginName.split(',')[0];
        __PluginURL__ = data.PluginName.split(',')[1];
      }
      
      if(__Author__.indexOf(",") != -1){
        __Author__ = data.Author.split(',')[0];
        __AuthorURL__ = data.Author.split(',')[1];
      }
      
      $.get('PluginCreate',function(result,textStatus){
        if(textStatus == 'success'){
           let code = result.replace(/(__PCN__|__PluginName__|__PluginURL__|__Author__|__AuthorURL__|__Version__|__Description__|__UIarea__)/gi, function ($0, $1) {
            return {
            "__PCN__": __PCN__,
            "__PluginName__": __PluginName__,
            "__PluginURL__": __PluginURL__,
            "__Author__": __Author__,
            "__AuthorURL__": __AuthorURL__,
            "__Version__": __Version__,
            "__Description__": __Description__,
            "__UIarea__": __UIarea__,
            }[$1];
          });
          var index = layer.open({
          type: 1
          ,title: '创建插件 [' + __PCN__ + '.class.php]'
          ,content: code
          ,maxmin: true
          ,area: ['80%','80%']
          ,btn: ['确定', '取消']
          ,yes: function(index, layero){
            var submit = layero.find("#createSub");
            submit.click();
          }
          });
          layer.full(index);
        }else{
          layer.msg('接口响应:' + textStatus, {icon: 2});
        }
      });
      }
    });
  },


  }
  $('.layui-btn.layuiadmin-btn-role').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
  });
  </script>
<?php require_once 'Footer.php' ?>