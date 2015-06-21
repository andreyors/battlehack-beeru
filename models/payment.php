<?php

class Payment extends ActiveTable {
    protected $_table = "payments";
    protected $_db = null;

    protected $_token = null;

    public function getAmount($items) {
        $result = 0;
        if (!empty($items)) {
            foreach($items as $v) {
                if (!empty($v['price'])) {
                    $result += $v['price'];
                }
            }
        }

        return $result;
    }

    public function getToken() {
        if (is_null($this->_token)) {
            $this->_token = sha1(uniqid('payment', true));
        }

        return $this->_token;
    }

    public function getTokenById($id) {
        return $this->_get($id, 'token', 'id');
    }

    public function getStatusByToken($token) {
        $status_id = $this->_get($token, 'status_id', 'token');
        $status = new Status($this->_db);

        return $status->getTitleById($status_id);
    }

    public function getTransactionIdByToken($token) {
        return $this->_get($token, 'transaction_id', 'token');
    }

    public function add($customer_id, $items, $nonce = 'fake-valid-nonce') {
        $token = $this->getToken();
        $amount = $this->getAmount($items);

        $status = new Status($this->_db);
        $status_id = $status->getIdByAlias('pending');

        $data = array(
            'token' => $token,
            'customer_id' => $customer_id,
            'status_id' => $status_id,
            'amount' => $amount,
        );

        $sql = sprintf('INSERT INTO
                    %s
                (%s)
                VALUES
                (%s)', $this->getTable(), $this->_build('key', $data), $this->_build('value', $data));

        $this->_db->beginTransaction();

        try {
            $this->_db->exec($sql);
            $payment_id = $this->_db->lastInsertId();
        } catch (PDOException $e) {
            $this->_db->rollBack();
        }

        $item = new Item($this->_db);
        $item->add($payment_id, $items);

        $this->_db->commit();

        return $payment_id;
    }
}