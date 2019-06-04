<?php
    /**
     * 模型基类
     */
    class FLModel extends FLBase {
        /** 
         * 数据库类
         * 
         * @var    medoo
         */
        static private $_instance;
        protected $db;

        /**
         * 构造类
         *
         * @return void
         */
        public function __construct () {
            if (DBHOST != '') {
                $this->db = self::getDB ();
            }
        }
        
        /**
         * 返回数据库实例
         *
         * @return medoo
         */
        private function getDB () {
            if (!self::$_instance) {
                self::$_instance = new Medoo (array (
                    'database_type' => 'mysql',
                    'database_name' => DBNAME,
                    'server' => DBHOST,
                    'username' => DBUSER,
                    'password' => DBPASS,
                    'charset' => 'utf8',
                    'option' => array (
                        PDO::ATTR_PERSISTENT => DBPERSISTENT
                    )
                ));
            }
            return self::$_instance;
        }
    }
