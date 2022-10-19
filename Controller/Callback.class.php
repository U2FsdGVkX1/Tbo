<?php
    class Callback extends FLController {
        function run () {
            /** 安全检测 */
            if(!$this->webHookSign()) die();
            
            /** 初始化 */
            $errorModel = new ErrorModel;
            $pluginModel = new PluginModel;
            $optionModel = new OptionModel;
            $telegramModel = new TelegramModel;
            
            $GLOBALS['statistics']['message_total'] = $optionModel->getvalue ('message_total');
            $GLOBALS['statistics']['send_total'] = $optionModel->getvalue ('send_total');
            $GLOBALS['statistics']['error_total'] = $optionModel->getvalue ('error_total');
            
            /** 分析消息 */
            $GLOBALS['statistics']['message_total']++;
            $this->parseMessage ();
            
            /** 操作 */
            if (isset ($this->func) && $this->func == 'callback_query') {
                $callbackData = explode (' ', $this->param[0]);
                if ($callbackData[0] == 'Tbo_Operations') {
                    if ($callbackData[1] == 'fastLogin' && FASTLOGIN) { // 快速登录
                        if ($callbackData[2] == 'allow') {
                            $optionModel->update ('fastlogin_ip', $callbackData[3]);
                            $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已允许授权');
                        } else if ($callbackData[2] == 'noallow') {
                            $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已拒绝授权');
                        }
                    } else if ($callbackData[1] == 'logout') { // 远程登出
                        session_id ($callbackData[2]);
                        session_start ();
                        
                        $_SESSION['logined'] = false;
                        $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已登出');
                    }
                }
            }
            
            /** 引入处理 */
            if (isset ($this->func)) {
                $pluginList = $pluginModel->getinfo (NULL, 1);
                if (!empty ($pluginList)) {
                    foreach ($pluginList as $pluginList_d) {
                        $pluginName = $pluginList_d['pcn'];
                        
                        $GLOBALS['cuPlugin'] = $pluginName;
                        require_once APP_PATH . '/Plugins/' . $pluginName . '/' . $pluginName . '.class.php';
                        $object[] = $objectNew = new $pluginName ($this->data);
                        if (method_exists ($objectNew, 'init'))
                            call_user_func_array (array ($objectNew, 'init'), $this->initParam);
                    }
                    
                    foreach ($object as $object_d) {
                        if (method_exists ($object_d, $this->func)) {
                            $GLOBALS['cuPlugin'] = get_class ($object_d);
                            call_user_func_array (array ($object_d, $this->func), $this->param);
                        }
                    }
                    
                    if (isset ($cuPlugin))
                        unset ($cuPlugin);
                }
                if ($this->func == 'inline_query') {
                    $telegramModel->sendInline ($this->param[2], 0);
                }
            }
            
            /** 统计 */
            $optionModel->update ('message_total', $GLOBALS['statistics']['message_total']);
            $optionModel->update ('send_total', $GLOBALS['statistics']['send_total']);
            $optionModel->update ('error_total', $GLOBALS['statistics']['error_total']);
        }

        private function webHookSign() {
            $telegramWebhook = [
                '149.154.160.0/20',
                '91.108.4.0/22'
            ];

            /* 允许以下配置
             * define ('WEBHOOK_CIDR', [
             *     "1.2.3.4/32",
             *     "233.233.233.0/24"
             * ]);
             * */
            if(defined('WEBHOOK_CIDR') && is_array(WEBHOOK_CIDR)) {
                $telegramWebhook = array_merge($telegramWebhook, WEBHOOK_CIDR);
            }

            foreach ($telegramWebhook as $cidr) {
                if($this->cidrMatch($_SERVER['REMOTE_ADDR'], $cidr))
                    return true;
            }

            return false;
        }

        private function cidrMatch($ip, $cidr)
        {
            list($subnet, $mask) = explode('/', $cidr);

            if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
            {
                return true;
            }

            return false;
        }

        private function parseMessage ()
        {
            $data = json_decode (file_get_contents ("php://input"), true);
            if (isset ($data['message'])) {
                if (isset ($data['message']['text'])) {
                    if(isset ($data['message']['reply_to_message'])) {
                        $data['message']['chat']['reply_to_message'] = $data['message']['reply_to_message'];
                    }
                                        
                     /** 艾特处理 */
                    if(isset ($data['message']['entities'])) {
                        foreach ($data['message']['entities'] as $entities){
                            if($entities['type'] == 'text_mention'){
                                $mentionUserId =  $entities['user']['id'];
                                $mentionUserName = $entities['user']['first_name'];
                            }
                            if($entities['type'] == 'mention'){
                                $tmp = mb_substr($data['message']['text'], $entities['offset'] + 1, $entities['length'],"utf-8");
                                $mentionUserId = $tmp;
                                $mentionUserName = $tmp;
                            }
                            $data['message']['at_array'][] = ['id' => $mentionUserId, 'name' => $mentionUserName];
                            $data['message']['at_ids'][] = $mentionUserId;
                        }
                                                
                    }
                                        
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
                        if(isset ($data['message']) && !isset ($data['message']['reply_to_message'])){
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
                        }else{
                            $func = 'reply_message';
                            $param = [
                                $data['message']['text'],
                                $data['message']['message_id'],
                                $data['message']['date'],
                                $data['message']['reply_to_message']['text'],
                                $data['message']['reply_to_message']['message_id'],
                                $data['message']['reply_to_message']['date'],
                                $data['message']['from'],
                                $data['message']['chat'],
                                $data['message']['reply_to_message']['from']['is_bot'],
                            ];
                            $initParam = [
                                $func,
                                $data['message']['from'],
                                $data['message']['chat'],
                                $data['message']['date'],
                            ];
                        }
                    }
                } else if (isset ($data['message']['new_chat_title'])) {
                    $func = 'new_chat_title';
                    $param = [
                        $data['message']['new_chat_title'],
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
                } else if (isset ($data['message']['sticker'])) {
                    $func = 'sticker';
                    $param = [
                        $data['message']['sticker'],
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
                } else if (isset ($data['message']['photo'])) {
                    $caption = '';
                    if (isset ($data['message']['caption'])) {
                        $caption = $data['message']['caption'];
                    }
    
                    $func = 'photo';
                    $param = [
                        $data['message']['photo'],
                        $caption,
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
                } else if (isset ($data['message']['document'])) {
                    $caption = '';
                    if (isset ($data['message']['caption'])) {
                        $caption = $data['message']['caption'];
                    }
    
                    $func = 'document';
                    $param = [
                        $data['message']['document'],
                        $caption,
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
                if (isset ($data['callback_query']['data'])) {
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
                } else if (isset ($data['callback_query']['game_short_name'])) {
                    $func = 'callback_game';
                    $param = [
                        $data['callback_query']['game_short_name'],
                        $data['callback_query']['id'],
                        $data['callback_query']['from']
                    ];
                    $initParam = [
                        $func,
                        $data['callback_query']['from'],
                        $data['callback_query']['from'],
                        time ()
                    ];
                }
            } else if (isset ($data['inline_query'])) {
                $func = 'inline_query';
                $param = [
                    $data['inline_query']['query'],
                    $data['inline_query']['offset'],
                    $data['inline_query']['id'],
                    $data['inline_query']['from']
                ];
                $initParam = [
                    $func,
                    $data['inline_query']['from'],
                    $data['inline_query']['from'],
                    time ()
                ];
            }
            
            $this->data = $data;
            if (isset ($func)) {
                $this->func = $func;
                $this->param = $param;
                $this->initParam = $initParam;
            }
        }
    }
