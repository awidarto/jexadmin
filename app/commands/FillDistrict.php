<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillDistrict extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'jex:district';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fill Empty District Field';

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
		$orders = Shipment::where('buyerdeliveryzone','=','')->get();

        $cities = array();
        foreach($orders as $order){
            $cities[] = $order->buyerdeliverycity;
        }

        $cities = array_unique($cities);

        $districts = Coverage::whereIn('city', $cities)->get();

        $district_list = array();
        foreach ($districts as $d) {
            $district_list[$d->city][] = $d->district;
        }

        $matches = 0;
        foreach($orders as $order){
            foreach ($district_list[$order->buyerdeliverycity] as $dist){
                foreach ($dist as $d){
                    if(preg_match('/'.$d.'/i', $order->shipping_address)){
                        print 'match '.$d.' for '.$order->delivery_id;
                        $matches++;
                    }
                    # code...
                }
            }
        }

        print 'found '.$matches.' matches';

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
