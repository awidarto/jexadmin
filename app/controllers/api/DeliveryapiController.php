<?php
namespace Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class DeliveryapiController extends \BaseController {

    public $controller_name = '';

    public $model;

    public $sql_connection;

    public $sql_table_name;

    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

        $this->sql_table_name =  \Config::get('jayon.incoming_delivery_table') ;
        $this->sql_connection = 'mysql';

        $this->model = \DB::connection($this->sql_connection)->table($this->sql_table_name);

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $key = Input::get('key');
        $deliverydate = Input::get('date');

        /*
                    ->join('members as m','d.merchant_id=m.id','left')
                    ->where('assignment_date',$indate)
                    ->where('device_id',$dev->id)
                    ->and_()
                    ->group_start()
                        ->where('status',$this->config->item('trans_status_admin_courierassigned'))
                        ->or_()
                        ->group_start()
                            ->where('status',$this->config->item('trans_status_new'))
                            ->where('pending_count >', 0)
                        ->group_end()
                    ->group_end()


        */

        $dev = \Device::where('key','=',$key)->first();

        //print_r($dev);

        $txtab = \Config::get('jayon.incoming_delivery_table');

        /*
        $orders = $this->model
                    ->select(
                            \DB::raw(
                                \Config::get('jayon.incoming_delivery_table').'.* ,'.
                                \Config::get('jayon.jayon_members_table').'.merchantname as merchant_name ,'.
                                \Config::get('jayon.applications_table').'.application_name as app_name ,'.
                                '('.$txtab.'.width * '.$txtab.'.height * '.$txtab.'.length ) as volume'
                            )
                    )
                    ->leftJoin(\Config::get('jayon.jayon_members_table'), \Config::get('jayon.incoming_delivery_table').'.merchant_id', '=', \Config::get('jayon.jayon_members_table').'.id' )
                    ->leftJoin(\Config::get('jayon.applications_table'), \Config::get('jayon.incoming_delivery_table').'.application_id', '=', \Config::get('jayon.applications_table').'.id' )

                    ->where('device_id','=',$dev->id)
                    ->where('assignment_date','=',$deliverydate)

                    ->where(function($q){
                        $q->where('status','=', \Config::get('jayon.trans_status_new') )
                            ->orWhere(function($ql){
                                $ql->where('status','=', \Config::get('jayon.trans_status_new') )
                                    ->where('pending_count','>',0);
                            });
                    })
                    ->orderBy('ordertime','desc')
                    ->get();
        */

        $orders = $this->model
                ->select(
                    \DB::raw(
                        \Config::get('jayon.incoming_delivery_table').'.* ,'.
                        \Config::get('jayon.jayon_couriers_table').'.fullname as courier ,'.
                        \Config::get('jayon.jayon_devices_table').'.identifier as device ,'.
                        \Config::get('jayon.jayon_members_table').'.merchantname as merchant_name ,'.
                        \Config::get('jayon.applications_table').'.application_name as app_name ,'.
                        '('.$txtab.'.width * '.$txtab.'.height * '.$txtab.'.length ) as volume'
                )
            )
            ->leftJoin(\Config::get('jayon.jayon_couriers_table'), \Config::get('jayon.incoming_delivery_table').'.courier_id', '=', \Config::get('jayon.jayon_couriers_table').'.id' )
            ->leftJoin(\Config::get('jayon.jayon_devices_table'), \Config::get('jayon.incoming_delivery_table').'.device_id', '=', \Config::get('jayon.jayon_devices_table').'.id' )
            ->leftJoin(\Config::get('jayon.jayon_members_table'), \Config::get('jayon.incoming_delivery_table').'.merchant_id', '=', \Config::get('jayon.jayon_members_table').'.id' )
            ->leftJoin(\Config::get('jayon.applications_table'), \Config::get('jayon.incoming_delivery_table').'.application_id', '=', \Config::get('jayon.applications_table').'.id' )

            ->where(function($q) use($dev, $deliverydate){
                    $q->where('device_id','=',$dev->id)
                    ->where('assignment_date','=',$deliverydate);

            })
            ->where(function($query){
                $query->where('status','=', \Config::get('jayon.trans_status_admin_courierassigned') )
                    ->orWhere('status','=', \Config::get('jayon.trans_status_mobile_pickedup') )
                    ->orWhere('status','=', \Config::get('jayon.trans_status_mobile_enroute') )
                    ->orWhere(function($q){
                            $q->where('status', \Config::get('jayon.trans_status_new'))
                                ->where(\Config::get('jayon.incoming_delivery_table').'.pending_count', '>', 0);
                    });

            })

