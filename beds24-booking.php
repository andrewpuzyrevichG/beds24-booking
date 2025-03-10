<?php

/**

 * Plugin Name: Booking by Beds24 API

 * Description: Plugin for booking system beds24

 * Version: 0.0.1

 */

define('BEDS_DIR', __DIR__);

define("BEDS_URL", plugins_url().'/beds24-booking/');

//region Debug
if(!function_exists('print_array')) {
    function print_array($arr = [])
    {
        printf('<pre>%s</pre>', print_r($arr, true));
    }
}
if(!function_exists('dd')){
    function dd($arr = []){
        print_array($arr);
        die();
    }
}
//endregion Debug

if(!function_exists('get_months_by_period')){
    function get_months_by_period(){
        return [
            'winter' => [
                1 => __('Januari'),
                2 => __('Februari'),
                3 => __('Mars'),
                4 => __('April'),
                5 => __('Maj'),

                11 => __('November'),
                12 => __('December'),
            ],
            'summer' => [
                6 => __('Juni'),
                7 => __('Juli'),
                8 => __('Augusti'),
                9 => __('September'),
                10 => __('Oktober'),
            ]
        ];
    }
}

require_once (BEDS_DIR.'/includes/loader_new.php');
// require_once (BEDS_DIR.'/includes/loader.php');
