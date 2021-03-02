<?php

namespace App\Models;

use CodeIgniter\Model;

class Customer extends Model
{
    protected $DBGroup = 'root';
    protected $primaryKey = 'staff_id';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('root');
    }



    public function getLoginInfo($array){

        $sql = "SELECT * FROM customer WHERE email = :email:";
        $email = $array['email'];
        $results = $this->db->query($sql,['email'=> $email])->getResult('array');
        return $results;
    }

    public function getUserOrderDetalails($user){

        $sql = "SELECT * FROM orders WHERE customer_id =:customer_id:";
        $resultSet = $this->db->query($sql,['customer_id' => $user])->getResult('array');

        return $resultSet;
    }

    public function getProductDetalails(){

        $sql = "SELECT * FROM item WHERE stock>0 ";
        $resultSet = $this->db->query($sql)->getResult('array');

        return $resultSet;
    }

    public function getCustomerDetails(){

        $sql = "SELECT * FROM customer_type";
        $resultSet = $this->db->query($sql)->getResult('array');

        return $resultSet;
    }

    public function getCustomerConstraintDetails($customertype){

        $sql = "SELECT * FROM customer_type WHERE type = :type:";
        $resultSet = $this->db->query($sql,['type' => $customertype])->getResult('array');

        return $resultSet;
    }


    public function getRouteDetalails(){

        $sql = "SELECT * FROM route";
        $resultSet = $this->db->query($sql)->getResult('array');

        return $resultSet;
    }


    public function register($data) {

        $data = [
            'name' => $data['name'],
            'email'  => $data['email'],
            'password'  => $data['password'],
            'billing_address'  => $data['address'],
            'type'  => $data['type'],
            'tel_no' => $data['tel_no']
        ];

        $result = $this->db->table('customer')->insert($data);

        return true;
    }

    public function Buy($data) {

        $data_first = [
            'customer_id' => $data['customer_id'],
            'order_date' => $data['order_date'],
            'ship_date' => $data['ship_date'],
            'route_id' => $data['route_id'],
            'status' => $data['status'],
            'shipping_address' => $data['shipping_address'],
            'total_bill' => $data['total_price']

        ];



        $this->db->transStart();
        $this->db->table('orders')->insert($data_first);
        $query = $this->db->query('SELECT LAST_INSERT_ID()')->getResult('array');


        $LastIdInserted = $query[0]['LAST_INSERT_ID()'];

        $data_new=[
            'order_id'=>$LastIdInserted ,
            'item_id'=>$data['item_id'],
            'quantity'=>$data['qty']
        ];

        $new = $this->db->table('order_details')->insert($data_new);
        $update_data = array(
            'stock'=>$data['remaining_stock']
        );



        $stock = $data['remainig_stock'];
        $item_id =$data['item_id'];
        $sql = "UPDATE `item` SET `stock` = {$stock} WHERE `item_id` = {$item_id}";

        $results = $this->db->query($sql)->getResult();

        $this->db->transComplete();
        //print_r($results);
        //$this->db->update('item',$update_data,array('item_id'=>$data['item_id']));




        return true;
    }

    public function cancelOrder($data) {
        $order_id = $data["order_id"];
        $status = $data["status"];


        //Cancel Order process work as transaction
        $this->db->transStart();;

        $sql = "UPDATE `orders` SET `status` = :status: WHERE `order_id` = {$order_id}";
        $this->db->query($sql,['status' => $status]);

        $sql = "SELECT item_id,quantity FROM order_details WHERE order_id = :order_id:";
        $results = $this->db->query($sql,['order_id'=> $order_id])->getResult('array');

        $item_id = $results[0]['item_id'];

        $sql = "SELECT stock FROM item WHERE item_id = :item_id:";
        $results_new = $this->db->query($sql,['item_id'=> $item_id])->getResult('array');

        $new_stock = $results_new[0]['stock'] + $results[0]['quantity'];
        $sql = "UPDATE `item` SET `stock` = :stock: WHERE `item_id` = {$item_id}";
        $this->db->query($sql,['stock' => $new_stock]);

        $this->db->transComplete();

        //print_r($new_stock);
        //$this->db->update('item',$update_data,array('item_id'=>$data['item_id']));




        return true;
    }

}