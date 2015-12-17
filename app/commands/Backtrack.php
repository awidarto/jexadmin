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
        $dbox = Orderlog::where('pickupStatus','=',Config::get('jayon.trans_status_pickup'))
                            ->get();

        if($dbox){
            foreach($dbox as $dbx){

                print_r($dbx);

                $ship = Shipment::where('delivery_id','=',$dbx->deliveryId)->first();
                if($ship){
                    print $ship->toArray();
                    $ship->pickup_status = $dbx->pickupStatus;
                    $ship->pickuptime = $dbx->pickuptime;

                    print $ship->toArray();

                    //print $ship->pickup_status."\r\n";
                    //print $ship->pickuptime."\r\n";
                }
            }
        }
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
