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
            $result = Braintree_Customer::create([
                'firstName' => $data['first_name'],
                'lastName' =>  $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            if ($result->success) {
                $customer_id  = $result->customer->id;
                $data['customer_id'] = $customer_id;
            }
        }

        $sql = sprintf("INSERT INTO
            %s
            (%s)
            VALUES
            (%s)", $this->getTable(), implode(', ', $this->_build('key', $data)), implode(', ', $this->_build('value', $data)));

        $this->_db->beginTransaction();

        try {
            $res = $this->_db->exec($sql);

            $id = $this->_db->lastInsertId();
            $customer_id = $this->getCustomerIdById($id);
        } catch(PDOException $e) {
            $this->_db->rollBack();
        }

        $this->_db->commit();

        return $customer_id;
    }

    public function update($id, $data) {
        $sql = sprintf("UPDATE %s
            SET
                %s
            WHERE
                id = %d", $this->getTable(), $this->_buildCondition($data, ', '), $id);
    }
}