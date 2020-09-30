<?php
    class Plugins extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            session_start ();
            if ($this->action != 'callback' && $_SESSION['logined'] != true) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
        function install () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 安装 */
            $pluginModel->install ($_POST['pcn']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function uninstall () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 安装 */
            $pluginModel->uninstall ($_POST['pcn']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function enable () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 安装 */
            $pluginModel->enable ($_POST['pcn']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function disable () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 安装 */
            $pluginModel->disable ($_POST['pcn']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function priority () {
            /** 检查 */
            if (empty ($_POST['pcn']) || !isset ($_POST['newPriority'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 设置 */
            $pluginModel->priority ($_POST['pcn'], $_POST['newPriority']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function installAll () {
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 安装 */
            $pluginList = $pluginModel->scan ();
            foreach ($pluginList as $pluginList_d) {
                $pluginModel->install ($pluginList_d);
            }
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function uninstallAll () {
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 卸载 */
            $pluginList = $pluginModel->getinfo ();
            foreach ($pluginList as $pluginList_d) {
                $pluginModel->uninstall ($pluginList_d['pcn']);
            }
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function enableAll () {
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 启用 */
            $pluginList = $pluginModel->getinfo ();
            foreach ($pluginList as $pluginList_d) {
                $pluginModel->enable ($pluginList_d['pcn']);
            }
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function disableAll () {
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 禁用 */
            $pluginList = $pluginModel->getinfo ();
            foreach ($pluginList as $pluginList_d) {
                $pluginModel->disable ($pluginList_d['pcn']);
            }
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function settings () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 调用设置 */
            $pluginSettingsContents = $pluginModel->settings ($_POST['pcn']);
        }
        function callback () {
            /** 检查 */
            if (empty ($_POST['pcn']) || empty ($_POST['method'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            if (in_array ($_POST['method'], ['init',
                                             'command',
                                             'message',
                                             'callback_query',
                                             'inline_query',
                                             'new_member',
                                             'left_member',
                                             'install',
                                             'uninstall',
                                             'enable',
                                             'disable',
                                             'settings',])) {
                exit (json_encode (array ('code' => -2, 'msg' => '非法请求')));
            }
            
            /** 初始化 */
            $pluginModel = new PluginModel;
            
            /** 获取设置 */
            $ret = $pluginModel->callMethod ($_POST['pcn'], $_POST['method']);
            
            /** 返回 */
            if ($ret == '') {
                exit (json_encode (array ('code' => 0)));
            } else {
                exit (json_encode (array ('code' => -1, 'msg' => $ret)));
            }
        }
        function remove () {
            /** 检查 */
            if (empty ($_POST['pcn'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 删除 */
            $this->rrmdir (APP_PATH . '/Plugins/' . $_POST['pcn']);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        private function rrmdir($src) {
            $dir = opendir ($src);
            $file = readdir ($dir);
            while($file !== false) {
                if ($file != '.' && $file != '..') {
                    $full = $src . '/' . $file;
                    if (is_dir ($full)) {
                        $this->rrmdir ($full);
                    } else {
                        unlink ($full);
                    }
                }
                $file = readdir ();
            }
            closedir ($dir);
            rmdir ($src);
        }
    }
