<?php

class DeliveryreportController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->controller_name = str_replace('Controller', '', get_class());

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Shipment();
        //$this->model = DB::collection('documents');
        $this->title = 'Delivery Report';

    }

    public function getIndex()
    {

        $this->title = 'Delivery Report';

        $this->place_action = 'none';

        $this->show_select = false;

        $this->can_add = false;

        $this->is_report = true;

        Breadcrumbs::addCrumb('Manifest',URL::to( strtolower($this->controller_name) ));

        $this->additional_filter = View::make('shared.addfilter')->with('submit_url','deliveryreport')->render();

        //device=&courier=&logistic=&date-from=2015-10-24

        $period_from = Input::get('date-from');
        $period_to = Input::get('date-to');

        $device = Input::get('device');
        $courier = Input::get('courier');

        $merchant = Input::get('merchant');
        $logistic = Input::get('logistic');

        $status = Input::get('status');
        $courierstatus = Input::get('courier-status');

        if($period_to == '' || is_null($period_to) ){
            $period_to = date('Y-m-d',time());
        }

        if($period_from == '' || is_null($period_from) ){
            $period_from = date('Y-m-d',time());
        }


        $this->def_order_by = 'TRANS_DATETIME';
        $this->def_order_dir = 'DESC';
        $this->place_action = 'none';
        $this->show_select = false;

        $mtab = Config::get('jayon.assigned_delivery_table');

        $model = new Shipment();

        $model = $model->select( DB::raw('count(*) as count, year(ordertime) as orderyear,weekofyear(ordertime) as orderweek, date(ordertime) as orderdate, m.merchantname as merchant_name, delivery_type, sum(delivery_cost) as delivery_cost, sum(cod_cost) as cod_cost') )
                    ->leftJoin('members as m', $mtab.'.merchant_id','=','m.id')
                    ->orderBy('m.merchantname', 'asc');


        if($period_from == '' || is_null($period_from) ){
            $datefrom = date( 'Y-m-d 00:00:00', strtotime($period_from) );
            $dateto = date( 'Y-m-d 23:59:59', strtotime($period_to) );

        }else{

            $datefrom = date( 'Y-m-d 00:00:00', strtotime($period_from) );
            $dateto = date( 'Y-m-d 23:59:59', strtotime($period_to) );

            $model = $model->where(function($q) use($datefrom,$dateto){
                $q->whereBetween('ordertime',array($datefrom,$dateto));
            });

        }

        $model->orderBy('ordertime','asc')
                ->groupBy('orderdate')
                ->groupBy('orderyear')
                ->groupBy('merchant_id')
                ->groupBy('delivery_type');

        $actualresult = $model->get();

        /* Start custom queries */


        $tattrs = array('width'=>'100%','class'=>'table table-bordered table-striped');


        $bymc = array();

        $bydecost = array();

        $bycodcost = array();

        $effdates = array();

        foreach($actualresult as $mc){
            if(isset($bymc[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate])){
                $bymc[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] += $mc->count;
            }else{
                $bymc[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] = $mc->count;
            }

            if(isset($bydecost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate])){
                $bydecost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] += $mc->delivery_cost;
            }else{
                $bydecost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] = $mc->delivery_cost;
            }

            if(isset($bycodcost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate])){
                $bycodcost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] += $mc->cod_cost;
            }else{
                $bycodcost[$mc->delivery_type][$mc->merchant_name][$mc->orderyear][$mc->orderweek][$mc->orderdate] = $mc->cod_cost;
            }

            $effdates[$mc->orderyear][$mc->orderweek][] = $mc->orderdate;
        }

        $effdates2 = array();

        foreach ($effdates as $yr => $wk) {
            ksort($wk);
            foreach ($wk as $k => $v) {
                $dts = array_unique($v);
                $effdates2[$yr][$k][] = array_shift($dts);
                $effdates2[$yr][$k][] = array_pop($dts);

            }
        }

        //print_r($effdates2);

        //print_r($bymc);

        $bydc = array();

        $tpd = array();

        $bpd = array();

        $wpd = array();


        $dtotal = array();

        $btotal = array();

        $wtotal = array();

        $headvar1 = array(
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>'')
        );

        $headvar2 = array(
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>'')

            );
        $headvar3 = array(
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>''),
            array('value'=>'','attr'=>'')

            );
        $headvar4 = array(
            array('value'=>'No.','attr'=>''),
            array('value'=>'Merchant','attr'=>''),
            array('value'=>'Total','attr'=>''),
            array('value'=>'DCost','attr'=>''),
            array('value'=>'CODSur','attr'=>'')

            );

        $weekspan = 0;

        $weektotal = array();

        foreach ($effdates2 as $y => $w) {

            $cy = '';
            foreach ($w as $wk => $v) {
                $headvar1[] = array('value'=>'','attr'=>'colspan="3"');

                if($cy != $y){
                    $headvar2[] = array('value'=>$y,'attr'=>'colspan="3"');
                }else{
                    $headvar2[] = array('value'=>'','attr'=>'colspan="3"');
                }

                $headvar3[] = array('value'=>$wk,'attr'=>'colspan="3"');
                $headvar4[] = array('value'=>$v[0].' - '.$v[1],'attr'=>'colspan="3"');
                $headvar1[] = array('value'=>'','attr'=>'colspan="3"');

                $weektotal[ $wk ] = array('count'=>0, 'dcost'=>0, 'codsur'=>0);


                $cy = $y;
                $weekspan++;
            }
        }

        //print $weekspan;

        $tabdata = array();

        $weekspan++;

        $tarr = array();
        $tharr = array();
        $thval = array();

        //print_r($weektotal);

        foreach ($bymc as $t => $m) {

            //$weekspan += 1;

            //$tabdata[] = $head;

            $seq = 1;

            $totaltype = 0;
            $totaldcost = 0;
            $totalcodsur = 0;

            foreach ($m as $mc=> $yr) {

                $mtotaltype = 0;
                $mtotaldcost = 0;
                $mtotalcodsur = 0;

                $row = array();

                $row[] = array('value'=>$seq,'attr'=>'');
                $row[] = array('value'=>$mc,'attr'=>'');

                $valrows = array();
                $totalrows = 0;
                $totaldrows = 0;
                $totalcrows = 0;

                foreach ($effdates2 as $yr=>$w) {



                    foreach ($w as $wk=>$dt) {

                        $val = 0;
                        $dcost = 0;
                        $codcost = 0;

                        if(isset($bymc[$t][$mc][$yr][$wk])){
                            //print_r($bymc[$t][$mc][$yr][$wk]);
                            $vr = $bymc[$t][$mc][$yr][$wk];
                            foreach ($vr as $k => $v) {
                                $val += $v;
                            }

                        }else{
                            $val = 0;
                        }

                        $weektotal[$wk]['count'] += $val;

                        if(isset($bydecost[$t][$mc][$yr][$wk])){
                            //print_r($bymc[$t][$mc][$yr][$wk]);
                            $dr = $bydecost[$t][$mc][$yr][$wk];
                            foreach ($dr as $k => $v) {
                                $dcost += $v;
                            }

                        }else{
                            $dcost = 0;
                        }

                        $weektotal[$wk]['dcost'] += $dcost;

                        if(isset($bycodcost[$t][$mc][$yr][$wk])){
                            //print_r($bymc[$t][$mc][$yr][$wk]);
                            $cr = $bycodcost[$t][$mc][$yr][$wk];
                            foreach ($cr as $k => $v) {
                                $codcost += $v;
                            }

                        }else{
                            $codcost = 0;
                        }

                        $weektotal[$wk]['codsur'] += $codcost;

                        $valrows[] = array('value'=>$val,'attr'=>'style="text-align:right;"');
                        $totalrows += $val;

                        $valrows[] = array('value'=>$dcost,'attr'=>'style="text-align:right;"');
                        $totaldrows += $dcost;

                        $valrows[] = array('value'=>$codcost,'attr'=>'style="text-align:right;"');
                        $totalcrows += $codcost;

                        $mtotaltype += $totalrows;
                        $mtotaldcost += $totaldrows;
                        $mtotalcodsur += $totalcrows;

                    }

                    $totaltype += $totalrows;
                    $totaldcost += $totaldrows;
                    $totalcodsur += $totalcrows;

                    $mtotaltype += $totaltype;
                    $mtotaldcost += $totaldcost;
                    $mtotalcodsur += $totalcodsur;


                }


                $row[] = array('value'=>$totalrows,'attr'=>'style="text-align:right;"');
                $row[] = array('value'=>$totaldrows,'attr'=>'style="text-align:right;"');
                $row[] = array('value'=>$totalcrows,'attr'=>'style="text-align:right;"');

                $mrow = array_merge($row, $valrows);

                $tarr[$t][] = $mrow;

                $seq++;

            }



            // subhead
            $head = array();

            $head[] = array('value'=>'','attr'=>'');
            $head[] = array('value'=>strtoupper($t),'attr'=>'style="text-align:right;"');

            $head[] = array('value'=>$totaltype,'attr'=>'style="text-align:right;font-weight:bold;"');
            $head[] = array('value'=>$totaldcost,'attr'=>'style="text-align:right;font-weight:bold;"');
            $head[] = array('value'=>$totalcodsur,'attr'=>'style="text-align:right;font-weight:bold;"');

            //for($i = 1; $i < $weekspan;$i++){
            foreach($weektotal as $wx=>$tv){
                $head[] = array('value'=>$tv['count'],'attr'=>'style="text-align:right;font-weight:bold;"');
                $head[] = array('value'=>$tv['dcost'],'attr'=>'style="text-align:right;font-weight:bold;"');
                $head[] = array('value'=>$tv['codsur'],'attr'=>'style="text-align:right;font-weight:bold;"');
            }
            $tharr[$t] = $head;

            $thval[$t] = $totaltype;


        }

        $totalorder = 0;

        foreach ($tharr as $t=>$v) {

            $tabdata[] = $tharr[$t];
            $totalorder += $thval[$t];
            foreach($tarr[$t] as $tv){
                $tabdata[] = $tv;
            }

        }

        $sumup[] = array('value'=>'','attr'=>'');
        $sumup[] = array('value'=>'<b>TOTAL</b>','attr'=>'style="text-align:right;"');

        $sumup[] = array('value'=>$totalorder,'attr'=>'style="text-align:right;"');
        $sumup[] = array('value'=>'','attr'=>'style="text-align:right;"');
        $sumup[] = array('value'=>'','attr'=>'style="text-align:right;"');

        for($i = 1; $i < $weekspan;$i++){
            $sumup[] = array('value'=>'','attr'=>'');
        }

        $tabdata[] = $sumup;

        //die();

        $thead = array();
        //$thead[] = $headvar1;
        $thead[] = $headvar2;
        $thead[] = $headvar3;
        $thead[] = $headvar4;



        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $d = 0;
        $gt = 0;

        $lastdate = '';

        $courier_name = '';

        $csv_data = array();

        $dids = array();

        $cntcod = 0;
        $cntccod = 0;
        $cntdo = 0;
        $cntps = 0;
        $cntreturn = 0;

        $box_count = 0;

        $weight_sum = 0;

        //total per columns
        $tcod = 0;
        $tccod = 0;
        $tdo = 0;
        $tps = 0;
        $treturn = 0;

        $tbox = 0;

        $tweight = 0;

        $totalrow = array();

        $mname = '';
        $cd = '';


        $mtable = new HtmlTable($tabdata,$tattrs,$thead);

        $tables[] = $mtable->build();

        $this->table_raw = $tables;

        $report_header_data = array(
                'cod'=>$cntcod,
                'ccod'=>$cntccod,
                'do'=>$cntdo,
                'ps'=>$cntps,
                'return'=>$cntreturn,
                'avg'=>0
        );

        if($this->print == true || $this->pdf == true){
            return array('tables'=>$tables,'report_header_data'=>$report_header_data);
        }else{
            return parent::reportPageGenerator();
        }


    }

    public function postIndex()
    {

        $this->fields = array(
            array('PERIOD',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('TRANS_DATETIME',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('VCHR_NUM',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('ACCNT_CODE',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('j10_acnt.DESCR',array('kind'=>'text', 'alias'=>'ACC_DESCR' , 'query'=>'like', 'pos'=>'both','show'=>true)),
            array('TREFERENCE',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('CONV_CODE',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('OTHER_AMT',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('BASE_RATE',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('AMOUNT',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('DESCRIPTN',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true))
        );

        /*
        $categoryFilter = Input::get('categoryFilter');
        if($categoryFilter != ''){
            $this->additional_query = array('shopcategoryLink'=>$categoryFilter, 'group_id'=>4);
        }
        */

        $db = Config::get('lundin.main_db');

        $company = Input::get('acc-company');

        $company = strtolower($company);

        if(Schema::hasTable( $db.'.'.$company.'_a_salfldg' )){
            $company = Config::get('lundin.default_company');
        }

        $company = strtolower($company);

        $this->def_order_by = 'TRANS_DATETIME';
        $this->def_order_dir = 'DESC';
        $this->place_action = 'none';
        $this->show_select = false;

        $this->sql_key = 'TRANS_DATETIME';
        $this->sql_table_name = $company.'_a_salfldg';
        $this->sql_connection = 'mysql2';

        return parent::SQLtableResponder();
    }

    public function getStatic()
    {

    }

    public function getPrint()
    {

        $this->print = true;

        $tables = $this->getIndex();

        $this->table_raw = $tables['tables'];
        $this->report_header_data = $tables['report_header_data'];


        $this->report_entity = false;
        $sequencer = new Sequence();
        $doc_number = $sequencer->getNewId('deliverybydate');

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addhead')
                                            ->with('doc_number',$doc_number)
                                            ->with('report_header_data',$this->report_header_data)
                                            ->render();

        $this->report_file_name = 'MDL-'.str_pad($doc_number, 5, '0', STR_PAD_LEFT).'.html';
        $this->report_file_path = realpath('storage/docs').'/deliverybydate/';

        $this->title = 'DELIVERY BY DATE';

        $this->report_type = 'deliverybydate';

        return parent::printReport();
    }

    public function getGenpdf()
    {

        $this->pdf = true;

        $tables = $this->getIndex();

        $this->table_raw = $tables;

        $this->report_entity = false;
        $sequencer = new Sequence();
        $doc_number = $sequencer->getNewId('devmanifest');

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addhead')
                                            ->with('doc_number',$doc_number)
                                            ->render();

        $this->report_file_name = 'MDL-'.str_pad($doc_number, 5, '0', STR_PAD_LEFT).'.html';
        $this->report_file_path = realpath('storage/docs').'/deliverybydate/';

        $this->title = 'DELIVERY BY DATE';

        $this->report_type = 'deliverybydate';

        return parent::printReport();
    }

    public function SQL_make_join($model)
    {
        //$model->with('coa');

        //PERIOD',TRANS_DATETIME,VCHR_NUM,ACC_DESCR,DESCRIPTN',TREFERENCE',CONV_CODE,AMOUNT',AMOUNT',DESCRIPTN'

        $model = $model->select('j10_a_salfldg.*','j10_acnt.DESCR as ACC_DESCR')
            ->leftJoin('j10_acnt', 'j10_a_salfldg.ACCNT_CODE', '=', 'j10_acnt.ACNT_CODE' );
        return $model;
    }

    public function SQL_additional_query($model)
    {
        $in = Input::get();

        $txtab = Config::get('jayon.incoming_delivery_table');

        $model = $model->where(function($query){
                            $query->where('bucket','=',Config::get('jayon.bucket_tracker'))
                                ->where(function($qs){
                                    $qs->where('logistic_type','=','external')
                                        ->orWhere(function($qx){
                                                $qx->where('logistic_type','=','internal')
                                                    ->where(function($qz){
                                                        $qz->where('status','=', Config::get('jayon.trans_status_admin_courierassigned') )
                                                            ->orWhere('status','=', Config::get('jayon.trans_status_mobile_pickedup') )
                                                            ->orWhere('status','=', Config::get('jayon.trans_status_mobile_enroute') )
                                                            ->orWhere(function($qx){
                                                                $qx->where('status', Config::get('jayon.trans_status_new'))
                                                                    ->where('pending_count', '>', 0);
                                                            });
                                                    });

                                        });
                                });


        })
        ->orderBy('assignment_date');

        return $model;

    }

    public function SQL_before_paging($model)
    {
        $m_original_amount = clone($model);
        $m_base_amount = clone($model);

        $aux['total_data_base'] = $m_base_amount->sum('OTHER_AMT');
        $aux['total_data_converted'] = $m_original_amount->sum('AMOUNT');

        //$this->aux_data = $aux;

        return $aux;
        //print_r($this->aux_data);

    }

    public function rows_post_process($rows, $aux = null){

        //print_r($this->aux_data);

        $total_base = 0;
        $total_converted = 0;
        $end = 0;

        $br = array_fill(0, $this->column_count(), '');


        $nrows = array();

        $subhead1 = '';
        $subhead2 = '';
        $subhead3 = '';

        $seq = 0;

        $subamount1 = 0;
        $subamount2 = 0;

        if(count($rows) > 0){

            for($i = 0; $i < count($rows);$i++){

                //print_r($rows[$i]['extra']);

                if($subhead1 == '' || $subhead1 != $rows[$i][1] || $subhead2 != $rows[$i][4] ){

                    $headline = $br;
                    if($subhead1 != $rows[$i][1]){
                        $headline[1] = '<b>'.$rows[$i]['extra']['PERIOD'].'</b>';
                    }else{
                        $headline[1] = '';
                    }

                    $headline[4] = '<b>'.$rows[$i]['extra']['ACCNT_CODE'].'</b>';
                    $headline['extra']['rowclass'] = 'row-underline';

                    if($subhead1 != ''){
                        $amtline = $br;
                        $amtline[8] = '<b>'.Ks::idr($subamount1).'</b>';
                        $amtline[10] = '<b>'.Ks::idr($subamount2).'</b>';
                        $amtline['extra']['rowclass'] = 'row-doubleunderline row-overline';

                        $nrows[] = $amtline;
                        $subamount1 = 0;
                        $subamount2 = 0;
                    }

                    $subamount1 += $rows[$i]['extra']['OTHER_AMT'];
                    $subamount2 += $rows[$i]['extra']['AMOUNT'];

                    $nrows[] = $headline;

                    $seq = 1;
                    $rows[$i][0] = $seq;

                    $rows[$i][8] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::idr($rows[$i][8]):'';
                    $rows[$i][9] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::dec2($rows[$i][9]):'';
                    $rows[$i][10] = Ks::usd($rows[$i][10]);

                    $nrows[] = $rows[$i];
                }else{
                    $seq++;
                    $rows[$i][0] = $seq;

                    $rows[$i][8] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::idr($rows[$i][8]):'';
                    $rows[$i][9] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::dec2($rows[$i][9]):'';
                    $rows[$i][10] = Ks::usd($rows[$i][10]);

                    $nrows[] = $rows[$i];


                }

                $total_base += doubleval( $rows[$i][8] );
                $total_converted += doubleval($rows[$i][10]);
                $end = $i;

                $subhead1 = $rows[$i][1];
                $subhead2 = $rows[$i][4];
            }

            // show total Page
            if($this->column_count() > 0){

                $tb = $br;
                $tb[1] = 'Total Page';
                $tb[8] = Ks::idr($total_base);
                $tb[10] = Ks::usd($total_converted);

                $nrows[] = $tb;

                if(!is_null($this->aux_data)){
                    $td = $br;
                    $td[1] = 'Total';
                    $td[8] = Ks::idr($aux['total_data_base']);
                    $td[10] = Ks::usd($aux['total_data_converted']);
                    $nrows[] = $td;
                }

            }

            return $nrows;

        }else{

            return $rows;

        }


        // show total queried


    }


    public function beforeSave($data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];

            $data['shortcode'] = str_random(5);

        return $data;
    }

    public function beforeUpdate($id,$data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        if(!isset($data['shortcode']) || $data['shortcode'] == ''){
            $data['shortcode'] = str_random(5);
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];


        return $data;
    }

    public function beforeUpdateForm($population)
    {
        //print_r($population);
        //exit();

        return $population;
    }

    public function afterSave($data)
    {

        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'new';
        $hdata['historySequence'] = 0;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);

        return $data;
    }

    public function afterUpdate($id,$data = null)
    {
        $data['_id'] = new MongoId($id);


        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'update';
        $hdata['historySequence'] = 1;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);


        return $id;
    }


    public function postAdd($data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        return parent::postAdd($data);
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        //exit();

        return parent::postEdit($id,$data);
    }

    public function postDlxl()
    {
        set_time_limit(0);

        $this->report_filter_input = Input::all();

        //print_r($this->report_filter_input);

        $this->print = true;

        $table = $this->getIndex();

        //print_r($table);

        //$view = View::make('print.xls')->with('tables',$table['tables'])->render();

        //print $view;

        $this->export_output_fields = $table;

        return parent::postTabletoxls();

    }

    public function getImport(){

        $this->importkey = 'SKU';

        return parent::getImport();
    }

    public function postUploadimport()
    {
        $this->importkey = 'SKU';

        return parent::postUploadimport();
    }

    public function beforeImportCommit($data)
    {
        $defaults = array();

        $files = array();

        // set new sequential ID


        $data['priceRegular'] = new MongoInt32($data['priceRegular']);

        $data['thumbnail_url'] = array();
        $data['large_url'] = array();
        $data['medium_url'] = array();
        $data['full_url'] = array();
        $data['delete_type'] = array();
        $data['delete_url'] = array();
        $data['filename'] = array();
        $data['filesize'] = array();
        $data['temp_dir'] = array();
        $data['filetype'] = array();
        $data['fileurl'] = array();
        $data['file_id'] = array();
        $data['caption'] = array();

        $data['defaultpic'] = '';
        $data['brchead'] = '';
        $data['brc1'] = '';
        $data['brc2'] = '';
        $data['brc3'] = '';


        $data['defaultpictures'] = array();
        $data['files'] = array();

        return $data;
    }

    public function postRack()
    {
        $locationId = Input::get('loc');
        if($locationId == ''){
            $racks = Assets::getRack()->RackToSelection('_id','SKU',true);
        }else{
            $racks = Assets::getRack(array('locationId'=>$locationId))->RackToSelection('_id','SKU',true);
        }

        $options = Assets::getRack(array('locationId'=>$locationId));

        return Response::json(array('result'=>'OK','html'=>$racks, 'options'=>$options ));
    }

    public function makeActions($data)
    {
        /*
        if(!is_array($data)){
            $d = array();
            foreach( $data as $k->$v ){
                $d[$k]=>$v;
            }
            $data = $d;
        }

        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-times-circle"></i> Delete</span>';
        $edit = '<a href="'.URL::to('advertiser/edit/'.$data['_id']).'"><i class="fa fa-edit"></i> Update</a>';
        $dl = '<a href="'.URL::to('brochure/dl/'.$data['_id']).'" target="new"><i class="fa fa-download"></i> Download</a>';
        $print = '<a href="'.URL::to('brochure/print/'.$data['_id']).'" target="new"><i class="fa fa-print"></i> Print</a>';
        $upload = '<span class="upload" id="'.$data['_id'].'" rel="'.$data['SKU'].'" ><i class="fa fa-upload"></i> Upload Picture</span>';
        $inv = '<span class="upinv" id="'.$data['_id'].'" rel="'.$data['SKU'].'" ><i class="fa fa-upload"></i> Update Inventory</span>';
        $stat = '<a href="'.URL::to('stats/merchant/'.$data['id']).'"><i class="fa fa-line-chart"></i> Stats</a>';

        $history = '<a href="'.URL::to('advertiser/history/'.$data['_id']).'"><i class="fa fa-clock-o"></i> History</a>';

        $actions = $stat.'<br />'.$edit.'<br />'.$delete;
        */
        $actions = '';
        return $actions;
    }

    public function accountDesc($data)
    {

        return $data['ACCNT_CODE'];
    }

    public function extractCategory()
    {
        $category = Product::distinct('category')->get()->toArray();
        $cats = array(''=>'All');

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public function splitTag($data){
        $tags = explode(',',$data['tags']);
        if(is_array($tags) && count($tags) > 0 && $data['tags'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['tags'];
        }
    }

    public function splitShare($data){
        $tags = explode(',',$data['docShare']);
        if(is_array($tags) && count($tags) > 0 && $data['docShare'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['docShare'];
        }
    }

    public function locationName($data){
        if(isset($data['locationId']) && $data['locationId'] != ''){
            $loc = Assets::getLocationDetail($data['locationId']);
            return $loc->name;
        }else{
            return '';
        }

    }

    public function catName($data)
    {
        return $data['shopcategory'];
    }

    public function rackName($data){
        if(isset($data['rackId']) && $data['rackId'] != ''){
            $loc = Assets::getRackDetail($data['rackId']);
            if($loc){
                return $loc->SKU;
            }else{
                return '';
            }
        }else{
            return '';
        }

    }

    public function postSynclegacy(){

        set_time_limit(0);

        $mymerchant = Merchant::where('group_id',4)->get();

        $count = 0;

        foreach($mymerchant->toArray() as $m){

            $member = Member::where('legacyId',$m['id'])->first();

            if($member){

            }else{
                $member = new Member();
            }

            foreach ($m as $k=>$v) {
                $member->{$k} = $v;
            }

            if(!isset($member->status)){
                $member->status = 'inactive';
            }

            if(!isset($member->url)){
                $member->url = '';
            }

            $member->legacyId = new MongoInt32($m['id']);

            $member->roleId = Prefs::getRoleId('Merchant');

            $member->unset('id');

            $member->save();

            $count++;
        }

        return Response::json( array('result'=>'OK', 'count'=>$count ) );

    }

    public function statNumbers($data){
        $datemonth = date('M Y',time());
        $firstday = Carbon::parse('first day of '.$datemonth);
        $lastday = Carbon::parse('last day of '.$datemonth)->addHours(23)->addMinutes(59)->addSeconds(59);

        $qval = array('$gte'=>new MongoDate(strtotime($firstday->toDateTimeString())),'$lte'=>new MongoDate( strtotime($lastday->toDateTimeString()) ));

        $qc = array();

        $qc['adId'] = $data['_id'];

        $qc['clickedAt'] = $qval;

        $qv = array();

        $qv['adId'] = $data['_id'];

        $qv['viewedAt'] = $qval;

        $clicks = Clicklog::whereRaw($qc)->count();

        $views = Viewlog::whereRaw($qv)->count();

        return $clicks.' clicks<br />'.$views.' views';
    }

    public function namePic($data)
    {
        $name = HTML::link('property/view/'.$data['_id'],$data['address']);

        $thumbnail_url = '';

        $ps = Config::get('picture.sizes');


        if(isset($data['files']) && count($data['files'])){
            $glinks = '';

            $gdata = $data['files'][$data['defaultpic']];

            $thumbnail_url = $gdata['thumbnail_url'];
            foreach($data['files'] as $g){
                $g['caption'] = ( isset($g['caption']) && $g['caption'] != '')?$g['caption']:$data['SKU'];
                $g['full_url'] = isset($g['full_url'])?$g['full_url']:$g['fileurl'];
                foreach($ps as $k=>$s){
                    if(isset($g[$k.'_url'])){
                        $glinks .= '<input type="hidden" class="g_'.$data['_id'].'" data-caption="'.$k.'" value="'.$g[$k.'_url'].'" />';
                    }
                }
            }
            if(isset($data['useImage']) && $data['useImage'] == 'linked'){
                $thumbnail_url = $data['extImageURL'];
                $display = HTML::image($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }else{
                $display = HTML::image($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }
            return $display;
        }else{
            return $data['SKU'];
        }
    }

    public function dispBar($data)

    {
        $display = HTML::image(URL::to('qr/'.urlencode(base64_encode($data['SKU']))), $data['SKU'], array('id' => $data['_id'], 'style'=>'width:100px;height:auto;' ));
        //$display = '<a href="'.URL::to('barcode/dl/'.urlencode($data['SKU'])).'">'.$display.'</a>';
        return $display.'<br />'. '<a href="'.URL::to('asset/detail/'.$data['_id']).'" >'.$data['SKU'].'</a>';
    }


    public function pics($data)
    {
        $name = HTML::link('products/view/'.$data['_id'],$data['productName']);
        if(isset($data['thumbnail_url']) && count($data['thumbnail_url'])){
            $display = HTML::image($data['thumbnail_url'][0].'?'.time(), $data['filename'][0], array('style'=>'min-width:100px;','id' => $data['_id']));
            return $display.'<br /><span class="img-more" id="'.$data['_id'].'">more images</span>';
        }else{
            return $name;
        }
    }

    public function getPrintlabel($sessionname, $printparam, $format = 'html' )
    {
        $pr = explode(':',$printparam);

        $columns = $pr[0];
        $resolution = $pr[1];
        $cell_width = $pr[2];
        $cell_height = $pr[3];
        $margin_right = $pr[4];
        $margin_bottom = $pr[5];
        $font_size = $pr[6];
        $code_type = $pr[7];
        $left_offset = $pr[8];
        $top_offset = $pr[9];

        $session = Printsession::find($sessionname)->toArray();
        $labels = Asset::whereIn('_id', $session)->get()->toArray();

        $skus = array();
        foreach($labels as $l){
            $skus[] = $l['SKU'];
        }

        $skus = array_unique($skus);

        $products = Asset::whereIn('SKU',$skus)->get()->toArray();

        $plist = array();
        foreach($products as $product){
            $plist[$product['SKU']] = $product;
        }

        return View::make('asset.printlabel')
            ->with('columns',$columns)
            ->with('resolution',$resolution)
            ->with('cell_width',$cell_width)
            ->with('cell_height',$cell_height)
            ->with('margin_right',$margin_right)
            ->with('margin_bottom',$margin_bottom)
            ->with('font_size',$font_size)
            ->with('code_type',$code_type)
            ->with('left_offset', $left_offset)
            ->with('top_offset', $top_offset)
            ->with('products',$plist)
            ->with('labels', $labels);
    }


    public function getViewpics($id)
    {

    }

    public function hide_trx($trx_id){
        if(preg_match('/^TRX_/', $trx_id)){
            return '';
        }else{
            return $trx_id;
        }
    }

    public function short_did($did){
        $did = explode('-',$did);
        return array_pop($did);
    }

    public function date_did($did){
        $did = explode('-',$did);
        if(count($did) == 3){
            $date_did = $did[1].'-'.$did[2];
        }else{
            $date_did = '';
        }
        return $date_did;
    }

    public function split_phone($phone){
        return str_replace(array('/','#','|'), '<br />', $phone);
    }

    public function updateStock($data){

        //print_r($data);

        $outlets = $data['outlets'];
        $outletNames = $data['outletNames'];
        $addQty = $data['addQty'];
        $adjustQty = $data['adjustQty'];

        unset($data['outlets']);
        unset($data['outletNames']);
        unset($data['addQty']);
        unset($data['adjustQty']);

        for( $i = 0; $i < count($outlets); $i++)
        {

            $su = array(
                    'outletId'=>$outlets[$i],
                    'outletName'=>$outletNames[$i],
                    'productId'=>$data['id'],
                    'SKU'=>$data['SKU'],
                    'productDetail'=>$data,
                    'status'=>'available',
                    'createdDate'=>new MongoDate(),
                    'lastUpdate'=>new MongoDate()
                );

            if($addQty[$i] > 0){
                for($a = 0; $a < $addQty[$i]; $a++){
                    $su['_id'] = str_random(40);
                    Stockunit::insert($su);
                }
            }

            if($adjustQty[$i] > 0){
                $td = Stockunit::where('outletId',$outlets[$i])
                    ->where('productId',$data['id'])
                    ->where('SKU', $data['SKU'])
                    ->where('status','available')
                    ->orderBy('createdDate', 'asc')
                    ->take($adjustQty[$i])
                    ->get();

                foreach($td as $d){
                    $d->status = 'deleted';
                    $d->lastUpdate = new MongoDate();
                    $d->save();
                }
            }
        }


    }

}
