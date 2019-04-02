<?php
class mysql_db
{
    public $link_id = null;

    public $settings = array();

    public $queryCount = 0;
    public $queryTime = '';
    public $queryLog = array();

    public $max_cache_time = 600; // 最大的缓存时间，以秒为单位

    public $cache_data_dir = 'public/runtime/app/db_caches/';
    public $root_path = '';

    public $error_message = array();
    public $platform = 'OTHER';
    public $version = '';
    public $dbhash = '';
    public $starttime = 0;
    public $timeline = 0;
    public $timezone = 0;

    public $mysql_config_cache_file_time = 0;

    public $mysql_disable_cache_tables = array(); // 不允许被缓存的表，遇到将不会进行缓存

    public $trans_status = false; //true,当前连接已经开启事务;false，未开启事务

    public $query_link = null; //只读连接

    public function __construct($dbhost, $dbuser, $dbpw, $dbname = '', $charset = 'utf8mb4', $pconnect = 0, $quiet = 0)
    {
        $this->mysql_db($dbhost, $dbuser, $dbpw, $dbname, $charset, $pconnect, $quiet);
    }

    public function mysql_db($dbhost, $dbuser, $dbpw, $dbname = '', $charset = 'utf8mb4', $pconnect = 0, $quiet = 0)
    {
        if (defined('APP_ROOT_PATH') && !$this->root_path) {
            $this->root_path = APP_ROOT_PATH;
        }

        if ($this->root_path == '') {
            $this->root_path = str_replace('system/db/db.php', '', str_replace('\\', '/', __FILE__));
        }

        $this->settings = array(
            'dbhost' => $dbhost,
            'dbuser' => $dbuser,
            'dbpw' => $dbpw,
            'dbname' => $dbname,
            'charset' => $charset,
            'pconnect' => $pconnect
        );

    }

    /**
     * 随机获得一个只读数据库连接
     * @param unknown_type $pid
     */
    public function connect_readdb($charset = 'utf8mb4')
    {
        if ($this->query_link != null) {
            return $this->query_link;
        } else {
            $c = count($GLOBALS['distribution_cfg']['DB_DISTRIBUTION']);
            if ($c == 0) {
                return null;
            } else {
                //还没有分配，只读数据库连接,则随机分配一个
                $c = $c - 1;
                $pid = mt_rand(0, $c);

                $dbhost = $GLOBALS['distribution_cfg']['DB_DISTRIBUTION'][$pid]['DB_HOST'];
                $dbport = $GLOBALS['distribution_cfg']['DB_DISTRIBUTION'][$pid]['DB_PORT'];
                $dbuser = $GLOBALS['distribution_cfg']['DB_DISTRIBUTION'][$pid]['DB_USER'];
                $dbpw = $GLOBALS['distribution_cfg']['DB_DISTRIBUTION'][$pid]['DB_PWD'];
                $dbname = $GLOBALS['distribution_cfg']['DB_DISTRIBUTION'][$pid]['DB_NAME'];
                $dbhost .= ":" . $dbport;

                $link_db = null;

                if (PHP_VERSION >= '4.2') {
                    $link_db = @mysqli_connect($dbhost, $dbuser, $dbpw, true);
                } else {
                    $link_db = @mysqli_connect($dbhost, $dbuser, $dbpw);
                }
                if ($link_db) {
                    $this->version = mysqli_get_server_info($link_db);
                    /* 如果mysql 版本是 4.1+ 以上，需要对字符集进行初始化 */
                    if ($this->version > '4.1') {
                        if ($charset != 'latin1') {
                            mysqli_query($this->link_list[$pid], "SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary");
                        }
                        if ($this->version > '5.0.1') {
                            mysqli_query($link_db, "SET sql_mode=''");
                        }
                    }
                    if ($dbname) {
                        if (mysqli_select_db($link_db, $dbname) === false) {
                            @mysqli_close($link_db);
                            $link_db = null;
                        } else {
                            return true;
                        }
                    } else {
                        @mysqli_close($link_db);
                        $link_db = null;

                    }
                }

                $this->query_link = $link_db;
                //logger::write("db_distribution_init_err:".$pid,logger::ERR,logger::FILE,"db_distribution");
                return $link_db;

            }
        }
    }

