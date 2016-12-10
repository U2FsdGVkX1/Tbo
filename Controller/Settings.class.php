<?php
    class Settings extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            $systemModel = new SystemModel;
            if ($_COOKIE['password'] != $systemModel->password ()) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
        function ajaxSave () {
            /** 初始化 */
            $systemModel = new SystemModel;
            
            /** 保存配置文件 */
            $config = "<?php
    define ('BOTNAME', '{$_POST['botName']}');
    define ('TOKEN', '{$_POST['botToken']}');
    define ('API_URL', 'https://api.telegram.org/bot' . TOKEN . '/');
    define ('DEBUG', {$_POST['debug']});
";
            file_put_contents (CONFIG_PATH . '/BotConfig.php', $config);
            
            /** 管理员密码 */
            if ($_POST['password'] != '') {
                $systemModel->password ($_POST['password']);
            }
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function setWebhook () {
            /** 初始化 */
            $telegramModel = new TelegramModel;
            $newurl = 'https://' . $_SERVER['SERVER_NAME'] . APP_URL . '/index.php/Callback';
            
            /** 设置回调 */
            $ret = $telegramModel->setWebhook ($newurl);
            
            /** 返回 */
            if ($ret['ok'] == true) {
                exit (json_encode (array ('code' => 0)));
            } else {
                exit (json_encode (array ('code' => -1, 'msg' => $ret['description'])));
            }
        }
        function getUsername () {
            /** 初始化 */
            $telegramModel = new TelegramModel;
            
            /** 设置回调 */
            $ret = $telegramModel->getMe ();
            
            /** 返回 */
            if ($ret['ok'] == true) {
                exit (json_encode (array ('code' => 0, 'username' => $ret['result']['username'])));
            } else {
                exit (json_encode (array ('code' => -1, 'msg' => $ret['description'])));
            }
        }
    }
