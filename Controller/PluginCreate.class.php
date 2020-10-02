<?php
    class PluginCreate extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            session_start ();
            if ($_SESSION['logined'] != true) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
        function ajaxCreate () {
            /** 检查 */
            if (empty ($_POST['pcn']) || empty ($_POST['code'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            if (file_exists(APP_PATH . '/Plugins/' . $_POST['pcn'])) {
              exit (json_encode (array ('code' => -9999, 'msg' => '插件PCN已存在')));
            }
            
            /** 写入 */
            mkdir (APP_PATH . '/Plugins/' . $_POST['pcn']);
            file_put_contents (APP_PATH . '/Plugins/' . $_POST['pcn'] . '/' . $_POST['pcn'] . '.class.php', $_POST['code']);
            file_put_contents (APP_PATH . '/Plugins/' . $_POST['pcn'] . '/' . 'settings.html', '');
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
    }
