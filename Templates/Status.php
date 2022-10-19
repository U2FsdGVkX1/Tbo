<?php $pageTitle = 'Bot状态'; require_once 'Header.php' ?>

<?php
    /** 初始化 */
    $optionModel = new OptionModel;
    $telegramModel = new TelegramModel;
    
    /** 统计图 */
    $message_total = $optionModel->getvalue ('message_total');
    $send_total = $optionModel->getvalue ('send_total');
    $error_total = $optionModel->getvalue ('error_total');
    
    /** Bot是否boom */
    $status = $telegramModel->getWebhook ();
    if ($status['ok']) {
        if (empty ($status['result']['last_error_message'])) {
            $boomMessage = '';
        } else {
            $boomMessage = $status['result']['last_error_message'] . ' at ' . date('Y-m-d H:i:s', $status['result']['last_error_date']);
        }
    } else {
        $boomMessage = '建议检查 Token 是否正确';
    }
?>
  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
      <div class="layui-col-md8">
        <div class="layui-row layui-col-space15">
          <div class="layui-col-md12">
            <div class="layui-card">
              <div class="layui-card-header">Bot状态</div>
              <?php if ($boomMessage == '') { ?>
              <div class="layui-card-body" style="font-size: 15px; color: #5FB878;">
                <i class="layui-icon layui-icon-face-smile" style="font-size: 20px;"></i>
                <strong>&nbsp;&nbsp;你的 Bot 正在运行……</strong>
              </div>
              <?php } else { ?>
              <div class="layui-card-body" style="font-size: 15px; color: #FF5722;">
                <i class="layui-icon layui-icon-face-cry" style="font-size: 20px;"></i>
                <strong>&nbsp;&nbsp;你的 Bot 好像 boom 了，<?php echo $boomMessage; ?></strong>
              </div>
              <?php } ?>
            </div>
           </div>
          
          <div class="layui-col-md12">
            <div class="layui-card">
              <div class="layui-card-header">运行日志</div>
              <div class="layui-tab layui-tab-brief layadmin-latestData">
                <div class="layui-tab-content">
                  <div class="layui-tab-item layui-show">
                    <table id="LAY-index-topSearch"></table>
                  </div>
                  <div class="layui-tab-item">
                    <table id="LAY-index-topCard"></table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="layui-col-md4">
        <div class="layui-card">
          <div class="layui-card-header">消息统计</div>
          <div class="layui-card-body">
            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
              <div carousel-item id="LAY-index-dataview">
                <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
              </div>
            </div>
          </div>
        </div>
        <div class="layui-card">
          <div class="layui-card-header">关于项目</div>
          <div class="layui-card-body layui-text">
            <table class="layui-table">
              <colgroup>
                <col width="100">
                <col>
              </colgroup>
              <tbody>
               <tr>
                  <td>项目简介</td>
                  <td>友好、强大且轻巧的TGBOT框架</td>
                </tr>
                <tr>
                  <td>开发语言</td>
                  <td>
                    <script type="text/html" template>
                      PHP & Layui-v{{ layui.v }}
                    </script>
                 </td>
                </tr>
                <tr>
                  <td>加入组织</td>
                  <td>
                    <script type="text/html" template>
                      搬梯子哦 👉
                      <a href="https://telegram.me/TboJiangGroup" target="_blank" style="padding-left: 15px;">@TboJiangGroup</a>
                    </script>
                 </td>
                </tr>
                <tr>
                  <td>文档相关</td>
                  <td style="padding-bottom: 0;">
                    <div class="layui-btn-container">
                      <a href="https://github.com/U2FsdGVkX1/Tbo/wiki" target="_blank" class="layui-btn layui-btn-danger">开发文档</a>
                      <a href="https://github.com/U2FsdGVkX1/Tbo" target="_blank" class="layui-btn">GitHub</a>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <script src="../Templates/assets/layui/layui.js?t=1"></script>  
  <script>
  layui.config({
    base: '../Templates/assets/'
  }).extend({
    index: 'lib/index'
  }).use(['index', 'console'], function(){
    layui.use(["carousel", "echarts"], function() {
        var e = layui.$
          , t = layui.carousel
          , a = layui.echarts
          , i = [];
          var l = [{
            title: {
                text: "消息数量统计",
                x: "center",
                textStyle: {
                    fontSize: 14
                }
            },
            tooltip: {
                trigger: "item",
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: "vertical",
                x: "left",
                data: ["消息数", "发送数", "错误数"]
            },
            series: [{
                name: "消息类型",
                type: "pie",
                radius: "40%",
                center: ["50%", "50%"],
                data: [{
                    value: <?php echo $message_total; ?>,
                    name: "消息数"
                }, {
                    value: <?php echo $send_total; ?>,
                    name: "发送数"
                }, {
                    value: <?php echo $error_total; ?>,
                    name: "错误数"
                }]
            }]
        }]
          , n = e("#LAY-index-dataview").children("div")
          , r = function(e) {
            i[e] = a.init(n[e], layui.echartsTheme),
            i[e].setOption(l[e]),
            window.onresize = i[e].resize
        };
        if (n[0]) {
            r(0);
            var o = 0;
            t.on("change(LAY-index-dataview)", function(e) {
                r(o = e.index)
            }),
            layui.admin.on("side", function() {
                setTimeout(function() {
                    r(o)
                }, 300)
            }),
            layui.admin.on("hash(tab)", function() {
                layui.router().path.join("") || r(o)
            })
        }
    });
    layui.use("table", function() {
        var e = (layui.$,
        layui.table);
        e.render({
            elem: "#LAY-index-topSearch",
            url: layui.setter.base + "json/console/top-search.js",
            page: !0,
            cols: [[{
                type: "numbers",
                fixed: "left"
            }, {
                field: "type",
                title: "消息类型",
                width: 100,
            }, {
                field: "from",
                title: "消息来源",
                width: 180,
            }, {
                field: "info",
                title: "消息内容",                
            }, {
                field: "time",
                title: "时间",
                width: 160,
                sort: !0
            }]],
            skin: "line"
        })
    })
  });
  </script>
<?php require_once 'Footer.php' ?>
