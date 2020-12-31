<?php
    class PluginMain extends FLController {
        function run () {
            
        }
        function init () {
            session_start ();
            if ($_SESSION['logined'] != true) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
        function edit () {
            $this->view->render ();
        }
        function ajaxSave () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 写入 */
            $file = ($_POST['file'] == 'settings.html') ? $_POST['file'] : $_POST['pcn'] . $_POST['file'];
            file_put_contents (APP_PATH . '/Plugins/' . $_POST['pcn'] . '/' . $file, $_POST['code']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
    }
