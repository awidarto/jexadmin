<?php

class Shipment extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'delivery_order_active';
    /*
    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
    */

}