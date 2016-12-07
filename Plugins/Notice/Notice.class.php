<?php
    class Notice extends Base {
        public function new_member ($new_member, $message_id, $from, $chat, $date) {
            $str = '@' . $from['username'] . ' 邀请了 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . ' 玩' . "\n";
            $str .= '欢迎 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . '  玩(ฅ>ω<*ฅ)';
            $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
        }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            $str = '喵喵喵？ @' . $left_member['username'] . ' 被 @' . $from['username'] . ' 移出了 ' . $chat['title'];
            $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
        }
    }