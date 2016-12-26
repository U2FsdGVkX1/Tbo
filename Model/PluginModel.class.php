<?php
    class PluginModel extends FLModel {
        function install ($pcn)
        {
            /** 判断是否已安装 */
            $pluginInfo = $this->getinfo ($pcn);
            if (!empty ($pluginInfo)) {
                return;
            }
            
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, 'install'))
                $plugin->install ();
            
            /** 安装插件 */
            $this->db->insert ('plugins', [
                'pcn' => $pcn,
                'enabled' => 0,
                'lasterror' => 0,
                'priority' => 1
            ]);
        }

        function uninstall ($pcn)
        {
            /** 判断是否已安装 */
            $pluginInfo = $this->getinfo ($pcn);
            if (empty ($pluginInfo)) {
                return;
            }
            
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, 'uninstall'))
                $plugin->uninstall ();
            
            /** 卸载插件 */
            $this->db->delete ('plugins', [
                'pcn' => $pcn
            ]);
        }
        
        function enable ($pcn)
        {
            /** 判断是否已启用 */
            $pluginInfo = $this->getinfo ($pcn);
            if (empty ($pluginInfo) || $pluginInfo[0]['enabled'] == 1) {
                return;
            }
            
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, 'enable'))
                $plugin->enable ();
            
            /** 更新启用 */
            $this->db->update ('plugins', [
                'enabled' => 1
            ], [
                'pcn' => $pcn
            ]);
        }
        function disable ($pcn)
        {
            /** 判断是否已禁用 */
            $pluginInfo = $this->getinfo ($pcn);
            if (empty ($pluginInfo) || $pluginInfo[0]['enabled'] == 0) {
                return;
            }
            
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, 'disable'))
                $plugin->disable ();
            
            /** 更新禁用 */
            $this->db->update ('plugins', [
                'enabled' => 0
            ], [
                'pcn' => $pcn
            ]);
        }
        function priority ($pcn, $newPriority)
        {
            /** 判断是否已禁用 */
            $pluginInfo = $this->getinfo ($pcn);
            if (empty ($pluginInfo) || $pluginInfo[0]['enabled'] == 0) {
                return;
            }
            
            /** 更新优先级 */
            $this->db->update ('plugins', [
                'priority' => $newPriority
            ], [
                'pcn' => $pcn
            ]);
        }
        function settings ($pcn)
        {
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, 'settings')) {
                $plugin->settings ();
                $contents = ob_get_contents ();
                ob_clean ();
            } else {
                $contents = NULL;
            }
            
            /** 返回 */
            return $contents;
        }
        function callMethod ($pcn, $method)
        {
            /** 调用方法 */
            require_once APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $ret = '';
            $plugin = new $pcn ('');
            if (method_exists ($plugin, $method))
                $ret = $plugin->$method ();
            
            /** 返回 */
            return $ret;
        }
        function getinfo ($pcn = NULL, $enabled = NULL, $limit = 0, $count = false)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'priority'
            );
            $pcn === NULL ? : $where['AND']['pcn'] = $pcn;
            $enabled === NULL ? : $where['AND']['enabled'] = $enabled;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $count ? $this->db->count ('plugins', $where) : $this->db->select ('plugins', '*', $where);

            /** 返回 */
            return $ret;
        }
        function scan ()
        {
            $pluginList = scandir (APP_PATH . '/Plugins');
            array_shift ($pluginList);
            array_shift ($pluginList);
            
            return $pluginList;
        }
        
        function getinfo_f ($pcn)
        {
            /** 初始化 */
            $pluginFile = APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.json';
            
            /** 判断是否为旧式 */
            if (!file_exists ($pluginFile)) {
                return $this->getinfo_f_old ($pcn);
            }
            
            /** 返回 */
            return json_decode (file_get_contents ($pluginFile), true);
        }
        
        function getinfo_f_old ($pcn)
        {
            /** 初始化 */
            $pluginFile = APP_PATH . '/Plugins/' . $pcn . '/' . $pcn . '.class.php';
            $pluginCode = file_get_contents ($pluginFile);
            $pinfo_f = array ();
            $token = token_get_all ($pluginCode);
            $isdoc = false;

            /** 分析 */
            foreach ($token as $token_d) {
                if ($token_d[0] == T_COMMENT) {
                    $doc = preg_split ("#\n#", $token_d[1]);
                    
                    if (trim ($doc[1]) == '-----BEGIN INFO-----') {
                        /** 标记 */
                        $isdoc = true;

                        /** 循环 */
                        foreach ($doc as $doc_d) {
                            /** 处理 */
                            $doc_t = trim ($doc_d);

                            /** 判断是否已结束 */
                            if ($doc_t == '-----END INFO-----') {
                                break;
                            }

                            /** 分析 */
                            if (!empty ($doc_t) && $doc_t[0] == '@') {
                                $args = explode (' ', substr ($doc_t, 1, strlen ($doc_t) - 1)); /** 分割 */
                                if (!empty ($args[0]) && count ($args) >= 2) {
                                    $pinfo_f[$args[0]] = $args[1];
                                }
                            }
                        }
                    }
                }

                if ($isdoc == true) {
                    break;
                }
            }

            /** 最后处理并返回 */
            return $pinfo_f;
        }
    }
