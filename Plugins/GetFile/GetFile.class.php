<?php

/*
-----BEGIN INFO-----
@PluginName 获取文件下载地址
@Description Maximum file size to download is 20 MB
@Author myluoluo
@AuthorEmail admin@myluoluo.com
@Version 1.0
-----END INFO-----
*/

class GetFile extends Base
{
    public function __construct ($data) {
        $this->data = $data;
        parent::__construct ();
    }

    public function document($document, $caption, $message_id, $from, $chat, $date) {
        // ⚠️ 下载地址内包含 Bot Token，请勿公开
        if($this->telegram->getMaster() == '' || $chat['id'] != $this->telegram->getMaster()) return;

        $mime_type = $document['mime_type'];
        $file_name = $document['file_name'];
        $file_id = $document['file_id'];
        $file_unique_id = $document['file_unique_id'];
        $file_size = $document['file_size'];
        if($file_size >= (20*1024*1024)) {
            $this->telegram->sendMessage($chat['id'], "Maximum file size to download is 20 MB", $message_id);
            return;
        }

        if(strstr($mime_type, 'image')) { // image/png
            $ret = $this->telegram->getFile($file_id);
            if($ret['ok']) {
                $url = $ret['result']['down_url'];
                
                $this->telegram->sendMessage($chat['id'], "下载地址: " . $url, $message_id);
            }
        } else {
            $this->telegram->sendMessage($chat['id'], "这不是一个图像文件", $message_id);
        }
    }
}
