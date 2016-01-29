<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeliveryBacktrack extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'backtrack:delivery';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delivery Backtrack';

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
		$delivereds = Orderlog::where('appname','=',Config::get('jex.tracker_app'))
                        ->where('status','=','delivered')
                        ->orderBy('deliveryId','desc')
                        ->orderBy('created_at','desc')
                        ->groupBy('deliveryId')
                        ->get(array( 'deliveryId', 'merchantTransId' ,'deliverytime' ));
        /*
        $pendingan = Orderlog::where('appname','=',Config::get('jex.tracker_app'))
                        ->where('pendingCount','!=', strval(0))
                        ->orderBy('created_at','desc')
                        ->orderBy('deliveryId','desc')
                        ->groupBy('deliveryNote')
                        ->get(array( 'deliveryId', 'deliveryNote','status' ));
        */

        $count = 0;

        $data = '';
        foreach ($delivereds as $d) {

            $shipment = \Shipment::where('delivery_id','=',$d->deliveryId)->first();

            if($shipment){
                if(date( 'Y-m-d', strtotime($d->deliverytime) ) != date( 'Y-m-d', strtotime($shipment->deliverytime) ) ){
                    //print $d->deliveryId." ".$d->deliverytime." ".$shipment->deliverytime."\r\n";

                    $data .= '"'.$d->deliveryId.'","'.$d->merchantTransId.'","'.$d->deliverytime.'","'.$shipment->deliverytime.'"'."\r\n";

                    $shipment->deliverytime = $d->deliverytime;

                    $shipment->save();

                    $count++;
                }

            }

        }

        print $data;

        //print "\r\ndifferent date : ".$count;

        /*
        $pc = array();
        foreach ($pendingan as $p) {
            if(isset($pc[$p->deliveryId])){
                if($p->status == 'pending'){
                    $pc[$p->deliveryId] = $pc[$p->deliveryId] + 1;
                }
            }else{
                $pc[$p->deliveryId] = 1;
            }
        }
        */

        //print_r($pc);
        /*
        foreach($pc as $d=>$c){
            //print $d->deliveryId." ".$d->deliverytime."\r\n";
            $count++;
            $shipment = \Shipment::where('delivery_id','=',$d)->first();

            if($shipment){
                $shipment->pending_count = $c;
                //$shipment->status = 'delivered';
                //$shipment->deliverytime = $d->deliverytime;
                //$shipment->save();
            }

            //print_r($d->toArray());
        }
        */

        print "\r\n".$count;
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
