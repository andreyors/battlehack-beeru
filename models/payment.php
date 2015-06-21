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

    public function getTransactionIdByToken($token) {
        return $this->_get($token, 'transaction_id', 'token');
    }

    public function getStatusByToken($token) {
        $status_id = $this->_get($token, 'status_id', 'token');
        $status = new Status($this->_db);

        return $status->getTitleById($status_id);
    }

    public function add($customer_id, $items, $nonce = 'fake-valid-nonce') {
        $this->_db->beginTransaction();

        $token = $this->getToken();
        $amount = $this->getAmount($items);

        $payment = Braintree_PaymentMethod::create([
            'customerId' => $customer_id,
        ]);

        $status = new Status($this->_db);
        $status_id = $status->getIdByAlias('paid');

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
                (%s)', $this->getTable(), implode(', ', $this->_build('key', $data)), implode(', ', $this->_build('value', $data)));

        try {
            $this->_db->exec($sql);
            $payment_id = $this->_db->lastInsertId();
        } catch (PDOException $e) {
            $this->_db->rollBack();
        }

        $item = new Item($this->_db);
        $item->addItems($payment_id, $items);

        $result = Braintree_Transaction::sale(array(
            'amount' => $amount,
            'customerId' => $customer_id,
            'paymentMethodNonce' => $nonce,
            'options' => array(
                'submitForSettlement' => true
            )
        ));

        if ($result->success) {
            $transaction_id = $result->transaction->id;
            if ($transaction_id) {
                $this->update($payment_id, array('transaction_id' => $transaction_id));
            }
        }

        $this->_db->commit();

        return $payment_id;
    }
}