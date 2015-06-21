<?php

class Status extends ActiveTable {
    protected $_table = 'statuses';
    protected $_db = null;

    public function getIdByAlias($alias) {
        return $this->_get($alias, 'id', 'alias');
    }

    public function getTitleById($id) {
        return $this->_get($id, 'title', 'id');
    }
}