<?php

/*
-----BEGIN INFO-----
@PluginName Chevereto 图床 API
@Description 回复某个图像 /upimg 上传 <br />API 需要为 Chevereto 管理员，仪表盘设置页面内获取
@Author myluoluo
@AuthorEmail admin@myluoluo.com
@Version 1.0
-----END INFO-----
*/

class Chevereto extends Base
{
    public function command($command, $param, $message_id, $from, $chat, $date)
    {
        if ($command == '/upimg') {
            # region 仅允许 Tbo 主人上传
            if ($this->telegram->getMaster() == '') {
                $this->telegram->sendMessage($chat['id'], "未定义『主人 ID』", $message_id);
                return;
            }
            if ($chat['id'] != $this->telegram->getMaster()) {
                $this->telegram->sendMessage($chat['id'], "你没有权限进行该操作！", $message_id);
                return;
            }
            # endregion

            #region 配置
            if(count($param) == 3 && $param[0] == 'set') {
                if($param[1] == 'host')
                    $this->option->iou('chevereto_host', $param[2]);
                if($param[1] == 'key')
                    $this->option->iou('chevereto_key', $param[2]);
                $this->telegram->sendMessage($chat['id'], "Chevereto API {$param[1]} 配置变更为\n`{$param[2]}`", $message_id, [], 'Markdown', false);
                return;
            }
            $cheveretoHost = $this->option->getvalue('chevereto_host');
            $cheveretoKey = $this->option->getvalue('chevereto_key');
            if(empty($cheveretoHost) || empty($cheveretoKey)) {
                $this->telegram->sendMessage($chat['id'], "未设置 Chevereto API 信息，回复以下格式设置：\n`/upimg set host http://mysite.com/\n/upimg set key 12345`", $message_id, [], 'Markdown', false);
                return;
            }
            #endregion

            if(!isset($chat['reply_to_message'])) {
                $this->telegram->sendMessage($chat['id'], "操作必须包含引用", $message_id);
                return;
            }

            $file = [];
            if(isset($chat['reply_to_message']['photo']) && count($chat['reply_to_message']['photo']) > 0) {
                $file = $chat['reply_to_message']['photo'];
                $file = $file[count($file) - 1];
            }

            if(empty($file)) {
                $this->telegram->sendMessage($chat['id'], "类型错误，接受 photo 类型，请不要发送文件", $message_id);
                return;
            }

            ignore_user_abort(true);
            if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
                @set_time_limit(0);
            }
            @ini_set("max_execution_time", '3600');
            @ini_set('max_input_time', '3600');

            // 获取文件下载地址
            $fileUrl = $this->telegram->getFile($file['file_id']);
            if(!isset($fileUrl['result']['down_url'])) {
                $this->telegram->sendMessage($chat['id'], "文件地址获取失败，错误信息：" . (isset($fileUrl['description']) ? $fileUrl['description'] : ""), $message_id);
                return;
            }
            $fileUrl = $fileUrl['result']['down_url'];
            $msg = $this->telegram->sendMessage($chat['id'], "正在请求 API 传输文件，请稍等……", $message_id);

            // 请求 Chevereto API
            $apiRes = $this->fetch($cheveretoHost . "/api/1/upload/?key={$cheveretoKey}&format=json&source={$fileUrl}");
            $apiRes = json_decode($apiRes, true);
            if($apiRes['status_code'] == 200 && $apiRes['success']['code'] == 200) {
                $apiRes = $apiRes['image'];
                $imgMsg = "文件上传成功  \n直链: [{$apiRes['url']}]({$apiRes['url']})  \n短连接: [{$apiRes['url_short']}]({$apiRes['url_short']})  \nMarkdown: `![]({$apiRes['url']})`";
                $this->telegram->sendMessage($chat['id'], $imgMsg, $message_id, [], 'Markdown', true);
                $this->telegram->deleteMessage($chat['id'], $msg);
            } else {
                $this->telegram->editMessage($chat['id'], $msg, "文件上传失败，错误代码：{$apiRes['status_code']}\n错误信息：{$apiRes['error']['message']}");
            }
        }
    }

    public function photo($photo, $caption, $message_id, $from, $chat, $date) {
        // 接受 Tbo 主人转发来的图像直接上传
        if($this->telegram->getMaster() != ''
            && $chat['id'] == $this->telegram->getMaster()
            && !empty($photo))
        {
            $chat['reply_to_message'] = ['photo' => $photo];
            // 跳跃到命令
            $this->command('/upimg', [], $message_id, $from, $chat, $date);
        }
    }

    public function  uninstall() {
        // 插件被卸载时删除配置项
        $this->option->delete('chevereto_host');
        $this->option->delete('chevereto_key');
    }
}
