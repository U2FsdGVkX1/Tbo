<?php
    class TelegramModel extends FLModel {
        private $ret;
        private $token;
        private static $inlineResults = array ();
        
        public function __construct ($token = NULL) {
            $this->token = ($token === NULL ? TOKEN : $token);
            parent::__construct ();
        }
        /**
         * http_build_query兼容多维数组，返回结果数组仍支持http_build_query函数处理
         * @author Zjmainstay https://bugs.php.net/bug.php?id=67477
         * @param $data array curl传递参数的数组（原装）
         * @return array 可curl传递的格式化数组
         */
        function http_build_query_develop($data) {
            if(!is_array($data)) {
                return $data;
            }
            foreach($data as $key => $val) {
                if(is_array($val)) {
                    foreach($val as $k => $v) {
                        if(is_array($v)) {
                            $data = array_merge($data, http_build_query_develop(array( "{$key}[{$k}]" => $v)));
                        } else {
                            $data["{$key}[{$k}]"] = $v;
                        }
                    }
                    unset($data[$key]);
                }
            }
            return $data;
        }
        private function fetch ($url, $postdata = null) {
            $ch = curl_init ();
            curl_setopt ($ch, CURLOPT_URL, $url);
            if (!is_null ($postdata)) {
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $this->http_build_query_develop($postdata));
            }
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            $re = curl_exec ($ch);
            curl_close ($ch);
            
            return $re;
        }
        public function callMethod ($method, $param = array (), $detection = true) {
            /** 访问网页 */
            $url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;
            $ret = json_decode ($this->fetch ($url, $param), true);
            
            /** 分析结果 */
            if ($ret['ok'] == false && $detection == true) {
                if ($ret['error_code'] != 400 && $ret['error_code'] != 403) {
                    $errorModel = new ErrorModel;
                    $errorModel->sendError (MASTER, '尝试调用 ' . $method . " 时出现问题，参数表如下：\n" . print_r ($param, true) . "\n\n返回结果：\n" . print_r ($ret, true));
                }
            }
            
            /** 返回 */
            return $ret;
        }
        public function getWebhook () {
            $this->ret = $this->callMethod ('getWebhookInfo', [
            ], false);
            return $this->ret;
        }
        public function setWebhook ($newurl) {
            $this->ret = $this->callMethod ('setWebhook', [
                'url' => $newurl
            ], false);
            return $this->ret;
        }
        public function sendMessage ($chat_id, $text, $reply_to_message_id = NULL, $reply_markup = array (), $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification = false) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            foreach (str_split ($text, 4096) as $text_i => $text_d) {
                $tmp = $this->callMethod ('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => $text_d,
                    'reply_to_message_id' => $reply_to_message_id,
                    'parse_mode' => $parse_mode,
                    'reply_markup' => $reply_markup,
                    'disable_web_page_preview' => $disable_web_page_preview,
                    'disable_notification' => $disable_notification
                ]);
                if ($text_i == 0) $this->ret = $tmp;
            }
            return $this->ret['result']['message_id'];
        }
        public function editMessage ($chat_id, $message_id, $text, $reply_markup = array (), $parse_mode = 'HTML') {
            $this->ret = $this->callMethod ('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $text,
                'parse_mode' => $parse_mode,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function deleteMessage ($chat_id, $message_id) {
            $this->ret = $this->callMethod ('deleteMessage', get_defined_vars());
            return $this->ret;
        }
        public function kickMember ($chat_id, $user_id, $until_date = NULL) {
            $this->ret = $this->callMethod ('kickChatMember', [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
                'until_date' => $until_date
            ]);
            return $this->ret;
        }
        public function sendPhoto ($chat_id, $photo, $caption = '', $reply_to_message_id = NULL, $reply_markup = array (), $parse_mode = 'HTML') {
            if (is_array ($photo)) {
                 return $this->sendMediaGroup ($chat_id, array_map (function ($p) use ($caption, $parse_mode) {
                     return [
                         'type' => 'photo',
                         'media' => $p,
                         'caption' => $caption,
                         'parse_mode' => $parse_mode
                     ];
                 }, $photo), $reply_to_message_id);
            }
            
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo,
                'caption' => $caption,
                'parse_mode' => $parse_mode,
                'reply_to_message_id' => $reply_to_message_id,
                'reply_markup' => $reply_markup
              ]);
            return $this->ret['result']['message_id'];
        }
       public function sendMediaGroup ($chat_id, $media, $reply_to_message_id = NULL) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendMediaGroup', [
                'chat_id' => $chat_id,
                'media' => json_encode ($media),
                'reply_to_message_id' => $reply_to_message_id
            ]);
            return array_map (function ($m){
                return $m['message_id'];
            }, $this->ret['result']);
        }
        public function sendAudio ($chat_id, $audio, $caption = '', $reply_to_message_id = NULL, $reply_markup = array (), $parse_mode = 'HTML', $duration = '', $performer = NULL, $title = NULL, $thumb = '', $disable_notification = false) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendAudio', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendDocument ($chat_id, $document, $caption = '', $reply_to_message_id = NULL, $reply_markup = array (), $parse_mode = 'HTML', $thumb = '', $disable_notification = false) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendDocument', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendVideo($chat_id, $video, $duration = '', $width = '', $height = '', $thumb = '', $caption = NULL, $parse_mode = 'HTML', $supports_streaming = '', $disable_notification = false, $reply_to_message_id = '', $reply_markup = '') {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendVideo', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendAnimation($chat_id, $animation, $duration = NULL, $width = NULL, $height = NULL, $thumb = NULL, $caption = '', $parse_mode = 'HTML', $disable_notification = NULL, $reply_to_message_id = NULL, $reply_markup = array ())
        {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendAnimation', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendVoice($chat_id, $voice, $caption = NULL, $parse_mode = 'HTML', $duration = '', $disable_notification = false, $reply_to_message_id = '', $reply_markup = '') {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendVoice', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function getUserProfilePhotos($user_id, $offset = '', $limit = '') {
            $this->ret = $this->callMethod ('getUserProfilePhotos', get_defined_vars());
            return $this->ret['result'];
        }
        public function unbanChatMember($chat_id, $user_id) {
            $this->ret = $this->callMethod ('unbanChatMember', get_defined_vars());
            return $this->ret['result'];
        }
        public function restrictChatMember($chat_id, $user_id, $permissions, $until_date = NULL) {
            $this->ret = $this->callMethod ('restrictChatMember', get_defined_vars());
            return $this->ret['result'];
        }
        public function promoteChatMember($chat_id, $user_id, $can_change_info = NULL, $can_post_messages = NULL, $can_edit_messages = NULL, $can_delete_messages = NULL, $can_invite_users = NULL, $can_restrict_members = NULL, $can_pin_messages = NULL, $can_promote_members = NULL) {
            $this->ret = $this->callMethod ('promoteChatMember', get_defined_vars());
            return $this->ret['result'];
        }
        public function setChatPermissions($chat_id, $permissions) {
            $this->ret = $this->callMethod ('setChatPermissions', get_defined_vars());
            return $this->ret['result'];
        }
        public function exportChatInviteLink($chat_id) {
            $this->ret = $this->callMethod ('exportChatInviteLink', get_defined_vars());
            return $this->ret['result'];
        }
        public function setChatPhoto($chat_id, $photo) {
            $this->ret = $this->callMethod ('setChatPhoto', get_defined_vars());
            return $this->ret['result'];
        }
        public function deleteChatPhoto($chat_id) {
            $this->ret = $this->callMethod ('deleteChatPhoto', get_defined_vars());
            return $this->ret['result'];
        }
        public function setChatTitle($chat_id, $title) {
            $this->ret = $this->callMethod ('setChatTitle', get_defined_vars());
            return $this->ret['result'];
        }
        public function setChatDescription($chat_id, $description = NULL) {
            $this->ret = $this->callMethod ('setChatDescription', get_defined_vars());
            return $this->ret['result'];
        }
        public function pinChatMessage($chat_id, $message_id, $disable_notification = false) {
            $this->ret = $this->callMethod ('pinChatMessage', get_defined_vars());
            return $this->ret['result'];
        }
        public function unpinChatMessage($chat_id) {
            $this->ret = $this->callMethod ('unpinChatMessage', get_defined_vars());
            return $this->ret['result'];
        }
        public function leaveChat($chat_id) {
            $this->ret = $this->callMethod ('leaveChat', get_defined_vars());
            return $this->ret['result'];
        }
        public function getChat($chat_id) {
            $this->ret = $this->callMethod ('getChat', get_defined_vars());
            return $this->ret['result'];
        }
        public function getChatMembersCount($chat_id) {
            $this->ret = $this->callMethod ('getChatMembersCount', get_defined_vars());
            return $this->ret['result'];
        }
        public function getChatMember($chat_id, $user_id) {
            $this->ret = $this->callMethod ('getChatMember', get_defined_vars());
            return $this->ret['result'];
        }
        public function editMessageText($chat_id = '', $message_id = '', $inline_message_id = NULL, $text, $parse_mode = 'HTML', $disable_web_page_preview = '', $reply_markup = '') {
            $this->ret = $this->callMethod ('editMessageText', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function editMessageCaption($chat_id = '', $message_id = '', $inline_message_id = NULL, $caption = NULL, $parse_mode = 'HTML', $reply_markup = '') {
            $this->ret = $this->callMethod ('editMessageCaption', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function editMessageMedia($chat_id = '', $message_id = '', $inline_message_id = NULL, $media, $reply_markup = '') {
            $this->ret = $this->callMethod ('editMessageMedia', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function editMessageReplyMarkup($chat_id = '', $message_id = '', $inline_message_id = NULL, $reply_markup = '') {
            $this->ret = $this->callMethod ('editMessageReplyMarkup', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendSticker($chat_id, $sticker, $reply_to_message_id = NULL, $reply_markup = array (), $disable_notification = false) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendSticker', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function sendGame ($chat_id, $game_name, $reply_to_message_id = NULL, $reply_markup = array ()) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('sendGame', [
                'chat_id' => $chat_id,
                'game_short_name' => $game_name,
                'reply_to_message_id' => $reply_to_message_id,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function setGameScore ($user_id, $score, $force = false, $disable_edit_message = false, $chat_id = NULL, $message_id = NULL, $inline_id = NULL) {
            $this->ret = $this->callMethod ('setGameScore', [
                'user_id' => $user_id,
                'score' => $score,
                'force' => $force,
                'disable_edit_message' => $disable_edit_message,
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'inline_message_id' => $inline_id
            ]);
            return $this->ret['result']['message_id'];
        }
        public function forwardMessage ($chat_id, $from_chat_id, $message_id, $disable_notification = false) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('forwardMessage', get_defined_vars());
            return $this->ret['result']['message_id'];
        }
        public function answerCallback ($callback_id, $text = '', $show_alert = false, $url = '', $cache_time = 0) {
            if (isset ($GLOBALS['statistics']['send_total']))
                $GLOBALS['statistics']['send_total']++;
            $this->ret = $this->callMethod ('answerCallbackQuery', [
                'callback_query_id' => $callback_id,
                'text' => $text,
                'show_alert' => $show_alert,
                'url' => $url,
                'cache_time' => $cache_time
            ]);
            return $this->ret;
        }
        public function sendInlineQuery ($results) {
            self::$inlineResults = array_merge (self::$inlineResults, $results);
        }
        public function sendInline ($inline_id, $cache_time = 600, $offset = '', $switch_pm_parameter = '') {
            $this->ret = $this->callMethod ('answerInlineQuery', [
                'inline_query_id' => $inline_id,
                'results' => json_encode (self::$inlineResults),
                'cache_time' => $cache_time,
                'next_offset' => $offset,
                'switch_pm_parameter' => $switch_pm_parameter
            ]);
            return $this->ret;
        }
        public function sendChatAction ($chat_id, $action) {
            $this->ret = $this->callMethod ('sendChatAction', [
                'chat_id' => $chat_id,
                'action' => $action
            ]);
            return $this->ret;
        }
        public function getFile ($file_id) {
            $this->ret = $this->callMethod ('getFile', [
                'file_id' => $file_id,
            ]);
            if($this->ret['ok']) {
                $fileUrl = 'https://api.telegram.org/file/bot' . $this->token . '/' . $this->ret['result']['file_path'];
                $this->ret['result']['down_url'] = $fileUrl;
            }
            return $this->ret;
        }
        public function getInlineId () {
            return hash ('sha256', uniqid (mt_rand (), true));
        }
        public function getChatAdmin ($chat_id) {
            $this->ret = $this->callMethod ('getChatAdministrators', [
                'chat_id' => $chat_id
            ]);
            return $this->ret['result'];
        }
        public function getMe () {
            $this->ret = $this->callMethod ('getMe', [
            ]);
            return $this->ret;
        }
        public function getStickerSet ($name) {
            $this->ret = $this->callMethod ('getStickerSet', [
                'name' => $name
            ]);
            return $this->ret['result'];
        }
        public function isAdmin ($chat_id, $user_id) {
            $ret = false;
            $adminList = $this->getChatAdmin ($chat_id);
            foreach ($adminList as $adminList_d) {
                if ($adminList_d['user']['id'] == $user_id) {
                    $ret = true;
                    break;
                }
            }
            return $ret;
        }
        public function getMaster () {
            return MASTER;
        }
        public function getReturn () {
            return $this->ret;
        }
        public function error () {
            $this->callMethod ('sendMessage');
        }
    }
