<?php
    $pjax = isset ($_SERVER['HTTP_X_PJAX']);
?>
<?php
    /** 初始化 */
    $optionModel = new OptionModel;
    $telegramModel = new TelegramModel;
    
    /** 统计图 */
    $message_total = $optionModel->getvalue ('message_total');
    $send_total = $optionModel->getvalue ('send_total');
    $error_total = $optionModel->getvalue ('error_total');
    
    /** Bot是否boom */
    $status = $telegramModel->getMe ();
    $boom = !$status['ok'];
?>
<?php if ($pjax == false) require_once 'Header1.php' ?>
<title>Bot 状态</title>
<?php if ($pjax == false) require_once 'Header2.php' ?>
<?php if ($pjax == false) require_once 'Sidebar.php' ?>
<?php if ($pjax == false) echo '<div class="container" id="container">' ?>

<div class="row">
    <div class="col-xs-12" style="margin-top: 10px">
        <?php
            if ($boom) {
                ?>
                <div class="alert alert-danger" role="alert">
                    <strong>你的 Bot 好像 boom 了，建议检查 Token 是否正确</strong>
                </div>
                <?php
            } else {
                ?>
                <div class="alert alert-success" role="alert">
                    <strong>你的 Bot 正在运行……</strong>
                </div>
                <?php
            }
        ?>
    </div>
    <div class="col-xs-12" style="margin-top: 10px">
        <canvas id="statistics"></canvas>
    </div>
</div> 
<script src="<?php echo $this->loadSource ("assets/js/Chart.min.js") ?>"></script>
<script>
    var timer = setInterval(function(){
        if(typeof(Chart) != "undefined"){
            clearInterval(timer);
            
            var ctx = $("#statistics");
            var statistics = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ["消息数", "发送数", "错误数"],
                    datasets: [{
                        label: '# of Votes',
                        data: [<?php echo $message_total, ",", $send_total, ",", $error_total ?>],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255,99,132,1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    cutoutPercentage: 50,
                    animation: {
                        animateScale: true,
                    },
                }
            });
        }
    }, 500);
</script>

<?php if ($pjax == false) echo '</div>' ?>
<?php if ($pjax == false) require_once 'Footer.php' ?>