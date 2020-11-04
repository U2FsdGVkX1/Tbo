<?php

/*
-----BEGIN INFO-----
@PluginName 备份数据库
@Description 发送 /backup 开始备份
@Author myluoluo
@AuthorEmail admin@myluoluo.com
@Version 1.2
-----END INFO-----
*/

class Backup extends Base
{
    private $serverUrl = '';
    public function __construct() {
        $this->serverUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];

        parent::__construct();
    }

    private function checkRule() {
        $chat_id = (int)$_POST["chat_id"];
        $message_id = (int)$_POST["message_id"];

        if ($this->telegram->getMaster() == '') {
            $this->telegram->sendMessage($chat_id, "未定义『主人 ID』", $message_id);
            die();
        }

        if ($chat_id != $this->telegram->getMaster()) {
            $this->telegram->sendMessage($chat_id, "你没有权限进行该操作！", $message_id);
            die();
        }

        // 避免数据量过大，导出不全的情况出现。
        ignore_user_abort(true);
        if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
            @set_time_limit(0);
        }
        @ini_set("max_execution_time", '3600');
        @ini_set('max_input_time', '3600');
    }

    /*
     * 异步执行备份任务
     */
    public function backup_callback() {
        $this->checkRule();

        $chat_id = (int)$_POST["chat_id"];
        $message_id = (int)$_POST["message_id"];

        $msg1 = $this->telegram->sendMessage($chat_id, '备份已经开始，请稍等', $message_id);
        $backupFile = $this->EXPORT_TABLES(__DIR__ . '/');

        $this->telegram->deleteMessage($chat_id, $msg1);

        $this->execUrl("{$this->serverUrl}/index.php/plugins/callback", 'POST', [
            'pcn' => 'Backup',
            'method' => 'send_backup_callback',
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'backupFile' => $backupFile,
        ]);
    }

    /*
     * 异步执行文件发送任务
     */
    public function send_backup_callback() {
        $this->checkRule();

        $chat_id = (int)$_POST["chat_id"];
        $message_id = (int)$_POST["message_id"];
        $backupFile = (string)$_POST["backupFile"];

        if(!file_exists($backupFile)) {
            $this->telegram->sendMessage($chat_id, "备份文件不存在，请检查是否有权限写入！", $message_id);
            return;
        }

        $msg2 = $this->telegram->sendMessage($chat_id, '备份完成，正在发送文件', $message_id);
        $fileCurl = new CURLFile($backupFile);
        $this->telegram->sendDocument($chat_id, $fileCurl, '#Backup ' . date('Y-m-d_H-i-s'), $message_id);

        $this->telegram->deleteMessage($chat_id, $msg2);
        unlink($backupFile);
    }

    public function command($command, $param, $message_id, $from, $chat, $date)
    {
        if ($command == '/backup') {
            $this->execUrl("{$this->serverUrl}/index.php/plugins/callback", 'POST', [
                'pcn' => 'Backup',
                'method' => 'backup_callback',
                'chat_id' => $chat["id"],
                'message_id' => $message_id
            ]);
        }
    }

    /**
     * 发起http异步请求
     * https://segmentfault.com/q/1010000003990460?_ea=450849
     * @param string $url http地址
     * @param string $method 请求方式
     * @param array $params 参数
     * @param string $ip 支持host配置
     * @param int $connectTimeout 连接超时，单位为秒
     * @throws Exception
     */
    function execUrl($url, $method = 'GET', $params = array(), $ip = null, $connectTimeout = 1)
    {
        $urlInfo = parse_url($url);

        $host = $urlInfo['host'];
        $port = isset($urlInfo['port']) ? $urlInfo['port'] : 80;
        $path = isset($urlInfo['path']) ? $urlInfo['path'] : '/';
        !$ip && $ip = $host;

        $method = strtoupper(trim($method)) !== "POST" ? "GET" : "POST";
        $params = http_build_query($params);

        if ($method === "GET" && strlen($params) > 0) {
            $path .= '?' . $params;
        }

        $fp = fsockopen($ip, $port, $errorCode, $errorInfo, $connectTimeout);

        if ($fp === false) {
            throw new Exception('Connect failed , error code: ' . $errorCode . ', error info: ' . $errorInfo);
        } else {
            $http = "$method $path HTTP/1.1\r\n";
            $http .= "Host: $host\r\n";
            $http .= "Content-type: application/x-www-form-urlencoded\r\n";
            $method === "POST" && $http .= "Content-Length: " . strlen($params) . "\r\n";
            $http .= "\r\n";
            $method === "POST" && $http .= $params . "\r\n\r\n";

            if (fwrite($fp, $http) === false || fclose($fp) === false) {
                throw new Exception('Request failed.');
            }
        }
    }

    // https://stackoverflow.com/a/35393866
    // Author: MStanley https://stackoverflow.com/users/5356839/mstanley
    // Last Modified: U2FsdGVkX1
    function EXPORT_TABLES($savePath, $tables = false)
    {
        $this->db->query('SET SQL_MODE = NO_AUTO_VALUE_ON_ZERO');
        $target_tables = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        if ($tables !== false) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n-- Database: `" . DBNAME . "`\r\n\r\n\r\n";
        foreach ($target_tables as $table) {
            $result = $this->db->query('SELECT * FROM ' . $table);
            $fields_amount = $result->columnCount();
            $rows_num = $result->rowCount();
            $res = $this->db->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch();
            $content .= "\n\n" . $TableMLine[1] . ";\n\n";
            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch()) {
                    // when started (and every after 100 command cycle):
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    // every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "\n\n\n";
        }
        $this->db->query('SET SQL_MODE = ANSI_QUOTES');
        $content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
        $backup_name = $savePath . DBNAME . "_(" . date('Y-m-d_H-i-s') . ")_rand" . rand(1, 11111111) . ".sql.gz";

        file_put_contents($backup_name, gzencode($content, 9));
        return $backup_name;
    }
}
