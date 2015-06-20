<?php

class ActiveTable {
    public function exists($data) {
        return $this->_read($data, 'COUNT(1) cnt');
    }
    
    public function getIdByValues($data) {
        return $this->_read($data, 'id');
    }
    
    public function __construct($db) {
        $this->_db = $db;
    }
    
    public function getTable() {
        return $this->_db;
    }
    
    protected function _read($data, $field) {
        $sql = "SELECT 
                    " . $field . "
                FROM
                    " . $this->getTable() . "
                WHERE
                    (" . $this->_buildCondition($data, ' AND ') . ")";
        $res = $this->_db->query($sql);
        
        return ($res ? $res->fetchColumn() : 0);
    }

    protected function _buildCondition($data, $glue) {
        if (!empty($data)) {
            foreach($data as $k => $v) {
                $fields[] = '`' . $k . '` = "' . $v . '"';
            }
        }
        
        return implode($glue, $fields);
    }
    
    protected function _build($type, $data) {
        $result = array();
        switch($type) {
            case 'key':
                foreach($data as $k => $v) {
                    $result[] = sprintf('`%s`', $k);
                }
                break;
            
            case 'value':
                foreach($data as $k => $v) {
                    $result[] = sprintf('"%s"', $v);
                }
                break;
        }
        
        return $result;
    }
}