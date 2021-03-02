<?php


namespace App\Controllers;

use App\Models\Admin;
use Psr\Log\NullLogger;

class AdminController extends BaseController
{


    public function __construct()
    {
        $session = \Config\Services::session();
    }
    public function home()
    {
        session_start();

        if (!isset($_SESSION['id'])){
            //not logged in
            echo view('admin/login');
//            $this->login();

        } else {
            $id = $_SESSION['id'];
            $type = $_SESSION['type'];
            echo view('/admin/dashboard');
        }

    }

    public function login(){

        $model = new Admin();
        //$data['routeDetails'] = $model->getRouteDetalails();
        $data['customerDetails'] = $model->getCustomerDetails();

        $session = \Config\Services::session();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            //$model ->login($_POST);
            $info = $model->getLoginInfo($_POST);
            if (count($info)>0){
                if (password_verify($_POST['password'],$info[0]['password'])){

                    //set session and redirect
                    session_start();
                    $newdata = [
                        'staff_id'  => $info[0]['staff_id'],
                        'name'  => $info[0]['name'],
                    ];
                    $session->set($newdata);



                    $this->viewHome();
                }
                else {
                    echo "wrong password";
                }
            }else{
                echo view('admin/login',$data);
            }

        }

        else{
            echo view('admin/login',$data);
        }

    }

    public function register(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'NIC'  => trim($_POST['NIC']),
                'position'  => 'admin',
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'tel_no' => trim($_POST['tel_no']),
                'address' => trim($_POST['address']),
                'city_id' => 1,

            ];
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);


            // Register user
            if($model->register($data)) {
                $this->login();
            } else {
                die('Something went wrong');
            }
        }else {
            echo view('admin/register');
        }
    }

    public function logout(){
        session_start();
        unset($_SESSION['id']);
        unset($_SESSION['type']);

        session_destroy();

        $this->login();

    }
    public function registerDriver(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'NIC'  => trim($_POST['NIC']),
                'position'  => trim($_POST['position']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'tel_no' => trim($_POST['tel_no']),
                'address' => trim($_POST['address']),
                'city_id' => trim($_POST['city_id']),
            ];
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Register user
            if($model->register($data)) {
                $this->addDriver();
            } else {
                die('Something went wrong');
            }
        }else {
            $this->addDriver();
        }
    }
    public function registerTruck(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'truck_no' => trim($_POST['truck_no']),
                'city_id' => trim($_POST['city_id']),
            ];

            // Register user
            if($model->registerTruck($data)) {
                $this->addTruck();
            } else {
                die('Something went wrong');
            }
        }else {
            $this->addTruck();
        }
    }
    public function registerRoute(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'city_id' => trim($_POST['city_id']),
                'description' => trim($_POST['description']),
                'max_time_mins' => trim($_POST['max_time_mins']),
            ];

            // Register user
            if($model->registerRoute($data)) {
                $this->addRoute();
            } else {
                die('Something went wrong');
            }
        }else {
            $this->addRoute();
        }
    }
    public function registerTrainSchedule(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'train_id' => trim($_POST['truck_no']),
                'quantity' => trim($_POST['quantity']),
                'date' => trim($_POST['date']),
                'order_id' => trim($_POST['order_id']),
            ];

            // Register user
            if($model->registerTrainSchedule($data)) {
                $this->addTrainSchedule();
            } else {
                die('Something went wrong');
            }
        }else {
            $this->addTrainSchedule();
        }
    }
    public function registerProduct(){
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'train_capacity' => trim($_POST['train_capacity']),
                'shelf_life' => trim($_POST['shelf_life']),
                'stock' => trim($_POST['stock']),
            ];

            // Register user
            if($model->registerProduct($data)) {
                $this->addProduct();
            } else {
                die('Something went wrong');
            }
        }else {
            $this->addProduct();
        }
    }

    public function viewHome() {
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');
        echo view('admin/dashboard', $data);
    }

    public function addDriver() {
        $model = new Admin();
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');
        $data['cityDetails'] = $model->getCityDetails();


        echo view('admin/addDriver', $data);
    }
    public function addTruck() {
        $model = new Admin();
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');
        $data['cityDetails'] = $model->getCityDetails();

        echo view('admin/addTruck', $data);
    }
    public function addTrainSchedule() {
        $model = new Admin();
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');

        echo view('admin/addTrainSchedule', $data);
    }
    public function addRoute() {
        $model = new Admin();
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');
        $data['cityDetails'] = $model->getCityDetails();

        echo view('admin/addRoute', $data);
    }
    public function addProduct() {
        $model = new Admin();
        $session = \Config\Services::session();
        $data['name'] = $session->get('name');

        echo view('admin/addProduct', $data);
    }




}