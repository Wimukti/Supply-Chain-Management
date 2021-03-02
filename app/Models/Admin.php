<?php

namespace App\Models;

use CodeIgniter\Model;

class Admin extends Model
{
    protected $DBGroup = 'root';
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('root');
    }

    public function getLoginInfo($array){

        $sql = "SELECT * FROM staff WHERE NIC = :NIC:";
        $NIC = $array['NIC'];
        $results = $this->db->query($sql,['NIC'=> $NIC])->getResult('array');
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


    public function getCityDetails(){

        $sql = "SELECT * FROM city";
        $resultSet = $this->db->query($sql)->getResult('array');

        return $resultSet;
    }

    public function register($data) {

        $data = [
            'name' => $data['name'],
            'NIC'  => $data['NIC'],
            'position'  => $data['position'],
            'email'  => $data['email'],
            'password'  => $data['password'],
            'tel_no'  => $data['tel_no'],
            'address'  => $data['address'],
            'city_id'  => $data['city_id'],
        ];

        $result = $this->db->table('staff')->insert($data);
        return true;
    }
    public function registerTruck($data) {

        $data = [
            'truck_no' => $data['truck_no'],
            'city_id'  => $data['city_id'],
        ];

        $result = $this->db->table('truck')->insert($data);
        return true;
    }
    public function registerRoute($data) {
        $data = [
            'city_id' => $data['city_id'],
            'description'  => $data['description'],
            'max_time_mins'  => $data['max_time_mins'],
        ];

        $result = $this->db->table('route')->insert($data);
        return true;
    }
    public function registerProduct($data) {
        $data = [
            'name' => $data['name'],
            'price'  => $data['price'],
            'train_capacity'  => $data['train_capacity'],
            'shelf_life'  => $data['shelf_life'],
            'stock'  => $data['stock'],
        ];

        $result = $this->db->table('item')->insert($data);
        return true;
    }
    public function registerTrainSchedule($data) {
        $data = [
            'train_id' => $data['train_id'],
            'quantity'  => $data['quantity'],
            'date'  => $data['date'],
            'order_id'  => $data['order_id'],
        ];

        $result = $this->db->table('train_schedule')->insert($data);
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