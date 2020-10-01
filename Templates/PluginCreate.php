<?php
    define ('DEFAULT_CODE', '<?php
/*
-----BEGIN INFO-----
@PluginName __PluginName__
@Author __Author__
@Version __Version__
@Description __Description__
@PluginURL __PluginURL__
@AuthorURL __AuthorURL__
@UIarea __UIarea__
-----END INFO-----
*/

class __PCN__ extends Base {
    private $data = array ();

    public function __construct ($data) {
        $this->data = $data;
        parent::__construct ();
    }
    public function init ($func, $from, $chat, $date) {

    }
    
    public function command ($command, $param, $message_id, $from, $chat, $date) {

    }
    
    public function message ($message, $message_id, $from, $chat, $date) {

    }
    
    public function sticker ($sticker, $message_id, $from, $chat, $date) {

    }
    public function photo ($photo, $caption, $message_id, $from, $chat, $date) {

    }
    
    public function callback_query ($callback_data, $callback_id, $callback_from, $message_id, $from, $chat, $date) {

    }
    
    public function inline_query ($query, $offset, $inline_id, $from) {

    }
    
    public function new_member ($new_member, $message_id, $from, $chat, $date) {

    }
        
    public function left_member ($left_member, $message_id, $from, $chat, $date) {

    }
    
    public function new_chat_title ($new_chat_title, $message_id, $from, $chat, $date) {

    }
    
    public function reply_to_message ($reply_msg, $reply_id, $reply_date, $orig_msg, $orig_id, $orig_date, $from, $chat, $is_bot) {

    }

    public function install () {

    }
    
    public function uninstall () {

    }
    
    public function enable () {

    }
    
    public function disable () {

    }

    public function settings () {
        include_once "settings.html";
    }

    //保存前端设置
    public function saveSettings () {
        //强行演示:)
        exit (json_encode (array ("code" => 0, "msg" => "喵:" . http_build_query($_POST))));

        foreach($_POST as $k => $v){
            if(!in_array($k,["pcn","method"])){
                $this->option->iou($k,$v);
            }
        }
        exit (json_encode (array ("code" => 0, "msg" => "保存成功")));
    }

}
');
?>
  <input id="pcn" type="text" style="display:none" value="__PCN__" />
  <div id="code"><?php echo htmlspecialchars (DEFAULT_CODE) ?></div>
  <input style="display:none" lay-submit lay-submit lay-filter="createSub" id="createSub" value="创建" />
  <style>
    #code {
      position: absolute;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
    }
  </style>
  <script src="/Templates/assets/ace/ace.js"></script>
  <script>
  var timer = setInterval(function(){
    if(typeof(ace) != "undefined"){
      clearInterval(timer);

      layui.use(['layer','form'], function() {
        var $ = layui.jquery, form = layui.form;
        var editor = ace.edit("code");
        editor.getSession().setMode("ace/mode/php");
        editor.setTheme("ace/theme/monokai");
        editor.setFontSize(16);
        editor.setShowPrintMargin(false);
        
        form.on('submit(createSub)', function(data){
          layer.msg('创建中...');
          $.ajax({
            type: "POST",
            url: "<?php echo APP_URL ?>/index.php/PluginCreate/ajaxCreate",
            dataType: "json",
            data: {
              "pcn": $("input#pcn").val(),
              "code": editor.getValue()
            },
            success: function(data, textStatus, jqXHR){
              if(data.code == '0'){
                layer.closeAll();
                layer.msg('创建成功', {icon: 1});
                layui.table.reload('plugins_table');
              }else{
                layer.msg(data.msg, {icon: 2});
              }
            }
          });
        });
      })
    }
  }, 500);
  </script>