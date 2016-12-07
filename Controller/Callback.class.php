<?php
    class Callback extends FLController {
        function run () {
            /** 初始化 */
            $errorModel = new ErrorModel;
            $pluginModel = new PluginModel;
            
		    /** 分析消息 */
		    $data = json_decode (file_get_contents ("php://input"), true);
		    if (isset ($data['message'])) {
		        if (isset ($data['message']['text'])) {
		            if ($data['message']['text'][0] == '/') {
		                $data['message']['text'] = str_replace ("\n", " ", $data['message']['text']);
		                $messageExplode = explode (' ', $data['message']['text']);
		                $commandExplode = explode ('@', $messageExplode[0]);
		                if (isset ($commandExplode[1]) && $commandExplode[1] != BOTNAME)
		                    exit ();
		                
		                $func = 'command';
		                $param = [
		                    $commandExplode[0],
		                    array (),
		                    $data['message']['message_id'],
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                $initParam = [
		                    $func,
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                if (isset ($messageExplode[1])) {
		                    $param[1] = array_slice ($messageExplode, 1);
		                }
		            } else {
		                $func = 'message';
		                $param = [
		                    $data['message']['text'],
		                    $data['message']['message_id'],
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                $initParam = [
		                    $func,
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		            }
		        } else if (isset ($data['message']['new_chat_member'])) {
		            $func = 'new_member';
		            $param = [
		                $data['message']['new_chat_member'],
		                $data['message']['message_id'],
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		            $initParam = [
		                $func,
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		        } else if (isset ($data['message']['left_chat_member'])) {
		            $func = 'left_member';
		            $param = [
		                $data['message']['left_chat_member'],
		                $data['message']['message_id'],
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		            $initParam = [
		                $func,
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		        }
		    } else if (isset ($data['callback_query'])) {
		        $func = 'callback_query';
		        $param = [
		            $data['callback_query']['data'],
		            $data['callback_query']['id'],
		            $data['callback_query']['from'],
		            $data['callback_query']['message']['message_id'],
		            $data['callback_query']['message']['from'],
		            $data['callback_query']['message']['chat'],
		            $data['callback_query']['message']['date']
		        ];
		        $initParam = [
		            $func,
		            $data['callback_query']['from'],
		            $data['callback_query']['message']['chat'],
		            $data['callback_query']['message']['date']
		        ];
		    }
		    
		    /** 引入处理 */
		    if (isset ($func)) {
		        $pluginList = $pluginModel->getinfo (NULL, 1);
		        if (!empty ($pluginList)) {
    		        foreach ($pluginList as $pluginList_d) {
    		            $pluginName = $pluginList_d['pcn'];
    		            
    	                require_once APP_PATH . '/Plugins/' . $pluginName . '/' . $pluginName . '.class.php';
    	                $object[] = $objectNew = new $pluginName ($data);
    	                if (method_exists ($objectNew, 'init'))
    	                    call_user_func_array (array ($objectNew, 'init'), $initParam);
    		        }
    		        
    		        foreach ($object as $object_d) {
    		            if (method_exists ($object_d, $func))
    		                call_user_func_array (array ($object_d, $func), $param);
    		        }
		        }
		    }
        }
    }
