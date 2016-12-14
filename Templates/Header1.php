<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <link rel="stylesheet" href="<?php echo $this->loadSource ("assets/css/bootstrap.min.css") ?>">
        <link rel="stylesheet" href="<?php echo $this->loadSource ('assets/style.css') ?>">
        <style>
            body {
                overflow-x: hidden;
            }
            #progressBar {
                position: absolute;
                top: 0px;
                left: 0px;
                height: 3px;
                z-index: 9999;
            }
        </style>
        <script src="<?php echo $this->loadSource ("assets/js/jquery-3.1.1.min.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/jquery.pjax.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/js-cookie.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/tether.min.js") ?>"></script>
        <script src="<?php echo $this->loadSource ("assets/js/bootstrap.min.js") ?>"></script>
        <script>
            $.pjax.defaults.timeout = 10000;
            $(document).on('pjax:click', function(){
                $('#container').css("position", "relative");
                newPosition = $('#container').offset().left + $('#container').width();
                $('#container').animate({
                    opacity: "0.0",
                    left: newPosition + "px"
                }, 1500, function(){
                    $(this).css("left", "-" + newPosition + "px");
                });
            });
            $(document).on('pjax:end', function(){
                setTimeout(function(){
                    $('#container').animate({
                        opacity: "1.0",
                        left: "0"
                    }, 1500, function(){
                        $('#container').css("position", "static");
                    });
                }, 1500)
                
            });
        </script>