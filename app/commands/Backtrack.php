<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Backtrack extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'backtrack:pickup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $shipped = Shipment::where('deliverytime','like','2016-01-11%')->get();

        print_r($shipped);

        foreach($shipped as $shipment){

            $is_there = Geolog::where('datetimestamp','=',$shipment->deliverytime)
                                ->where('deliveryId' ,'=',  $shipment->delivery_id)
                                ->where('status','=', $shipment->status)
                                ->where('sourceSensor','=','gps')
                                ->get();
            print_r($is_there);

            $stay = array_pop($is_there->toArray());

            foreach($is_there as $there){
                print 'there'."\r\n";
                print_r($there);
                //$there->remove();
            }

            print 'stay'."\r\n";
            print_r($stay);

            if($stay){
                $stay->latitude = doubleval($shipment->latitude);
                $stay->longitude = doubleval($shipment->longitude);

                //$stay->save();
            }
        }

        /*
        $dbox = Orderlog::where('pickupStatus','=',Config::get('jayon.trans_status_pickup'))
                            ->where('pickuptime','!=','0000-00-00 00:00:00')
                            ->orderBy('created_at','desc')
                            //->groupBy('created_at')
                            ->get();

        if($dbox){
            print count($dbox)."\r\n";
            foreach($dbox as $dbx){

                print_r(array($dbx->pickupStatus, $dbx->pickuptime) );

                $ship = Shipment::where('delivery_id','=',$dbx->deliveryId)
                            ->where('pickuptime','!=','0000-00-00 00:00:00')
                            ->first();
                if($ship){
                    print 'before : '.$ship->pickup_status."\r\n";
                    print 'before : '.$ship->pickuptime."\r\n";

                    $pickuptime = ($dbx->pickuptime == '0000-00-00 00:00:00')? date('Y-m-d H:i:s', $dbx->created_at->sec ) :$dbx->pickuptime;

                    $ship->pickup_status = $dbx->pickupStatus;
                    $ship->pickuptime = $pickuptime;

                    $ship->save();
                    //print_r( $ship->toArray());

                    print 'after : '.$ship->pickup_status."\r\n";
                    print 'after : '.$ship->pickuptime."\r\n";
                }
            }
        }
        */
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('example', InputArgument::OPTIONAL, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
