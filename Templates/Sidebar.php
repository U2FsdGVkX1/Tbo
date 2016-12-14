<nav class="navbar navbar-light bg-faded">
    <ul class="nav navbar-nav">
        <li class="nav-item">
            <a id="menu" data-href="Index" class="nav-link">首页</a>
        </li>
        <li class="nav-item">
            <a id="menu" data-href="Status" class="nav-link">Bot 状态</a>
        </li>
        <li class="nav-item">
            <a id="menu" data-href="Plugins" class="nav-link">插件</a>
        </li>
        <li class="nav-item">
            <a id="menu" data-href="Settings" class="nav-link">设置</a>
        </li>
    </ul>
</nav>
<script>
    $(document).pjax('nav ul li a', '#container');
    $("a#menu").each(function(){
        $(this).attr("href", "<?php echo APP_URL . '/index.php/' ?>" + $(this).data("href"));
    });
    $("a#menu").click(function(){
        $(this).parent().addClass("active");
        if(typeof(oldMenu) != "undefined"){
            oldMenu.parent().removeClass("active");
        }
        oldMenu = $(this);
    });
    oldMenu = $("a#menu[data-href='<?php echo $this->controller ?>']");
    oldMenu.parent().addClass("active");
</script>
