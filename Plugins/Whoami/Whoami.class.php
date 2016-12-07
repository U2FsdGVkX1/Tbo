<?php
    class Whoami extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/whoami') {
                $str = '你的 chat_id：' . $from['id'] . "\n";
                $str .= '群的 chat_id：' . $chat['id'] . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";
                $this->telegram->sendMessage ($chat['id'], $str, $message_id);
            }
        }
    }