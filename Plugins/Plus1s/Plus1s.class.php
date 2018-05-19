<?php
    class Plus1s extends Base {
        public function message ($message, $message_id, $from, $chat, $date) {
            if ($message == '+1s') {
                $s = file_get_contents (__DIR__ . '/s.txt');
                $s++;
                file_put_contents (__DIR__ . '/s.txt', $s);
                /*
                    数据库驱动版本：
                    $s = $this->option->getvalue('plus1s_min');
                    if ($s === NULL)
                        $this->option->add ('plus1s_min', 0);
                    $s++;
                    $this->option->update ('plus1s_min', $s);
                    
                    详细定义：/Model/OptionModel.class.php
                */
                
                $text = gmstrftime ('🌚迄今为止，我已经多活了 %Hhours %Mminutes %Sseconds', $s);
                $button = json_encode (array (
                    'inline_keyboard' => array (
                        array (array (
                            'text' => '我真的还想再活一秒',
                            'callback_data' => '+1s'
                        ))
                    )
                ));
                $this->telegram->sendMessage ($chat['id'], $text, $message_id, $button);
            }
        }
        public function callback_query ($callback_data, $callback_id, $callback_from, $message_id, $from, $chat, $date) {
            if ($callback_data == '+1s') {
                $s = file_get_contents (__DIR__ . '/s.txt');
                $s++;
                file_put_contents (__DIR__ . '/s.txt', $s);
                
                $button = json_encode (array (
                    'inline_keyboard' => array (
                        array (array (
                            'text' => '再再活一秒',
                            'callback_data' => '+2s'
                        ))
                    )
                ));
                $this->telegram->editMessage ($chat['id'], $message_id, '🐸蛤丝，你觉得连续吼不吼啊？', $button);
            } else if ($callback_data == '+2s') {
                $this->telegram->editMessage ($chat['id'], $message_id, '暴力续命不可取🌝👎👎');
            }
        }
        public function inline_query ($query, $offset, $inline_id, $from) {
            if ($query == '+1s') {
                $tmp = [
                    [
                        'type' => 'article',
                        'id' => $this->telegram->getInlineId(),
                        'title' => '一句诗',
                        'input_message_content' => [
                            'message_text' => '苟……'
                        ]
                    ]
                ];
                $this->telegram->sendInlineQuery($tmp);
            }
        }
    }
