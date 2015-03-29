<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct() {
        parent::__construct();
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index() {
        // Build a list of orders
        // load the helper library of "directory"
        $this->load->helper('directory');

        // store the directory path as an array
        $dir_map = directory_map(DATAPATH);
        
        $orders = array();
        
        // seprate the xml file("order") and store to orders array
        foreach ($dir_map as $filename) {
            $file_info = pathinfo($filename);
            if ($file_info['extension'] === strtolower('xml') && preg_match("/^[a-zA-Z]+\d+$/", $file_info['filename'])) {
                $orders[] = array('order' => $filename);
            }
        }
        
        // load the model of "Order"
        $this->load->model('Order');
        
        foreach ($orders as &$arr) {
            $order = $this->Order->makeOrder($arr['order']);
            $customer = $order['customer'];
            $arr['customer'] = $customer;
        }

        // Present the list to choose from
        $this->data['pagebody'] = 'homepage';
        $this->data['orders'] = $orders;
        $this->render();
    }

    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($filename) {
        // Build a receipt for the chosen order
        // load the model of "Order"
        $this->load->model('Order');
        $order = $this->Order->makeOrder($filename);

        // Present the list to choose from
        $this->data['pagebody'] = 'justone';
        $this->data['customer'] = $order['customer'];
        $this->data['order'] = $filename;
        $this->data['burgers'] = $order['burgers'];
        $this->data['eatin'] = $order['eatin'];
        $this->data['total'] = $order['total'];

        $this->render();
    }

}
