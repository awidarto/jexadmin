<?php

class Prefs {

    public static $category;
    public static $shopcategory;
    public static $section;
    public static $faqcategory;
    public static $productcategory;
    public static $role;

    public function __construct()
    {

    }

    public static function hashcheck($in , $pass){

        $hash = hash("haval256,5", Config::get('kickstart.ci_key') . $in);

        if($hash == $pass){
            return true;
        }else{
            return false;
        }

    }

    public static function getRoleId($rolename){
        $role = Role::where('rolename',$rolename)->first();
        if($role){
            return $role->_id;
        }else{
            return false;
        }
    }

    public static function getShopCategory(){
        $c = Shopcategory::get();
        self::$shopcategory = $c;
        return new self;
    }

    public static function getCategory(){
        $c = Category::get();
        self::$category = $c;
        return new self;
    }

    public static function getSection(){
        $s = Section::get();

        self::$section = $s;
        return new self;
    }

    public function sectionToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$section as $s) {
            $ret[$s->{$value}] = $s->{$label};
        }

        return $ret;
    }


    public function catToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$category as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function ShopCatToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$shopcategory as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function sectionToArray()
    {
        return self::$section;
    }

    public function catToArray()
    {
        return self::$category;
    }

    public function shopcatToArray()
    {
        return self::$shopcategory;
    }

    public static function getRole(){
        $c = Role::get();

        self::$role = $c;
        return new self;
    }

    public function RoleToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Role');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function RoleToArray()
    {
        return self::$role;
    }

//company
    public static function getCompany(){
        $c = Company::get();

        self::$role = $c;
        return new self;
    }

    public function CompanyToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Company');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$value}.' - '.$c->{$label};
        }


        return $ret;
    }

    public function CompanyToArray()
    {
        return self::$role;
    }

//company
    public static function getCoa(){
        $c = Coa::get();

        self::$role = $c;
        return new self;
    }

    public function CoaToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Coa');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function CoaToArray()
    {
        return self::$role;
    }


    public static function yearSelection(){
        $ya = array();
        for( $i = 1970; $i < 2050; $i++ ){
            $ya[$i] = $i;
        }
        return $ya;
    }

    public static function GetBatchId($SKU, $year, $month){

        $seq = DB::collection('batchnumbers')->raw();

        $new_id = $seq->findAndModify(
                array(
                    'SKU'=>$SKU,
                    'year'=>$year,
                    'month'=>$month
                    ),
                array('$inc'=>array('sequence'=>1)),
                null,
                array(
                    'new' => true,
                    'upsert'=>true
                )
            );


        $batchid = $year.$month.str_pad($new_id['sequence'], 4, '0', STR_PAD_LEFT);

        return $batchid;

    }

    public static function ExtractProductCategory($selection = true)
    {
        $category = Product::distinct('category')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractPages($selection = true)
    {
        $category = Viewlog::distinct('pageUri')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractHotspot($selection = true)
    {
        $category = Viewlog::distinct('spot')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractAdAsset($merchant_id,$selection = true)
    {
        $category = Asset::where('merchantId', $merchant_id )->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        if(count($category) > 0){
            foreach($category as $cat){
                $cats[$cat['_id']] = $cat['itemDescription'];
            }
        }

        return $cats;
    }

    public static function themeAssetsUrl()
    {
        return URL::to('/').'/'.Theme::getCurrentTheme();
    }

    public static function themeAssetsPath()
    {
        return 'themes/'.Theme::getCurrentTheme().'/assets/';
    }

    public static function getActiveTheme()
    {
        return Config::get('kickstart.default_theme');
    }

    public static function getPrintDefault($type = 'asset'){
        $printdef = Printdefault::where('ownerId',Auth::user()->_id)
                        ->where('type',$type)
                        ->first();
        if($printdef){
            return $printdef;
        }else{
            $d = new stdClass();
            $d->col = 2;
            $d->res = 150;
            $d->cell_width = 250;
            $d->cell_height = 300;
            $d->margin_right = 8;
            $d->margin_bottom = 10;
            $d->font_size = 8;
            $d->code_type = 'qr';

            return $d;
        }
    }

    public static function colorizestatus($status, $prefix = '', $suffix = ''){

        $colors = Config::get('jayon.status_colors');
        if($status == '' || !in_array($status, array_keys($colors))){
            $class = 'brown';
            $status = 'N/A';
        }else{
            $class = $colors[$status];
        }

        $atatus = str_replace('_', ' ', $status);
        $status = $prefix.ucwords($status).$suffix;

        return sprintf('<span class="%s">%s</span>',$class,$status);
    }

    public static function colorizetype($type, $prefix = '', $suffix = ''){

        if($type == 'COD'){
            $class = 'brown';
        }else if($type == 'CCOD'){
            $class = 'maroon';
        }else if($type == 'PS'){
            $class = 'green';
        }else{
            $class = 'red';
            $type = 'DO';
        }

        $type = $prefix.$type.$suffix;

        return sprintf('<span class="%s" style="text-align:center;">%s</span>',$class,$type);
    }


}
