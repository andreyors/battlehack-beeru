<?php

class Item extends ActiveTable {
    protected $_table = 'items';
    protected $_db = null;

    public function add($payment_id, $data) {
        if (!empty($data)) {
            $header = array(
                'title' => 'title',
                'price' => 'price',
            );

            $template = sprintf('INSERT INTO
                            %s
                        (payment_id, %s)
                        VALUES
                        (%d, [values])', $this->getTable(), $this->_build('key', $header), $payment_id);

            foreach($data as $v) {
                $set = array(
                    'title' => $v['title'],
                    'price' => $v['price'],
                );

                $sql = strtr($template, array('[values]' => $this->_build('value', $set)));
                try {
                    $this->_db->exec($sql);
                } catch(PDOException $e) {
                    $this->_db->rollBack();
                }
            }
        }
    }
}