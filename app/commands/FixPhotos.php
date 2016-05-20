<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixPhotos extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'photo:fix';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fix Photo Sizes.';

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
		$dirs = glob( public_path().'/storage/media/*' );

        $ps = \Config::get('picture.sizes');

        foreach ($dirs as $dir) {

            $files = glob($dir.'/*');

            $original = '';
            foreach ($files as $file) {
                if(preg_match('/\/th_|\/lrg_|\/full_|\/med_/i', $file)){

                }else{

                    $original = $file;
                }
            }


            if($original != ''){

                $filename = explode('/', $original);
                $filename = array_pop($filename);

                print $filename."\r\n";

                $destinationPath = $dir;

                $img = Image::make($original);

                if( $img->width() >= $img->height()){
                    //image is landscape
                    $full = $img
                        ->save($destinationPath.'/full_'.$filename);

                    $large = $img
                        ->resize($ps['large']['width'],null,function($c){
                            $c->aspectRatio();
                        })
                        //->fit($ps['large']['width'],$ps['large']['height'])
                        ->save($destinationPath.'/lrg_'.$filename);

                    $medium = $img
                        ->resize($ps['medium']['width'],null,function($c){
                            $c->aspectRatio();
                        })
                        //->fit($ps['medium']['width'],$ps['medium']['height'])
                        ->save($destinationPath.'/med_'.$filename);

                    $thumbnail = $img
                        //->resize($ps['thumbnail']['width'],$ps['thumbnail']['height'],function($c){
                        //    $c->aspectRatio();
                        //})
                        ->fit($ps['thumbnail']['width'],$ps['thumbnail']['height'])
                        ->save($destinationPath.'/th_'.$filename);

                }else{

                    $full = $img
                        ->save($destinationPath.'/full_'.$filename);

                    $large = $img
                        ->resize(null,$ps['large']['height'],function($c){
                            $c->aspectRatio();
                        })
                        //->fit($ps['large']['height'],$ps['large']['height'])
                        ->save($destinationPath.'/lrg_'.$filename);

                    $medium = $img
                        ->resize(null,$ps['medium']['height'],function($c){
                            $c->aspectRatio();
                        })
                        //->fit($ps['medium']['height'],$ps['medium']['height'])
                        ->save($destinationPath.'/med_'.$filename);

                    $thumbnail = $img
                        //->resize($ps['thumbnail']['width'],$ps['thumbnail']['height'],function($c){
                        //    $c->aspectRatio();
                        //})
                        ->fit($ps['thumbnail']['width'],$ps['thumbnail']['height'])
                        ->save($destinationPath.'/th_'.$filename);

                }


                    /*
                    try{


                        $destinationPath = $dir;

                        $thumbnail = \Image::make($destinationPath.'/'.$filename)
                            ->fit($ps['thumbnail']['width'],$ps['thumbnail']['height'])
                            ->save($destinationPath.'/th_'.$filename);

                        $medium = \Image::make($destinationPath.'/'.$filename)
                            ->fit($ps['medium']['width'],$ps['medium']['height'])
                            ->save($destinationPath.'/med_'.$filename);

                        $large = \Image::make($destinationPath.'/'.$filename)
                            ->fit($ps['large']['width'],$ps['large']['height'])
                            ->save($destinationPath.'/lrg_'.$filename);

                        $full = \Image::make($destinationPath.'/'.$filename)
                            ->save($destinationPath.'/full_'.$filename);

                    }catch(Exception $e){
                        print $e->getMessage();
                    }
                    */

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
