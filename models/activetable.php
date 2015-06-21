<?php

class ActiveTable {
    public function exists($data) {
        return $this->_read($data, 'COUNT(1) cnt');
    }

    public function update($id, $data) {
        $sql = sprintf("UPDATE %s
                        SET
                        %s
                        WHERE
                        (id = %d)", $this->getTable(), $this->_buildCondition($data, ', '), $id);
        $this->_db->exec($sql);
    }

    public function getIdByValues($data) {
        return $this->_read($data, 'id');
    }

    protected function _get($value, $searched_for = 'id', $searched_by = 'id') {
        $sql = sprintf('SELECT
                    `%s`
                FROM
                    %s
                WHERE
                    ( `%s` = ' . ('id' === $searched_by ? '%d' : '"%s"'). " )", $searched_for, $this->getTable(), $searched_by, $value);
        $res = $this->_db->query($sql);

        return ($res ? $res->fetchColumn() : false);
    }

    protected function _read($data, $field) {
        $sql = "SELECT
                    `" . $field . "`
                FROM
                    " . $this->getTable() . "
                WHERE
                    (" . $this->_buildCondition($data, ' AND ') . ")";
        $res = $this->_db->query($sql);

        return ($res ? $res->fetchColumn() : false);
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

        return implode(', ', $result);
    }

    public function __construct($db) {
        $this->_db = $db;
    }

    public function getTable() {
        return $this->_table;
    }
}