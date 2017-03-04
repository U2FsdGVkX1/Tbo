<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <link rel="stylesheet" href="<?php echo $this->loadSource ("assets/css/bootstrap.min.css") ?>">
        <link rel="stylesheet" href="<?php echo $this->loadSource ('assets/style.css') ?>">
        <script src="<?php echo $this->loadSource ("assets/js/jquery-3.1.1.min.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/jquery.pjax.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/js-cookie.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/tether.min.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/bootstrap.min.js") ?>"></script>
        <script>
            $(document).ready(function(){
                // 菜单
                $("a#menu").each(function(){
                    $(this).attr("href", "<?php echo APP_URL . '/index.php/' ?>" + $(this).data("href"));
                    $(this).attr("data-pjax", "true");
                });
                $("a#menu").click(function(){
                    $(this).parent().addClass("active");
                    if(typeof oldMenu != "undefined"){
                        oldMenu.parent().removeClass("active");
                    }
                    oldMenu = $(this);
                });
                oldMenu = $("a#menu[data-href='<?php echo $this->controller ?>']");
                oldMenu.parent().addClass("active");
                
                // pjax
                $.pjax.defaults.timeout = 10000;
                $(document).on("click", "[data-pjax='true']", function(){
                    // 初始化
                    newUrl = $(this).attr("href");
                    newPosition = $('#container').offset().left + $('#container').width();
                    
                    // 动画
                    $('#container').css("position", "relative");
                    $('#container').animate({
                        opacity: "0.0",
                        left: newPosition + "px"
                    }, 1500, function(){
                        $(this).css("left", "-" + newPosition + "px");
                        $.pjax({url: newUrl, container: '#container'});
                    });
                    return false;
                });
                $(document).on('pjax:success', function(data){
                    
                });
                $(document).on('pjax:end', function(){
                    $('#container').animate({
                        opacity: "1.0",
                        left: "0"
                    }, 1500, function(){
                        $('#container').css("position", "static");
                    });
                });
            });
        </script>