    public function connect($dbhost, $dbuser, $dbpw, $dbname = '', $charset = 'utf8mb4', $pconnect = 0, $quiet = 0)
    {
        if ($pconnect) {
            if (!($this->link_id = @mysqli_connect("p:" . $dbhost, $dbuser, $dbpw))) {
                if (!$quiet) {
                    $this->ErrorMsg("Can't Connect MySQL Server($dbhost)!");
                }

                return false;
            }
        } else {
            if (PHP_VERSION >= '4.2') {
                $this->link_id = @mysqli_connect($dbhost, $dbuser, $dbpw);
            } else {
                $this->link_id = @mysqli_connect($dbhost, $dbuser, $dbpw);
                mt_srand((double) microtime() * 1000000); // 对 PHP 4.2
                // 以下的版本进行随机数函数的初始化工作
            }
            if (!$this->link_id) {
                if (!$quiet) {
                    $this->ErrorMsg("Can't Connect MySQL Server($dbhost)!");
                }

                return false;
            }
        }

        $this->dbhash = md5($this->root_path . $dbhost . $dbuser . $dbpw . $dbname);
        $this->version = mysqli_get_server_info($this->link_id);

        /* 如果mysql 版本是 4.1+ 以上，需要对字符集进行初始化 */
        if ($this->version > '4.1') {
            if ($charset != 'latin1') {
                mysqli_query($this->link_id, "SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary");
            }
            if ($this->version > '5.0.1') {
                mysqli_query($this->link_id, "SET sql_mode=''");
            }
        }
        /*
        $sqlcache_config_file = $this->root_path . $this->cache_data_dir . 'sqlcache_config_file_' . $this->dbhash . '.php';

        $this->starttime = time ();

        if (! file_exists ( $sqlcache_config_file )) {
        if ($dbhost != '.') {
        $result = mysqli_query ( "SHOW VARIABLES LIKE 'basedir'", $this->link_id );
        $row = mysqli_fetch_assoc ( $result );
        if (! empty ( $row ['Value'] {1} ) && $row ['Value'] {1} == ':' && ! empty ( $row ['Value'] {2} ) && $row ['Value'] {2} == "\\") {
        $this->platform = 'WINDOWS';
        } else {
        $this->platform = 'OTHER';
        }
        } else {
        $this->platform = 'WINDOWS';
        }

        if ($this->platform == 'OTHER' && ($dbhost != '.' && strtolower ( $dbhost ) != 'localhost:3306' && $dbhost != '127.0.0.1:3306') || (PHP_VERSION >= '5.1' && date_default_timezone_get () == 'UTC')) {
        $result = mysqli_query ( "SELECT UNIX_TIMESTAMP() AS timeline, UNIX_TIMESTAMP('" . date ( 'Y-m-d H:i:s', $this->starttime ) . "') AS timezone", $this->link_id );
        $row = mysqli_fetch_assoc ( $result );

        if ($dbhost != '.' && strtolower ( $dbhost ) != 'localhost:3306' && $dbhost != '127.0.0.1:3306') {
        $this->timeline = $this->starttime - $row ['timeline'];
        }

        if (PHP_VERSION >= '5.1' && date_default_timezone_get () == 'UTC') {
        $this->timezone = $this->starttime - $row ['timezone'];
        }
        }

        $content = '<' . "?php\r\n" . '$this->mysqli_config_cache_file_time = ' . $this->starttime . ";\r\n" . '$this->timeline = ' . $this->timeline . ";\r\n" . '$this->timezone = ' . $this->timezone . ";\r\n" . '$this->platform = ' . "'" . $this->platform . "';\r\n?" . '>';

        @file_put_contents ( $sqlcache_config_file, $content );
        }
        @include ($sqlcache_config_file);
         */
        /* 选择数据库 */
        if ($dbname) {
            if (mysqli_select_db($this->link_id, $dbname) === false) {
                if (!$quiet) {
                    $this->ErrorMsg("Can't select MySQL database($dbname)!");
                }

                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function select_database($dbname)
    {
        return mysqli_select_db($this->link_id, $dbname);
    }

    public function set_mysql_charset($charset)
    {
        /* 如果mysql 版本是 4.1+ 以上，需要对字符集进行初始化 */
        if ($this->version > '4.1') {
            if (in_array(strtolower($charset), array('gbk', 'big5', 'utf-8', 'utf8', 'utf8mb4'))) {
                $charset = str_replace('-', '', $charset);
            }
            if ($charset != 'latin1') {
                mysqli_query($this->link_id, "SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary");
            }
        }
    }

    public function fetch_array($query, $result_type = MYSQL_ASSOC)
    {
        return mysqli_fetch_array($query, $result_type);
    }

    public function query($sql, $type = "SILENT", $is_read_db = false)
    {
        if (!IS_DEBUG && !SHOW_DEBUG) {
            $type = "SILENT";
        }

        if ($is_read_db) {
            if ($this->query_link === null) {
                $this->query_link = $this->connect_readdb();
            }

            if ($this->query_link === null) {
                if ($this->link_id === null) {
                    $this->connect($this->settings['dbhost'], $this->settings['dbuser'], $this->settings['dbpw'], $this->settings['dbname'], $this->settings['charset'], $this->settings['pconnect']);
                    $this->settings = array();
                }
                $this->query_link = $this->link_id;
            }

            $query_link = $this->query_link;
        } else {
            if ($this->link_id === null) {
                $this->connect($this->settings['dbhost'], $this->settings['dbuser'], $this->settings['dbpw'], $this->settings['dbname'], $this->settings['charset'], $this->settings['pconnect']);
                $this->settings = array();
            }

            $query_link = $this->link_id;
        }

        /* 当当前的时间大于类初始化时间的时候，自动执行 ping 这个自动重新连接操作 */
        if (PHP_VERSION >= '4.3' && time() > $this->starttime + 1) {
            mysqli_ping($query_link);
        }

        if (PHP_VERSION >= '5.0.0') {
            $begin_query_time = microtime(true);
        } else {
            $begin_query_time = microtime();
        }

        if (!($query = mysqli_query($query_link, $sql)) && $type != 'SILENT') {
            $message['message'] = 'MySQL Query Error';
            //if ($pid)
            //$message['message'] = 'MySQL Query Error:' . $pid;
            $message['sql'] = $sql;
            $message['error'] = mysqli_error($query_link);
            $message['errno'] = mysqli_errno($query_link);
            $this->error_message[] = $message;

            $this->ErrorMsg($message['message'] . ":" . $message['error'] . "<br />errno:" . $message['errno'] . "<br />sql:" . $message['sql']);

            return false;
        }
        if (PHP_VERSION >= '5.0.0') {
            $query_time = microtime(true) - $begin_query_time;
        } else {
            list($now_usec, $now_sec) = explode(' ', microtime());
            list($start_usec, $start_sec) = explode(' ', $begin_query_time);
            $query_time = ($now_sec - $start_sec) + ($now_usec - $start_usec);
        }
        $this->queryTime += $query_time;

        if ($this->queryCount++ <= 99) {
            $this->queryLog[] = $sql . " " . $query_time;
        }

        // echo
        // $sql."<br/><br/>======================================<br/><br/>";
        return $query;
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->link_id);
    }

    public function error()
    {
        return mysqli_error($this->link_id);
    }

    public function errno()
    {
        return mysqli_errno($this->link_id);
    }

    public function result($query, $row, $field = 0)
    {
        return $res->data_seek($row);
        $datarow = $res->fetch_array();
        return $datarow[$field];
    }

    public function num_rows($query)
    {
        return mysqli_num_rows($query);
    }

    public function num_fields($query)
    {
        return mysqli_num_fields($query);
    }

    public function free_result($query)
    {
        return mysqli_free_result($query);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->link_id);
    }

    public function fetchRow($query)
    {
        return mysqli_fetch_assoc($query);
    }

    public function fetch_fields($query)
    {
        return mysqli_fetch_field($query);
    }

    public function version()
    {
        return $this->version;
    }

    public function ping()
    {
        if (PHP_VERSION >= '4.3') {
            return mysqli_ping($this->link_id);
        } else {
            return false;
        }
    }

    public function escape_string($unescaped_string)
    {
        if (PHP_VERSION >= '4.3') {
            return mysqli_real_escape_string($unescaped_string);
        } else {
            return mysqli_escape_string($unescaped_string);
        }
    }

    public function close()
    {
        return mysqli_close($this->link_id);
    }

    public function ErrorMsg($message = '', $sql = '')
    {
        if ($message) {
            echo "<b>error info</b>: $message\n\n<br /><br />";
        } else {
            echo "<b>MySQL server error report:";
            print_r($this->error_message);
            //echo "<br /><br /><a href='http://faq.comsenz.com/?type=mysql&dberrno=" . $this->error_message[3]['errno'] . "&dberror=" . urlencode($this->error_message[2]['error']) . "' target='_blank'>http://faq.comsenz.com/</a>";
        }

        exit;
    }

/* 仿真 Adodb 函数 */
    public function selectLimit($sql, $num, $start = 0)
    {
        if ($start == 0) {
            $sql .= ' LIMIT ' . $num;
        } else {
            $sql .= ' LIMIT ' . $start . ', ' . $num;
        }

        return $this->query($sql);
    }

    /**
     * 检测查询语句中的表是否支持查询缓存
     * @param unknown_type $sql true:即时查询 false:缓存查询
     */
    public function is_immediate($sql, $is_immediate)
    {
        /*
        if(!$is_immediate)
        {
        if(in_array(APP_INDEX, $GLOBALS['distribution_cfg']['DB_CACHE_APP'])&&$GLOBALS['distribution_cfg']['CACHE_TYPE']!="File")
        {
        return false;
        }
        else
        {
        return true;
        }
        }
        else
        {
        if(in_array(APP_INDEX, $GLOBALS['distribution_cfg']['DB_CACHE_APP'])&&$GLOBALS['distribution_cfg']['CACHE_TYPE']!="File")
        {
        preg_match_all("/from\s+([\S]+)/", $sql,$matches);
        if($matches)
        {
        foreach($matches[1] as $k=>$v)
        {
        $table = str_replace(DB_PREFIX, "", $v);
        if(in_array($table, $GLOBALS['distribution_cfg']['DB_CACHE_TABLES']))
        {
        return false;
        }
        }
        }
        }
        }
         */
        return $is_immediate;
    }

    /**
     *
     * @param unknown_type $sql
     * @param unknown_type $is_immediate 是否为立即查 询，默认为true,则再按缓存配置读取, false时直接按指定方式
     * @return unknown|Ambigous <>|string|boolean
     */
    public function getOne($sql, $is_immediate = true, $is_read_db = false)
    {
        $immediate = $this->is_immediate($sql, $is_immediate);
        $res = false;
        if (!IS_DEBUG && !$immediate) {
            $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
            $res = $GLOBALS['cache']->get($sql);
        }
        if ($res !== false) {
            return $res;
        }

        $res = $this->query($sql, "", $is_read_db);

        if ($res !== false) {
            $row = mysqli_fetch_row($res);

            if ($row !== false) {
                if (!IS_DEBUG && !$immediate) {

                    $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
                    $GLOBALS['cache']->set($sql, $row[0], $this->max_cache_time);

                }
                return $row[0];
            } else {
                if (!IS_DEBUG && !$immediate) {

                    $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
                    $GLOBALS['cache']->set($sql, '', $this->max_cache_time);

                }
                return '';
            }
        } else {
            if (!IS_DEBUG && !$immediate) {

                $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
                $GLOBALS['cache']->set($sql, '', $this->max_cache_time);

            }
            return false;
        }
    }

    public function getOneCached($sql, $cached = 'FILEFIRST', $is_read_db = false)
    {

        $cachefirst = ($cached == 'FILEFIRST' || ($cached == 'MYSQLFIRST' && $this->platform != 'WINDOWS')) && $this->max_cache_time;

        if (!$cachefirst) {
            return $this->getOne($sql, true, $is_read_db);
        } else {
            $result = $this->getSqlCacheData($sql, $cached);
            if (empty($result['storecache']) == true) {
                return $result['data'];
            }
        }

        $arr = $this->getOne($sql, true);

        if ($arr !== false && $cachefirst) {
            $this->setSqlCacheData($result, $arr);
        }

        return $arr;
    }

    /**
     *
     * @param string $sql
     * @param boolean $is_immediate true:即时查询 false:缓存查询
     * @param boolean $is_read_db true:从只读数据库中取; false：从主数据库中取数据
     * @return unknown|multitype:multitype: |boolean
     */
    public function getAll($sql, $is_immediate = true, $is_read_db = false)
    {

        $res = false;

        if ($res !== false) {
            return $res;
        }

        $res = $this->query($sql, "", $is_read_db);

        if ($res !== false) {
            $arr = array();
            while ($row = mysqli_fetch_assoc($res)) {
                $arr[] = $row;
            }

            return $arr;
        } else {
            return false;
        }
    }

    public function getAllCached($sql, $cached = 'FILEFIRST', $is_read_db)
    {
        $cachefirst = ($cached == 'FILEFIRST' || ($cached == 'MYSQLFIRST' && $this->platform != 'WINDOWS')) && $this->max_cache_time;
        if (!$cachefirst) {
            return $this->getAll($sql, true, $is_read_db);
        } else {
            $result = $this->getSqlCacheData($sql, $cached);
            if (empty($result['storecache']) == true) {
                return $result['data'];
            }
        }

        $arr = $this->getAll($sql, true, $is_read_db);

        if ($arr !== false && $cachefirst) {
            $this->setSqlCacheData($result, $arr);
        }

        return $arr;
    }

    public function getRow($sql, $is_immediate = true, $is_read_db = false)
    {
        $immediate = $this->is_immediate($sql, $is_immediate);
        $res = false;
        if (!IS_DEBUG && !$immediate) {

            $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
            $res = $GLOBALS['cache']->get($sql);

        }
        if ($res !== false) {
            return $res;
        }

        $res = $this->query($sql, "", $is_read_db);

        if ($res !== false) {
            $res = mysqli_fetch_assoc($res);
            if (!IS_DEBUG && !$immediate) {

                $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
                if ($res) {
                    $GLOBALS['cache']->set($sql, $res, $this->max_cache_time);
                } else {
                    $GLOBALS['cache']->set($sql, '', $this->max_cache_time);
                }

            }
            return $res;
        } else {
            if (!IS_DEBUG && !$immediate) {
                $GLOBALS['cache']->set_dir(APP_ROOT_PATH . $this->cache_data_dir);
                $GLOBALS['cache']->set($sql, '', $this->max_cache_time);
            }
            return false;
        }
    }

    public function getRowCached($sql, $cached = 'FILEFIRST', $is_read_db = false)
    {

        $cachefirst = ($cached == 'FILEFIRST' || ($cached == 'MYSQLFIRST' && $this->platform != 'WINDOWS')) && $this->max_cache_time;
        if (!$cachefirst) {
            return $this->getRow($sql, true, $is_read_db);
        } else {
            $result = $this->getSqlCacheData($sql, $cached);
            if (empty($result['storecache']) == true) {
                return $result['data'];
            }
        }

        $arr = $this->getRow($sql, true, $is_read_db);

        if ($arr !== false && $cachefirst) {
            $this->setSqlCacheData($result, $arr);
        }

        return $arr;
    }

    /**
     * 针对数据的查询缓存返回的当前时间戳，用于查询
     * @param unknown_type $time
     */
    public function getCacheTime($time)
    {
        return intval($time / $this->max_cache_time) * $this->max_cache_time;
    }

    public function getCol($sql, $is_read_db = false)
    {
        $res = $this->query($sql, "", $is_read_db);
        if ($res !== false) {
            $arr = array();
            while ($row = mysqli_fetch_row($res)) {
                $arr[] = $row[0];
            }

            return $arr;
        } else {
            return false;
        }
    }

    public function getColCached($sql, $cached = 'FILEFIRST', $is_read_db = false)
    {
        $cachefirst = ($cached == 'FILEFIRST' || ($cached == 'MYSQLFIRST' && $this->platform != 'WINDOWS')) && $this->max_cache_time;
        if (!$cachefirst) {
            return $this->getCol($sql, $is_read_db);
        } else {
            $result = $this->getSqlCacheData($sql, $cached);
            if (empty($result['storecache']) == true) {
                return $result['data'];
            }
        }

        $arr = $this->getCol($sql, $is_read_db);

        if ($arr !== false && $cachefirst) {
            $this->setSqlCacheData($result, $arr);
        }

        return $arr;
    }

    public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '')
    {
        $field_names = $this->getCol('DESC ' . $table);

        $sql = '';
        if ($mode == 'INSERT') {
            $fields = $values = array();
            foreach ($field_names as $value) {
                if (@array_key_exists($value, $field_values) == true) {
                    $fields[] = $value;
                    $field_values[$value] = stripslashes($field_values[$value]);
                    $values[] = "'" . addslashes($field_values[$value]) . "'";
                }
            }

            if (!empty($fields)) {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        } else {
            $sets = array();
            foreach ($field_names as $value) {
                if (array_key_exists($value, $field_values) == true) {
                    $field_values[$value] = stripslashes($field_values[$value]);
                    $sets[] = $value . " = '" . addslashes($field_values[$value]) . "'";
                }
            }

            if (!empty($sets)) {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }

        if ($sql) {
            return $this->query($sql, $querymode);
        } else {
            return false;
        }
    }

    public function autoReplace($table, $field_values, $update_values, $where = '', $querymode = '')
    {
        $field_descs = $this->getAll('DESC ' . $table);

        $primary_keys = array();
        foreach ($field_descs as $value) {
            $field_names[] = $value['Field'];
            if ($value['Key'] == 'PRI') {
                $primary_keys[] = $value['Field'];
            }
        }

        $fields = $values = array();
        foreach ($field_names as $value) {
            if (array_key_exists($value, $field_values) == true) {
                $fields[] = $value;
                $values[] = "'" . $field_values[$value] . "'";
            }
        }

        $sets = array();
        foreach ($update_values as $key => $value) {
            if (array_key_exists($key, $field_values) == true) {
                if (is_int($value) || is_float($value)) {
                    $sets[] = $key . ' = ' . $key . ' + ' . $value;
                } else {
                    $sets[] = $key . " = '" . $value . "'";
                }
            }
        }

        $sql = '';
        if (empty($primary_keys)) {
            if (!empty($fields)) {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        } else {
            if ($this->version() >= '4.1') {
                if (!empty($fields)) {
                    $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                    if (!empty($sets)) {
                        $sql .= 'ON DUPLICATE KEY UPDATE ' . implode(', ', $sets);
                    }
                }
            } else {
                if (empty($where)) {
                    $where = array();
                    foreach ($primary_keys as $value) {
                        if (is_numeric($value)) {
                            $where[] = $value . ' = ' . $field_values[$value];
                        } else {
                            $where[] = $value . " = '" . $field_values[$value] . "'";
                        }
                    }
                    $where = implode(' AND ', $where);
                }

                if ($where && (!empty($sets) || !empty($fields))) {
                    if (intval($this->getOne("SELECT COUNT(*) FROM $table WHERE $where")) > 0) {
                        if (!empty($sets)) {
                            $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
                        }
                    } else {
                        if (!empty($fields)) {
                            $sql = 'REPLACE INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                        }
                    }
                }
            }
        }

        if ($sql) {
            return $this->query($sql, $querymode);
        } else {
            return false;
        }
    }

    public function setMaxCacheTime($second)
    {
        $this->max_cache_time = $second;
    }

    public function getMaxCacheTime()
    {
        return $this->max_cache_time;
    }

    public function getSqlCacheData($sql, $cached = '')
    {
        $sql = trim($sql);

        $result = array();
        $result['filename'] = $this->root_path . $this->cache_data_dir . 'sqlcache_' . abs(crc32($this->dbhash . $sql)) . '_' . md5($this->dbhash . $sql) . '.php';

        $result['data'] = $GLOBALS['cache']->get($result['filename']);
        if ($result['data'] === false) {
            $result['storecache'] = true;
        } else {
            $result['storecache'] = false;
        }
        return $result;
    }

    public function setSqlCacheData($result, $data)
    {
        if ($result['storecache'] === true && $result['filename']) {
            $GLOBALS['cache']->set($result['filename'], $data, $this->max_cache_time);
        }
    }

    /* 获取 SQL 语句中最后更新的表的时间，有多个表的情况下，返回最新的表的时间 */
    public function table_lastupdate($tables)
    {
        if ($this->link_id === null) {
            $this->connect($this->settings['dbhost'], $this->settings['dbuser'], $this->settings['dbpw'], $this->settings['dbname'], $this->settings['charset'], $this->settings['pconnect']);
            $this->settings = array();
        }

        $lastupdatetime = '0000-00-00 00:00:00';

        $tables = str_replace('`', '', $tables);
        $this->mysql_disable_cache_tables = str_replace('`', '', $this->mysql_disable_cache_tables);

        foreach ($tables as $table) {
            if (in_array($table, $this->mysql_disable_cache_tables) == true) {
                $lastupdatetime = '2037-12-31 23:59:59';

                break;
            }

            if (strstr($table, '.') != null) {
                $tmp = explode('.', $table);
                $sql = 'SHOW TABLE STATUS FROM `' . trim($tmp[0]) . "` LIKE '" . trim($tmp[1]) . "'";
            } else {
                $sql = "SHOW TABLE STATUS LIKE '" . trim($table) . "'";
            }
            $result = mysqli_query($this->link_id, $sql);

            $row = mysqli_fetch_assoc($result);
            if ($row['Update_time'] > $lastupdatetime) {
                $lastupdatetime = $row['Update_time'];
            }
        }
        $lastupdatetime = strtotime($lastupdatetime) - $this->timezone + $this->timeline;

        return $lastupdatetime;
    }

    public function get_table_name($query_item)
    {
        $query_item = trim($query_item);
        $table_names = array();

        /* 判断语句中是不是含有 JOIN */
        if (stristr($query_item, ' JOIN ') == '') {
            /* 解析一般的 SELECT FROM 语句 */
            if (preg_match('/^SELECT.*?FROM\s*((?:`?\w+`?\s*\.\s*)?`?\w+`?(?:(?:\s*AS)?\s*`?\w+`?)?(?:\s*,\s*(?:`?\w+`?\s*\.\s*)?`?\w+`?(?:(?:\s*AS)?\s*`?\w+`?)?)*)/is', $query_item, $table_names)) {
                $table_names = preg_replace('/((?:`?\w+`?\s*\.\s*)?`?\w+`?)[^,]*/', '\1', $table_names[1]);

                return preg_split('/\s*,\s*/', $table_names);
            }
        } else {
            /* 对含有 JOIN 的语句进行解析 */
            if (preg_match('/^SELECT.*?FROM\s*((?:`?\w+`?\s*\.\s*)?`?\w+`?)(?:(?:\s*AS)?\s*`?\w+`?)?.*?JOIN.*$/is', $query_item, $table_names)) {
                $other_table_names = array();
                preg_match_all('/JOIN\s*((?:`?\w+`?\s*\.\s*)?`?\w+`?)\s*/i', $query_item, $other_table_names);

                return array_merge(array($table_names[1]), $other_table_names[1]);
            }
        }

        return $table_names;
    }

    /* 设置不允许进行缓存的表 */
    public function set_disable_cache_tables($tables)
    {
        if (!is_array($tables)) {
            $tables = explode(',', $tables);
        }

        foreach ($tables as $table) {
            $this->mysql_disable_cache_tables[] = $table;
        }

        array_unique($this->mysql_disable_cache_tables);
    }

    /**
     * 判断当前连接是否，已经开启事务
     */
    public function InTransaction()
    {
        return $this->trans_status;
    }

    /**
     * 开启事务
     * return boolean $pInTrans
     */
    public function StartTrans()
    {
        $pInTrans = true;
        if ($this->InTransaction()) {
            $pInTrans = false;
        } else {
            $this->query("start transaction");
            $this->trans_status = true;
            $pInTrans = true;

        }
        return $pInTrans;
    }

    /**
     * 提交事务
     * @param boolean $pInTrans
     */
    public function Commit($pInTrans)
    {
        if ($pInTrans && $this->InTransaction()) {
            $this->query("commit");
            $this->trans_status = false;
        }
    }

    /**
     *回滚事务
     * @param boolean $pInTrans
     */
    public function Rollback($pInTrans)
    {
        if ($pInTrans && $this->InTransaction()) {
            $this->query("rollback");
            $this->trans_status = false;
        }
    }
}
