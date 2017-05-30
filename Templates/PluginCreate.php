<?php
    define ('DEFAULT_CODE', '<?php
    /*
    -----BEGIN INFO-----
    @PluginName 插件名称
    @PluginURL 插件主页
    @Description 说明
    @Author 作者
    @AuthorURL 作者主页
    @AuthorEmail 作者邮箱
    @Version 版本号
    -----END INFO-----
    */
    class 这里要和PCN一致 extends Base {
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
        public function install () {
            
        }
        public function uninstall () {
            
        }
        public function enable () {
            
        }
        public function disable () {
            
        }
        public function settings () {
            
        }
    }
');
?>
<?php
    $pjax = isset ($_SERVER['HTTP_X_PJAX']);
?>
<?php if ($pjax == false) require_once 'Header1.php' ?>
<title>创建插件</title>
<?php if ($pjax == false) require_once 'Header2.php' ?>
<?php if ($pjax == false) require_once 'Sidebar.php' ?>
<?php if ($pjax == false) echo '<div class="container" id="container">' ?>

<div class="row">
    <div class="col-xs-12" style="margin-top: 10px">
        <div class="input-group">
            <input id="pcn" type="text" class="form-control" placeholder="插件 PCN" aria-describedby="basic-addon2">
            <span class="input-group-addon" id="basic-addon2">.class.php</span>
        </div>
    </div>
    <div class="col-xs-12" style="margin-top: 10px">
        <div id="code"><?php echo htmlspecialchars (DEFAULT_CODE) ?></div>
    </div>
    <div class="col-xs-12" style="margin-top: 760px">
        <button id="create" style="float: right" type="button" class="btn btn-success">创建</button>
    </div>
</div>
<style>
    #code {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 750px;
    }
</style>
<script src="<?php echo $this->loadSource ("assets/ace/ace.js") ?>" type="text/javascript" charset="utf-8"></script>
<script>
    var timer = setInterval(function(){
        if(typeof(ace) != "undefined"){
            clearInterval(timer);
            
            var editor = ace.edit("code");
            editor.getSession().setMode("ace/mode/php");
            editor.setTheme("ace/theme/clouds");
            editor.setFontSize(16);
            editor.setShowPrintMargin(false);
            
            $("button#create").click(function(){
                buttonThis = $(this);
                $(buttonThis).attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: "<?php echo APP_URL ?>/index.php/PluginCreate/ajaxCreate",
                    data: {
                        "pcn": $("input#pcn").val(),
                        "code": editor.getValue()
                    },
                    success: function(data, textStatus, jqXHR){
                        if(data.code == '0'){
                            location.href = "<?php echo APP_URL ?>/index.php/plugins";
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
        }
    }, 500);
</script>

<?php if ($pjax == false) echo '</div>' ?>
<?php if ($pjax == false) require_once 'Footer.php' ?>
