<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 27/11/14
 * Time: 11:38
 */

class Models extends DB implements Iterator {
    protected $_items = [];
    protected $table = null;
    protected $template = null;

    public function __construct($someData = null) {
        parent::__construct();
        $this->from($this->table);
        if(!is_null($someData)) {
            $this->_items = $someData;
        }
    }

    public function table($table) {
        $this->table = $table;
    }

    public function getTemplate()
    {
        $this->from($this->table);
        if($this->template !== null) return $this->template;
        $fields = $this->fields($this->table);
        if($fields) {
            $this->template = new stdClass();
            foreach ($fields as $f) {
                $val = $f->Default;
                if(stristr($f->Type, 'int')) {
                    $val = (int)$val;
                }
                if($f->Extra == 'auto_increment') {
                    $val = (int)$this->max() + 1;
                }
                $field = $f->Field;
                if(!is_null($field)) {
                    $this->template->{$field} = $val;
                }
            }
        }
        return $this->template;
    }

    /**
     * @param null $id
     * @return $this
     */
    public function find($id = null) {
        if($id) {
            $this->where('id', $id)->get()->row();
        }
        return $this;
    }

    public function create($data = null) {
	    $put = $this->getTemplate();
        if(is_array($data)) {
            foreach($put as $k => $v) {
                $put->$k = arrayGet($data, $k, $v);
            }
        } else {
            foreach ($put as $k => $v) {
                $put->$k = objectGet($data, $k, $v);
            }
        }
        $this->insert($put);
	    $this->find($this->insertId());
        return $this;
    }

    public function initWithTemplate()
    {
        $this->_items = $this->getTemplate();
        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        if(is_object($this->_items)) {
            $this->where('id', $this->id)->update($this->_items);
        }
        return $this;
    }

    public function result()
    {
        $items = parent::result();
        $class = get_class($this);
        foreach($items as $k => $i) {
            $items[$k] = new $class($i);
        }
        $this->_items = $items;
        return $this;
    }

    public function items()
    {
        return $this->_items;
    }

    public function item($key, $alter = null)
    {
        return objectGet($this->items(), $key, $alter);
    }

    public function row($index = 0)
    {
        $this->_items = parent::row();
        return $this;
    }

    public function first() {
        $this->_items = parent::row(0);
        return $this;
    }


    public function rewind()
    {
        reset($this->_items);
    }

    public function current()
    {
        return current($this->_items);
    }

    public function key()
    {
        return key($this->_items);
    }

    public function next()
    {
        return next($this->_items);
    }

    public function valid()
    {
        $key = key($this->_items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

    public function isEmpty() {
        return empty($this->_items);
    }

    public function __get($name) {
        if(!empty($this->_items) and is_object($this->_items) and isset($this->_items->$name)) {
            return $this->_items->$name;
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        if(!empty($this->_items) and is_object($this->_items) and isset($this->_items->$name)) {
            $this->_items->$name = $value;
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Not in object context __set(): ' . $name . ' : ' . $value .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'] .
                '. Object has multikeys or is empty',
                E_USER_NOTICE);
        }
    }

	public function append($collection) {
		$this->_items[] = $collection;
	}

}