            ->orderBy('ordertime','desc')
            ->get();


        for($n = 0; $n < count($orders);$n++){
            $or = new \stdClass();
            foreach( $orders[$n] as $k=>$v ){
                $nk = $this->underscoreToCamelCase($k);
                $or->$nk = (is_null($v))?'':$v;
            }

            $or->extId = $or->id;
            unset($or->id);

            $or->boxList = $this->boxList('delivery_id',$or->deliveryId,$key,$or->merchantId);
            //$or->boxList = $this->boxList('delivery_id',$or->deliveryId);
            $or->boxObjects = $this->boxList('delivery_id',$or->deliveryId, $key, $or->merchantId , true);
            $or->merchantObject = $this->merchantObject($or->merchantId);

            $orders[$n] = $or;
        }


        $actor = $key;
        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'logged out'));

        return $orders;
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $in = Input::get();
        if(isset($in['key']) && $in['key'] != ''){
            print $in['key'];
        }else{
            print 'no key';
        }
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function underscoreToCamelCase( $string, $first_char_caps = false)
    {

        $strings = explode('_', $string);

        if(count($strings) > 1){
            for($i = 0; $i < count($strings);$i++){
                if($i == 0){
                    if($first_char_caps == true){
                        $strings[$i] = ucwords($strings[$i]);
                    }
                }else{
                    $strings[$i] = ucwords($strings[$i]);
                }
            }

            return implode('', $strings);
        }else{
            return $string;
        }

    }

    public function merchantObject($merchant_id)
    {
        $merchant = \Merchant::where('id','=',$merchant_id)->first();
        if($merchant){

            $merchant = $merchant->toArray();

            $nm = array();
            foreach ($merchant as $key => $value) {
                if(in_array($key, $this->merchant_unset)){

                }else{
                    $nk = $this->underscoreToCamelCase($key);
                    $nm[$nk] = (is_null($value))?'':$value;
                }
            }

            $nm['extId'] = $nm['id'];
            unset($nm['id']);

            return $nm;
        }else{
            return array();
        }
    }

    public function boxList($field,$val, $device_key , $merchant_id ,$obj = false){

        $boxes = \Box::where($field,'=',$val)
                        //->where('deliveryStatus','!=','delivered')
                        //->where('deliveryStatus','!=','returned')
                        ->get();

        $bx = array();

        if($obj == true){

            $boxes = $boxes->toArray();

            for($n = 0; $n < count($boxes);$n++){


                $ob = new \stdClass();

                foreach( $boxes[$n] as $k=>$v ){
                    if($k != '_id' && $k != 'id'){
                        $nk = $this->underscoreToCamelCase($k);
                    }else{
                        $nk = $k;
                    }

                    $ob->$nk = (is_null($v))?'':$v;
                }

                //print_r($ob);
                $ob->extId = $ob->id;
                $ob->merchantId = $merchant_id;
                unset($ob->_id);

                $ob->status = $this->lastBoxStatus($device_key, $ob->deliveryId, $ob->fulfillmentCode ,$ob->boxId);

                $boxes[$n] = $ob;
            }

            return $boxes;

        }else{
            foreach($boxes as $b){
                $bx[] = $b->box_id;
            }

            if(count($bx) > 0){
                return implode(',',$bx);
            }else{
                return '1';
            }
        }

    }

    public function lastBoxStatus($device_key, $delivery_id, $fulfillment_code ,$box_id){
        $last = \Boxstatus::where('deliveryId','=',$delivery_id)
                                ->where('deviceKey','=',$device_key)
                                ->where('appname','=',\Config::get('jex.pickup_app'))
                                //->where('fulfillmentCode'.'=',$fulfillment_code)
                                ->where('boxId','=',strval($box_id))
                                ->orderBy('mtimestamp', 'desc')
                                ->first();
        //print_r($last);

        if($last){
            return $last->status;
        }else{
            return 'out';
        }
    }

    /*
    public function boxList($field,$val){

        $boxes = \Box::where($field,'=',$val)->get();

        $bx = array();

        foreach($boxes as $b){
            $bx[] = $b->box_id;
        }

        if(count($bx) > 0){
            return implode(',',$bx);
        }else{
            return '1';
        }

    }
    */
}
