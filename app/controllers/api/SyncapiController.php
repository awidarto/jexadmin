<?php
namespace Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class SyncapiController extends \Controller {
    public $controller_name = '';

    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postScanlog()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }


        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){
                if(isset($j['timestamp'])){
                    $j['mtimestamp'] = new \MongoDate(strtotime($j['timestamp']));
                }

                $log = \Scanlog::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    \Scanlog::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postBoxstatus()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';

        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){
                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new \MongoDate(strtotime($j['datetimestamp']));
                }

                $log = \Boxstatus::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    /*
                    $bs = array();
                    foreach($j as $k=>$v){
                        $bs[$this->camel_to_underscore($k)] = $v;
                    }*/
                    $j['appname'] = $appname;
                    \Boxstatus::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postPickupstatus()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate();

            if(is_array($j)){


                $olog = new \Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = \Shipment::where('delivery_id','=',$olog->deliveryId)->first();

                if($shipment){

                    $ts = new \MongoDate();
                    $pre = clone $shipment;

                    //$shipment->status = $olog->status;
                    $shipment->pickup_status = $olog->pickupStatus;

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    $shipment->save();

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_pickup_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    \History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_pickup_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    \Shipmentlog::insert($sdata);


                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( \Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postHubstatus()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate();

            if(is_array($j)){


                $olog = new \Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = \Shipment::where('delivery_id','=',$olog->deliveryId)->first();

                if($shipment){

                    $ts = new \MongoDate();
                    $pre = clone $shipment;

                    //$shipment->status = $olog->status;
                    $shipment->warehouse_status = $olog->warehouseStatus;

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    $shipment->save();

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_hub_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    \History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_hub_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    \Shipmentlog::insert($sdata);


                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( \Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postOrderstatus()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate();

            if(is_array($j)){


                $olog = new \Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                $r = $olog->save();

                $shipment = \Shipment::where('delivery_id','=',$olog->deliveryId)->first();

                if($shipment){

                    $ts = new \MongoDate();
                    $pre = clone $shipment;


                    if($appname == \Config::get('jex.pickup_app')){
                        $shipment->pickup_status = $olog->pickup_status;
                    }elseif($appname == \Config::get('jex.hub_app')){
                        $shipment->warehouse_status = $olog->warehouse_status;
                    }else{
                        $shipment->status = $olog->status;
                        $shipment->courier_status = $olog->courierStatus;

                        if($olog->status == 'pending'){
                            $shipment->pending_count = $shipment->pending_count + 1;
                        }elseif($olog->status == 'delivered'){
                            $shipment->deliverytime = date('Y-m-d H:i:s',time());
                        }

                    }


                    $shipment->save();

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_shipment_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    \History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_shipment_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    \Shipmentlog::insert($sdata);

                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( \Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postBox()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';

        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate(time());

            if(is_array($j)){
                $blog = new \Boxid();

                foreach ($j as $k=>$v) {
                    $blog->{$k} = $v;
                }

                $blog->appname = $appname;

                //$blog->mtimestamp = new \MongoDate(time());
                $blog->mtimestamp = date('Y-m-d H:i:s',time());

                $box = \Box::where('delivery_id','=',$blog->deliveryId)->first();


                if($box){

                    if($appname == \Config::get('jex.pickup_app')){
                        $box->pickupStatus = $blog->pickup_status;
                    }elseif($appname == \Config::get('jex.hub_app')){
                        $box->warehouseStatus = $blog->warehouse_status;
                    }else{
                        $box->status = $blog->status;
                        $box->courierStatus = $blog->courierStatus;
                    }

                    $box->save();
                }



                $r = $blog->save();

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postHuborder()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate(time());

            if(is_array($j)){
                $olog = new \Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = \Shipment::where('delivery_id','=',$olog->deliveryId)->first();

                if($shipment){
                    //$shipment->status = $olog->status;
                    $shipment->warehouse_status = $olog->warehouseStatus;

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    /*
                    $shipment->pending_count = new \MongoInt32($olog->pendingCount) ;

                    if($olog->courierStatus == \Config::get('jayon.trans_cr_oncr') || $olog->courierStatus == \Config::get('jayon.trans_cr_oncr_partial'))
                    {
                        $shipment->pickup_status = \Config::get('jayon.trans_status_pickup');
                    }
                    */
                    $shipment->save();
                }


                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postPickuporder()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate(time());

            if(is_array($j)){
                $olog = new \Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = \Shipment::where('delivery_id','=',$olog->deliveryId)->first();

                if($shipment){
                    //$shipment->status = $olog->status;
                    $shipment->pickup_status = $olog->pickupStatus;

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    /*
                    $shipment->pending_count = new \MongoInt32($olog->pendingCount) ;

                    if($olog->courierStatus == \Config::get('jayon.trans_cr_oncr') || $olog->courierStatus == \Config::get('jayon.trans_cr_oncr_partial'))
                    {
                        $shipment->pickup_status = \Config::get('jayon.trans_status_pickup');
                    }
                    */
                    $shipment->save();
                }


                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postOrder()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new \MongoDate(time());

            if(is_array($j)){
                $olog = new \Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new \MongoDate(time());

                $r = $olog->save();

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postGeolog()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){
                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new \MongoDate(strtotime($j['datetimestamp']));
                }

                $log = \Geolog::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    \Geolog::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postNote()
    {

        $key = \Input::get('key');

        $appname = (\Input::has('app'))?\Input::get('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = \Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return \Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }


        $json = \Input::all();

        $batch = \Input::get('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){
                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new \MongoDate(strtotime($j['datetimestamp']));
                }

                $log = \Deliverynote::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    \Deliverynote::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync note'));

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putAssets()
    {

        $json = \Input::all();

        $key = \Input::get('key');

        $json['mode'] = 'edit';

        $batch = \Input::get('batch');

        \Dumper::insert($json);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putRacks()
    {

        $json = \Input::all();

        $key = \Input::get('key');

        $json['mode'] = 'edit';

        $batch = \Input::get('batch');

        \Dumper::insert($json);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putLocations()
    {

        $json = \Input::all();

        $key = \Input::get('key');

        $json['mode'] = 'edit';

        $batch = \Input::get('batch');

        \Dumper::insert($json);

    }

    public function camel_to_underscore($str)
    {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

}