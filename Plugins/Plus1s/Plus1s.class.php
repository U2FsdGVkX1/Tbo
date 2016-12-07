<?php
    class Plus1s extends Base {
        public function message ($message, $message_id, $from, $chat, $date) {
            if ($message == '+1s') {
                $s = file_get_contents (__DIR__ . '/s.txt');
                $s++;
                file_put_contents (__DIR__ . '/s.txt', $s);
                
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
    }