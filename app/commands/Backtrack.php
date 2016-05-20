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
	protected $name = 'backtrack:image';

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
        $img_ids = array(

                '0742e9da70696b4',
                '2f3de6087c42820',
                '67605b7a79914ea',
                '917a623ee3adb7d',
                'd947446c56f6427',
                '0c3dfb64b81d780',
                '2f47c9232d4b9f6',
                '679e949fadd4880',
                '91f34261d8b7b63',
                'dba249421586825',
                '0c56338bfe9e170',
                '3335ee6f0de5781',
                '6a55b8f1dd91028',
                'a1df2bea18be518',
                'e1053cc97d82515',
                '0c7df6c2c3428eb',
                '35c04f272e9b031',
                '6bd653bd9273dc8',
                'a24f4cf6b7573d0',
                'e398db4c1c16494',
                '0cfa433cd8ae65f',
                '3672857a6620558',
                '6ead3f012c58c60',
                'ac6c5166f36e463',
                'e982d227c63357b',
                '0d4d6667f590238',
                '3793e16a0ec0717',
                '714adf42ae37508',
                'b1a6dd74b28139e',
                'ea8082e04e9a80b',
                '0f593e4f4608296',
                '384e7e43bfe5451',
                '71900b0c79a6ecd',
                'b4b83c0fc12d596',
                'ec26c99a7528e40',
                '162d78e46704d07',
                '38f3716a13ce242',
                '74668d926446870',
                'b4f9f39e188e90b',
                'ef040e9bb4b3e40',
                '1a4fa28569d2433',
                '3b03604218a10e0',
                '74fb8eefb59f1b5',
                'b6182ce6c3db8e8',
                'f1a8865e346289f',
                '1a64460add817e9',
                '3b7a79f27369ffd',
                '7d359026b3235a7',
                'b636b7c0f3fa5c9',
                'f2c5bd2f3b92062',
                '1b38c0bdce42868',
                '3f2b3d2e961d47f',
                '7d47f3264eb4e34',
                'b961fcb4d85690e',
                'f4f8b2142bb0a2e',
                '1db98eca7a3bb5a',
                '46184a2fd201777',
                '7d6bb3c3ffefbfd',
                'bc62475323cc204',
                'f7e659e2fb7805e',
                '1dd58d9adadbb36',
                '490b9144c2a7513',
                '814387eb8e6df5f',
                'c5ddf0ff25555ba',
                'fa1afdaeb23ea7b',
                '22f35831534faae',
                '4bf0367144b5c5a',
                '83927c9d92c1c5e',
                'c5edef0ef0647ed',
                'fd3736b7b59d7cf',
                '287c34fd6217b9a',
                '55fd0b3a93acec8',
                '877dbec3d98240f',
                'ccf7747f99d9b32',
                '2aabcb20c92a66f',
                '58d609ddc243770',
                '880cf33b3a01b1f',
                'cddeef6176650d3',
                '2e066291e6905ab',
                '61df95a37dd69ab',
                '8a781f396ee3bfe',
                'd2adeabb83c281e'
            );

            $files = Uploaded::whereIn('file_id',$img_ids)->get();

            foreach($files as $file){


                if(preg_match('/201605/', $file->thumbnail_url)){
                    $file->thumbnail_url = str_replace('storage/media', 'storage/media2', $file->thumbnail_url);
                    $file->large_url = str_replace('storage/media', 'storage/media2', $file->large_url);
                    $file->medium_url = str_replace('storage/media', 'storage/media2', $file->medium_url);
                    $file->full_url = str_replace('storage/media', 'storage/media2', $file->full_url);

                }else{
                    $file->thumbnail_url = str_replace('storage/media', 'storage/media2/201605', $file->thumbnail_url);
                    $file->large_url = str_replace('storage/media', 'storage/media2/201605', $file->large_url);
                    $file->medium_url = str_replace('storage/media', 'storage/media2/201605', $file->medium_url);
                    $file->full_url = str_replace('storage/media', 'storage/media2/201605', $file->full_url);
                }


                print $file->thumbnail_url."\r\n";
                print $file->large_url."\r\n";
                print $file->medium_url."\r\n";
                print $file->full_url."\r\n";

                $file->save();

            }


        /*
        $shipped = Shipment::where('deliverytime','like','2016-01-11%')->get();

        foreach($shipped as $shipment){

            $is_there = Geolog::where('datetimestamp','=',$shipment->deliverytime)
                                ->where('deliveryId' ,'=',  $shipment->delivery_id)
                                //->where('status','=', $shipment->status)
                                ->where('sourceSensor','=','gps')
                                ->get();

            if($is_there){

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

        }


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
