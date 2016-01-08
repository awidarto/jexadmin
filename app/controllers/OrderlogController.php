<?php

class OrderlogController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->controller_name = str_replace('Controller', '', get_class());

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Orderlog();
        //$this->model = DB::collection('documents');

    }

    public function getTest()
    {
        $raw = $this->model->where('docFormat','like','picture')->get();

        print $raw->toJSON();
    }


    public function getIndex()
    {
        $this->heads = array(
            array('Timestamp',array('search'=>true,'sort'=>true,'datetimerange'=>true)),
            array('Merchant Id',array('search'=>true,'sort'=>true)),
            array('Delivery Id',array('search'=>true,'sort'=>true)),
            array('No Kode Toko',array('search'=>true,'sort'=>false)),
            array('Fulfillment Code',array('search'=>true,'sort'=>false)),
            array('Status',array('search'=>true,'sort'=>false)),
            array('Pickup Status',array('search'=>true,'sort'=>true)),
            array('Pickup Time',array('search'=>true,'sort'=>true)),
            array('Pick Up Actor',array('search'=>true,'sort'=>true)),
            array('Warehouse Status',array('search'=>true,'sort'=>true)),
            array('Warehouse In',array('search'=>true,'sort'=>true ,'datetimerange'=>true)),
            array('Warehouse Out',array('search'=>true,'sort'=>true ,'datetimerange'=>true)),
            array('Warehouse Actor',array('search'=>true,'sort'=>true)),
            array('App Name',array('search'=>true,'sort'=>true)),

        );

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Order Status Log';

        $this->show_select = false;

        $this->place_action = 'none';

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('created_at',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('merchantId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('merchantTransId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('fulfillmentCode',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupStatus',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickuptime',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDevId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseStatus',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseIn',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseOut',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseDevId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('appname',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true))
        );

        $this->def_order_by = 'created_at';
        $this->def_order_dir = 'desc';
        $this->show_select = false;

        $this->place_action = 'none';

        return parent::postIndex();
    }

    public function postAdd($data = null)
    {

        $this->validator = array(
            'firstname' => 'required',
            'lastname' => 'required',
            'email'=> 'required|unique:agents',
            'pass'=>'required|same:repass'
        );

        return parent::postAdd($data);
    }

    public function beforeSave($data)
    {
        unset($data['repass']);
        $data['pass'] = Hash::make($data['pass']);
        return $data;
    }

    public function beforeUpdate($id,$data)
    {
        //print_r($data);

        if(isset($data['pass']) && $data['pass'] != ''){
            unset($data['repass']);
            $data['pass'] = Hash::make($data['pass']);

        }else{
            unset($data['pass']);
            unset($data['repass']);
        }

        //print_r($data);

        //exit();

        return $data;
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'firstname' => 'required',
            'lastname' => 'required',
            'email'=> 'required'
        );

        if($data['pass'] == ''){
            unset($data['pass']);
            unset($data['repass']);
        }else{
            $this->validator['pass'] = 'required|same:repass';
        }

        return parent::postEdit($id,$data);
    }

    public function makeActions($data)
    {
        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="icon-trash"></i>Delete</span>';
        $edit = '<a href="'.URL::to('agent/edit/'.$data['_id']).'"><i class="icon-edit"></i>Update</a>';

        $actions = $edit.'<br />'.$delete;
        return $actions;
    }

    public function splitTag($data){
        $tags = explode(',',$data['docTag']);
        if(is_array($tags) && count($tags) > 0 && $data['docTag'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['docTag'];
        }
    }

    public function splitShare($data){
        $tags = explode(',',$data['docShare']);
        if(is_array($tags) && count($tags) > 0 && $data['docShare'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['docShare'];
        }
    }

    public function namePic($data)
    {
        $name = HTML::link('products/view/'.$data['_id'],$data['productName']);
        if(isset($data['thumbnail_url']) && count($data['thumbnail_url'])){
            $display = HTML::image($data['thumbnail_url'][0].'?'.time(), $data['filename'][0], array('id' => $data['_id']));
            return $display.'<br />'.$name;
        }else{
            return $name;
        }
    }

    public function pics($data)
    {
        $name = HTML::link('products/view/'.$data['_id'],$data['productName']);
        if(isset($data['thumbnail_url']) && count($data['thumbnail_url'])){
            $display = HTML::image($data['thumbnail_url'][0].'?'.time(), $data['filename'][0], array('style'=>'min-width:100px;','id' => $data['_id']));
            return $display.'<br /><span class="img-more" id="'.$data['_id'].'">more images</span>';
        }else{
            return $name;
        }
    }

    public function getViewpics($id)
    {

    }


}