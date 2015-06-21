<?php

class Customer extends ActiveTable {
    protected $_table = "customers";
    protected $_db = null;

    public function getCustomerIdByValues($data) {
        return $this->_read($data, 'customer_id');
    }

    public function getCustomerIdById($id) {
        return $this->_get($id, 'customer_id');
    }

    public function getTokenByCustomer($customer_id) {
        $token =  Braintree_ClientToken::generate([
            "customerId" => $customer_id,
        ]);

        return $token;
    }

    public function create($data) {
        $customer_id = false;

        $first_name = !empty($data['first_name']) ? $data['first_name'] : '';
        $last_name = !empty($data['last_name']) ? $data['last_name'] : '';
        $email = !empty($data['email']) ? $data['email'] : '';
        $phone = !empty($data['phone']) ? $data['phone'] : '';

        if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone)) {
            $sql = sprintf("INSERT INTO
                %s
                (%s)
                VALUES
                (%s)", $this->getTable(), $this->_build('key', $data), $this->_build('value', $data));

            $this->_db->beginTransaction();
            try {
                $res = $this->_db->exec($sql);
                $id = $this->_db->lastInsertId();
            } catch(PDOException $e) {
                $this->_db->rollBack();
            }
            $this->_db->commit();
        }

        return $id;
    }
}