<?php
	class PluginModel extends FLModel {
		function installAll ()
		{
			$pluginList = $this->scan ();
			
			if (!empty ($pluginList)) {
				$this->db->pdo->beginTransaction ();
				foreach ($pluginList as $pluginList_d) {
					$this->db->insert ('plugins', [
					    'pcn' => $pluginList_d,
					    'enabled' => 0,
					    'lasterror' => 0
					]);
				}
				$this->db->pdo->commit ();
			}
		}
		
		function uninstallAll ()
		{
			$this->db->delete ('plugins');
		}
		
		function enableAll ()
        {
            $this->db->update ('plugins', [
			    'enabled' => 1
			]);
        }
        
        function disableAll ()
        {
            $this->db->update ('plugins', [
			    'enabled' => 0
			]);
        }
		
		function install ($pcn)
		{
			$this->db->insert ('plugins', [
			    'pcn' => $pcn,
			    'enabled' => 0,
			    'lasterror' => 0,
			]);
		}

		function uninstall ($pcn)
		{
			$this->db->delete ('plugins', [
			    'pcn' => $pcn
			]);
		}
        
        function enable ($pcn)
        {
            $this->db->update ('plugins', [
			    'enabled' => 1
			], [
			    'pcn' => $pcn
			]);
        }
        
        function disable ($pcn)
        {
            $this->db->update ('plugins', [
			    'enabled' => 0
			], [
			    'pcn' => $pcn
			]);
        }
        
		function getinfo ($pcn = NULL, $enabled = NULL, $limit = 0, $count = false)
		{
		    /** 查询 */
			$where = array ();
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
			/** 初始化变量 */
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
