<?php

/**
    @version 3.5.4
    * works with php mysqli_* functions
    * created by Janis Rublevskis <janis@xit.lv>
    * pardon my English. I code better than write :D
    @since 2010
    @last updated 2014.06.19
*/
Class DB {
    /**
     * mysqli link
     */
    private $link         = null;
    /**
     * database prefix
     */
    private $prefix;
    /**
     *  SELECT elements. If false then * is used (all elements)
     */
    private $select       = null;
    /**
     * array with WHERE statement. If false, none is used
     */
    private $where        = null;
    /**
     * array with WHERE IN statements. Extends WHERE statement if not false
     */
    private $where_in     = null;
    /**
     * array with WHERE statement for match function. If false, none is used
     */
    private $match        = null;
    /**
     * array with ORDER BY statements
     */
    private $order_by     = [];
    /**
     * GROUP BY statement
     */
    private $group_by     = '';
    /**
     * contains result object of last query.
     */
    protected $result       = null;
    /**
     * same as $result, but this is array instead of object
     */
    protected $result_array = null;
    /**
     * JOIN statements
     */
    private $join         = [];
    /**
     * table to use
     */
    private $from         = '';
    protected $table      = '';
    /**
     * last executed or emulated query
     */
    private $last_query   = null;
    /**
     * contains LIMIT statement
     */
    private $limit        = '';

    protected $dbName = null;

    public static $instance = null;

    /**
     * Class constructor - makes mysqli connection and sets prefix
     */
    function __construct() {
        if(is_null(self::$instance)) {
            $this->dbName = Config::get('database.name');
            $this->connect();
            $this->setPrefix(Config::get('database.prefix'));
            self::$instance = [
                'link' => $this->link,
                'db' => $this->dbName,
                'prefix' => $this->prefix
            ];
        } else {
            $this->link = self::$instance['link'];
            $this->dbName = self::$instance['db'];
            $this->setPrefix(self::$instance['prefix']);
        }
    }

    /**
     * Clears previous queries data
     */
    private function clear() {
        $this->select   = null;
        $this->where    = null;
        $this->order_by = array();
        $this->group_by = '';
        $this->join     = array();
        $this->limit    = '';
        $this->where_in = null;
    }

    /**
     * Sets table prefix
     * @param string $prefix
     * @return string
     */
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
        return $prefix;
    }

    /**
     * gets table prefix
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * prefixes table
     * @param string $table
     * @return string
     */
    public function prefix($table) {
        return $this->escape($this->prefix.$table);
    }

    /**
     * makes mysqli connection
     */
    private function connect() {
        $this->link = @mysqli_connect(
            Config::get('database.host'),
            Config::get('database.user'),
            Config::get('database.password'),
            Config::get('database.name')
        );
        if(!$this->link) {
            $message = 'DB Connection error ('.mysqli_connect_errno() . '): ' . mysqli_connect_error();
            if(isCLI()) {
                (new Console())->error($message); exit(1);
            } else {
                die($message);
            }
        }
    }

    /**
     * makes mysqli query
     * @param string $query
     * @return false|mysqli_result object
     */
    public function query($query) {
        $this->clear();
        $this->last_query = $query;
        $res = mysqli_query($this->link, $query)
            or die("<pre>Query error: \n".$query."\n".mysqli_error($this->link).'</pre>');
        return $res;
    }

    /**
     * gets data from query and sets it to private data
     * @param string $query
     * @return db
     */
    public function getQuery($query) {
        $res = $this->query($query);
        $ret_arr = array();
        $ret_arr_arr = array();
        $id = 0;
        while($row = mysqli_fetch_assoc($res)) {
            foreach($row as $key => $val) {
                if(!isset($ret_arr[$id])) {
                    $ret_arr_arr[$id] = array();
                    $ret_arr[$id] = new stdClass();
                }
                $ret_arr_arr[$id][$key] = $val;
                $ret_arr[$id]->$key = $val;
            }
            $id++;
        }
        $this->result = $ret_arr;
        $this->result_array = $ret_arr_arr;
        return $this;
    }

    /**
     * Execute query builder and query itself
     * @param integer $limit
     * @param integer $offset
     * @return db
     */
    public function get($limit = null, $offset = 0) {
        $query = $this->buildQuery($limit, $offset);
        $this->getQuery($query);
        return $this;
    }

    /**
     * Makes insert into table
     * @param array|object $data
     * @return false|object
     */
    public function insert($data) {
        $cols = '';
        $vals = '';
        foreach($data as $col => $val) {
            $cols .= ' `'.$this->escape($col).'`,';
            $vals .= ' "'.$this->escape($val).'",';
        }
        $cols = trim($cols, ',');
        $vals = trim($vals, ',');
        $q = sprintf('INSERT INTO %s (%s) VALUES (%s)'
            , $this->from
            , $cols
            , $vals
        );
        $this->clear();
        $this->last_query = $q;
        return $this->query($q);
    }

    /**
     * Gives last inserted rows id
     * @return integer
     */
    public function insertId() {
        return mysqli_insert_id($this->link);
    }

    /**
     * Executes UPDATE query
     * @param array $data
     * @param integer $limit
     * @param integer $offset
     * @return db
     */
    public function update($data, $limit = null, $offset = 0) {
        $this->result = null;
        $query = $this->buildQuery($limit, $offset, $data);
        $this->result = $this->query($query);
        $this->clear();
        return $this;
    }

    public function delete($limit = null, $offset = 0) {
        $query = $this->buildQuery($limit, $offset, null, 'delete');
        return $this->query($query);
    }

    /**
     * Executes multiple queries
     * @param string $queries
     * @param bool $free
     * @return FALSE|object
     */
    public function multiQuery($queries, $free = false)
    {
        $this->clear();
        $this->last_query = $queries;
        $res = mysqli_multi_query($this->link, $queries)
            or die('Could not process query: '.mysqli_error($this->link)."\n in query: ".$this->last_query);
        if($free) {
            do {
                mysqli_next_result($this->link);
                if ($result = mysqli_store_result($this->link)) {
                    mysqli_free_result($result);
                }
            } while(mysqli_more_results($this->link));
        }
        return $res;
    }

    /**
     * Gets data from multi query
     * @param string $query
     * @return db
     */
    public function getMultiQuery($query) {
        $this->multiQuery($query);
        $ret_arr = array();
        $ret_arr_arr = array();
        $id = 0;
        do {
            /* store first result set */
            mysqli_next_result($this->link);
            if ($result = mysqli_store_result($this->link)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    foreach($row as $key => $val) {
                        if(!isset($ret_arr[$id])) {
                            $ret_arr_arr[$id] = array();
                            $ret_arr[$id] = new stdClass();
                        }
                        $ret_arr_arr[$id][$key] = $val;
                        $ret_arr[$id]->$key = $val;
                    }
                    $id++;
                }
                mysqli_free_result($result);
            }
        } while(mysqli_more_results($this->link));

        $this->result = $ret_arr;
        $this->result_array = $ret_arr_arr;
        $this->clear();
        return $this;
    }

    /**
     * returns result array with objects from last query
     * @return array of objects
     */
    public function result() {
        return $this->result;
    }

    /**
     * returns one row from last query results. Default is first row.
     * Indexing starts with 0
     * @param integer $index
     * @return object
     */
    public function row($index = 0) {
        if($this->result) {
            if(isset($this->result[$index])) {
                return $this->result[$index];
            }
        }
        return null;
    }

    /**
     * @return object
     */
    public function first() {
        return $this->row(0);
    }

    /**
     * returns result array from last query with data as array
     * @return array
     */
    public function resultArray() {
        return $this->result_array;
    }

    /**
     * returns one row from last query as array
     * @param int $id
     * @return array
     */
    public function rowArray($id = 0) {
        return isset($this->result_array[$id]) ? $this->result_array[$id] : false;
    }

    /**
     * returns first element of first query from last result set.
     * @return mixed
     */
    public function scalar() {
        if($this->result_array) {
            $keys = array_keys($this->result_array[0]);
            return $this->result_array[0][$keys[0]];
        }
        return false;
    }

    /**
     * returns array of first elements from last results
     * @return array
     */
    public function scalarArray()
    {
        if($this->result_array) {
            $arr = array();
            $keys = array_keys($this->result_array[0]);
            foreach($this->result_array as $v) {
                $arr[] = $v[$keys[0]];
            }
            return $arr;
        }
        return [];
    }

    /**
     * emulates query just for building string for debuging reasons.
     * @param integer $limit
     * @param integer $offset
     * @param bool|array $data - if $data is array UPDATE statement will be generated
     * @return string
     */
    public function emulate($limit = null, $offset = 0, $data = false) {
        $query = $this->buildQuery($limit, $offset, $data);
        $this->clear();
        return $query;
    }

    /**
     * returns last query as string
     * @return string
     */
    public function lastQuery() {
        return $this->last_query;
    }

    /**
     * helper function for lazy ones - will generate count query
     * @param string $field
     * @return integer
     */
    public function count($field = 'id')
    {
        if(stristr($field, 'DISTINCT')) {
            $field = preg_replace('|^distinct (.*)$|i', 'DISTINCT `$1`', $field);
        } else {
            $field = '`'.$field.'`';
        }
        return (int)$this->select('COUNT('.$field.') as ct')->get()->scalar();
    }

    /**
     * trying to escape SQL injections
     * @param $str
     * @return string
     */
    public function escape($str) {
        return mysqli_real_escape_string($this->link, $str);
    }

    /**
     * sets SELECT variables
     * @param string $select
     * @return db
     */
    public function select($select) {
        $this->select = $select;
        return $this;
    }

    /**
     * set table FROM. If written in form "table a", result will be "`prefix_table` as a"
     * @param string $table
     * @return db
     */
    public function from($table) {
        $table = explode(' ', $table);
        if(count($table) > 1) {
            $this->from = '`'.self::prefix($table[0]).'` as '.self::escape($table[1]);
        } else {
            $this->from = '`'.self::prefix($table[0]).'`';
        }
        return $this;
    }

    public function table($table) {
        return $this->from($table);
    }

    /**
     * joins tables
     * @param string $table
     * @param string $on
     * @param string $type - default INNER
     * @return db
     */
    public function join($table, $on, $type = 'INNER') {
        $table = explode(' ', $table);
        $q = strtoupper($type).' JOIN ';
        if(count($table) > 1) {
            $q .= '`'.$this->prefix($table[0]).'` as '.$this->escape($table[1]);
        } else {
            $q .= '`'.$this->prefix($table[0]).'`';
        }
        $q .= ' ON ('.$on.')';
        $this->join[] = $q;
        return $this;
    }

    /**
     * builds WHERE statements variables
     * $key can be used as associative array so array keys will be table columns
     * and value will be value. In that case, $value param is not needed.
     * complaining parameters need to be set in key field like "age <" or "age !=" etc.
     * @param string|array $key
     * @param string $value
     * @return db
     */
    public function where($key, $value = null) {
        if(!$this->where) {
            $this->where = array();
        }
        if(!is_array($key)) {
            $key = array($key => $value);
        }
        foreach($key as $k => $v) {
            $this->where[$k] = $v;
        }
        return $this;
    }

    /**
     * builds WHERE MATCH AGAINST statement variables
     * $field can be array but will be used in one MATCH statement
     * @param string|array $field
     * @param string $value
     * @return db
     */
    public function match($field, $value) {
        if(!$this->match) {
            $this->match = array();
        }
        if(!is_array($field)) {
            $field = array($field);
        }
        $this->match['`'.$this->escape(implode('`,`', $field)).'`'] = $this->escape($value);
        return $this;
    }

    /**
     * builds WHERE IN statements variables
     * $key can be used as associative array so array keys will be table columns
     * and value will be value. In that case, $value param is not needed.
     * @param string|array $key
     * @param string $value
     * @return db
     */
    public function whereIn($key, $value = null) {
        if(!$this->where_in) {
            $this->where_in = array();
        }
        if(!is_array($key)) {
            $key = array($key => $value);
        }
        foreach($key as $k => $v) {
            $this->where_in[$k] = $v;
        }
        return $this;
    }

    /**
     * build params for ORDER BY statement
     * for random results, set $column to "rand"
     * $column can be used as associative array so array keys will be table columns
     * and value will be order d. In that case, $direction param is not needed.
     * @param string|array $column
     * @param string $direction
     * @return db
     */
    public function orderBy($column, $direction = 'asc') {
        if($column == 'rand') {
            $this->order_by[] = 'rand';
        } else {
            if(!is_array($column)) {
                $column = array($column => $direction);
            }
            foreach($column as $col => $d) {
                $this->order_by[] = array($col, $d);
            }
        }
        return $this;
    }

    /**
     * build params for GROUP BY statement
     * multiple columns can be added as array or as coma seperated string
     * @param string|array $column
     * @return db
     */
    function groupBy($column) {
        if(!is_array($column)) {
            $column = array($column);
        }
        foreach ($column as $value) {
            $this->group_by .= ', `'.str_replace('.', '`.`', $value).'`';
        }
        $this->group_by = trim($this->group_by, ', ');
        return $this;
    }

    /**
     * sets query result limit and offset
     * @param integer $limit
     * @param integer $offset
     * @return db
     */
    public function limit($limit = null, $offset = 0) {
        if($limit) {
            $this->limit = 'LIMIT '.floor($offset).', '.floor($limit);
        } else {
            $this->limit = '';
        }
        return $this;
    }

	/**
	 * builds query string from all previously set parameters
	 * if $data is array, it will be UPDATE statement
	 * otherwise SELECT statement will be generated
	 *
	 * @param integer $limit
	 * @param integer $offset
	 * @param array|bool $data
	 * @param string $type
	 *
	 * @return string
	 */
    private function buildQuery($limit, $offset, $data = false, $type = 'select')
    {
        if(!is_null($limit)) {
            $this->limit($limit, $offset);
        }
        $this->result = false;
        $where = '';
        if(is_array($this->where)) {
            foreach($this->where as $field => $value) {
                $params = explode(' ', $field);
                if(!isset($params[1])) {
                    $params[1] = '=';
                }
                $where .= ' `'.$this->escape(str_replace('.', '`.`', $params[0])).'` '.$this->escape($params[1]).' "'.$this->escape($value).'" AND';
            }
        }
        if(is_array($this->where_in)) {
            foreach($this->where_in as $field => $value) {
                $w_st = $value;
                if(is_array($value)) {
                    $w_st = '';
                    foreach($value as $v) {
                        $w_st .= $v.',';
                    }
                    $w_st = trim($w_st, ',');
                }
                $where .= ' `'.$this->escape(str_replace('.', '`.`', $field)).'` IN('.$this->escape($w_st).') AND';
            }
        }

        if(is_array($this->match)) {
            foreach($this->match as $field => $value) {
                $where .= ' MATCH ('.$field.') AGAINST ("'.$value.'") AND';
            }
        }

        $where = trim($where, ' AND');
        $order_by = '';
        foreach($this->order_by as $ob) {
            if(!is_array($ob) && $ob == 'rand') {
                $order_by .= ' RAND(),';
            } else {
                if(count(explode('.', $ob[0])) > 1) {
                    $order_by .= ' '.$this->escape($ob[0]).' '.(isset($ob[1]) ? $ob[1] : 'asc').',';
                } else {
                    $order_by .= ' `'.$this->escape($ob[0]).'` '.(isset($ob[1]) ? $ob[1] : 'asc').',';
                }
            }
        }
        $order_by = trim($order_by, ',');

        if($data) {
            $update_data = '';
            foreach($data as $col => $val) {
                $quotes = true;
                if(preg_match('/^~(.*)$/i', $val, $r)) {
                    $quotes = false;
                    $val = $r[1];
                }
                $update_data .= ' `'.$this->escape($col).'` = '.($quotes ? '"' : '').$this->escape($val).($quotes ? '"' : '').',';
            }
            $update_data = trim($update_data, ',');

            return sprintf("UPDATE %s SET %s %s %s %s %s"
                , $this->from
                , $update_data
                , empty($where) ? '' : 'WHERE '.$where
                , empty($this->group_by) ? '' : 'GROUP BY '.$this->group_by
                , empty($this->order_by) ? '' : 'ORDER BY '.$order_by
                , $limit ? 'LIMIT '.floor($limit) : ''
            );
        } else {
            switch($type) {
                case "delete":
                    return sprintf("DELETE FROM %s %s %s %s %s %s"
                        , $this->from
                        , count($this->join) ? implode(" \n", $this->join) : ''
                        , empty($where) ? '' : 'WHERE '.$where
                        , empty($this->group_by) ? '' : 'GROUP BY '.$this->group_by
                        , empty($this->order_by) ? '' : 'ORDER BY '.$order_by
                        , $this->limit
                    );
                    break;
                default:
                    return sprintf("SELECT %s FROM %s %s %s %s %s %s"
                        , ($this->select ? $this->escape($this->select) : '*')
                        , $this->from
                        , count($this->join) ? implode(" \n", $this->join) : ''
                        , empty($where) ? '' : 'WHERE '.$where
                        , empty($this->group_by) ? '' : 'GROUP BY '.$this->group_by
                        , empty($this->order_by) ? '' : 'ORDER BY '.$order_by
                        , $this->limit
                    );
            }
        }
    }

    public function max($field = 'id') {
        return $this->select('MAX('.self::escape($field).')')->get()->scalar();
    }

    /**
     * SCHEMA functions
     */

    /**
     * Checks if table exists
     * @param string $table
     * @return boolean
     */
    public function tableExists($table){
        $sql = "show tables like '".$this->prefix.$table."'";
        $res = $this->getQuery($sql)->row();
        return $res != false;
    }

    /**
     * drops provided table if exists
     * @param string $table
     * @return false|object
     */
    public function drop($table)
    {
        return $this->query('DROP TABLE IF EXISTS '.$this->prefix($table));
    }

    /**
     * @param null $table
     * @return array
     */
    public function fields($table = null) {
        if($table) {
            $t = self::prefix($table);
        } else {
            $t = $this->from;
        }
        $this->getQuery('SHOW COLUMNS FROM '.$t. ' FROM '.$this->dbName);
	    return $this->result;
    }

    public function createFields($fields) {
        $fieldQuery = '';
        $primary = null;
        foreach($fields as $field => $params) {
            $fieldQuery .= '`'.$this->escape($field).'` ';
            $params = explode("|", $params);
            $p = explode(":", $params[0]);
            $extra = '';
            for($i = 1; $i < count($params); $i++) {
                $e = explode(":", $params[$i]);
                switch($e[0]) {
                    case 'nullable':
                        $extra = ' DEFAULT NULL';
                        break;
                    case 'default':
                        $extra = " NOT NULL DEFAULT '".$e[1]."'";
                }
            }

            switch($p[0]) {
                case "increments":
                    $fieldQuery .= 'int(10) unsigned NOT NULL AUTO_INCREMENT, ';
                    $primary = $p;
                    break;
                case "string":
                    $fieldQuery .= 'varchar('.arrayGet($p, 1, 100).') ' . $extra . ', ';
                    break;
                case "text":
                    $fieldQuery .= 'text ' . $extra . ', ';
                    break;
                case "integer":
                    $fieldQuery .= 'int('.arrayGet($p, 1, 10).') ' . $extra . ', ';
                    break;
                case "bigInteger":
                    $fieldQuery .= 'bigint('.arrayGet($p, 1, 20).') ' . $extra . ', ';
                    break;
                case "timestamp":
                    $fieldQuery .= "timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', ";
                    break;
            }

        }

        if(!is_null($primary)) {
            $fieldQuery .= "PRIMARY KEY (`id`), ";
        }
        $fieldQuery = trim($fieldQuery, ', ');
        return $fieldQuery;
    }

    public function createIndexes($table, $indexes) {
        $indQuery = '';
        foreach($indexes as $k => $ind) {
            switch($k) {
                case "key":
                    foreach($ind as $idx) {
                        if(!is_array($idx)) {
                            $idx = [$idx];
                        }
                        $name = $this->prefix($table).'_'.implode('_', $idx).'_index';
                        $keys = '`'.implode('`,`', $idx).'`';
                        $indQuery .= 'KEY `'.$name.'` ('.$keys.'), ';
                    }
                    break;
                case "primary":
                    $indQuery .= "PRIMARY KEY (`".$ind."`), ";
                    break;
            }

        }
        if(!empty($indQuery))
            $indQuery = ', '.trim($indQuery, ', ');
        return $indQuery;
    }

    public function createTable($table, $fields, $index = []) {

        if(is_null($table)) return false;
        $query = "CREATE TABLE `".$this->prefix($table).'` (';
        $query .= $this->createFields($fields);
        $query .= $this->createIndexes($table, $index);
        $query .= ') DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
        return $this->query($query);
    }

    public function index($table, $index) {
        if(!is_array($index)) $index = [$index];
        $name = $this->prefix($table).'_'.implode('_', $index).'_index';
        $keys = '`'.implode('`,`', $index).'`';
        return $this->query('ALTER TABLE `'.$this->prefix($table).'` ADD INDEX `'.$name.'` ('.$keys.')');
    }

    public function dropIndex($table, $index) {
        if(!is_array($index)) $index = [$index];
        $name = $this->prefix($table).'_'.implode('_', $index).'_index';
        return $this->query('ALTER TABLE '.$this->prefix($table).' DROP INDEX `'.$name.'`');
    }

    public function addFields($table, $fields) {
        if(!is_array($fields)) $fields = [$fields];
        $query = 'ALTER TABLE '.$this->prefix($table).' ';
        foreach($fields as $k => $f) {
            $query .= 'ADD COLUMN '.$this->createFields([$k => $f]).', ';
        }
        $query = trim($query, ', ');
        return $this->query($query);
    }

    public function dropFields($table, $fields) {
        if(!is_array($fields)) $fields = [$fields];
        $query = 'ALTER TABLE '.$this->prefix($table).' ';
        foreach($fields as $f) {
            $query .= 'DROP COLUMN `'.$this->escape($f).'`, ';
        }
        $query = trim($query, ', ');
        return $this->query($query);
    }
}