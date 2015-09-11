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
    public function postGeolog()
    {

        $key = \Input::get('key');

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

        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

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

}