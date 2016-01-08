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
                        ->orderBy('created_at','desc')
                        ->orderBy('deliveryId','desc')
                        ->groupBy('deliveryId')
                        ->get(array( 'deliveryId', 'deliverytime' ));

        foreach($delivereds as $d){
            //print $d->deliveryId." ".$d->deliverytime."\r\n";
            print_r($d->toArray());
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
