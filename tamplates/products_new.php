<?php
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

$current_page = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

if(isset($_GET['npage'])){
    $current_page = $_GET['npage'];
    update_option('current_page', $_GET['npage']);
}else{
    $current_page = 1;
}
$per_page = 10; // Кількість об'єктів на сторінку

if (isset($meta_arr)){
    $loop_filter = new WP_Query(array(
        'post_type' => 'product',
        'product_tag' => $area,
        'orderby' => 'post__in',
        'order' => 'DESC',
        'meta_query' => $meta_arr,
        'nopaging' => true
    ));
}
$f_array = [];
//echo '<pre>';
//var_dump($adult);
//var_dump($child);

if ($loop_filter->have_posts()){
while ($loop_filter->have_posts()) {
    $loop_filter->the_post();

    $people = 0;
    $c = get_post_meta(get_the_id(),'_children',true);
    if (!is_numeric($c)){
        $c = 0;
    }
    $a = get_post_meta(get_the_ID(),'_product_peoples',true);
    if (!is_numeric($a)){
        $a = 0;
    }
//    var_dump($a);
//    var_dump($c);
    if (isset($child)){

        // c = 4, a = 5
        
        if ($child > $maxChild){  // 4>2
            $adult = $adult + ($child-$maxChild); // 5 + (4-2) = 7
            $child = $child-$maxChild; //4-2 = 2
        }
//        var_dump($adult);
//        var_dump($child);
        if ($c>=$child && $a >= $adult){
            $people++;
        } elseif ( $a>= $adult + $child ){
            $people++;
        }
//        var_dump($people);
    } elseif ($adult > 0){
//        var_dump($adult);
//        var_dump($child);
        if ($a >= $adult){
            $people++;
        }
//        var_dump($people);
    }
//    echo 'all ='.$people;
    if ($people >0){
        array_push($f_array,get_the_id());
    }
    }
}

//var_dump($f_array);
//echo '</pre>';

$available_ids = $product_ids; // Ваша функція для отримання доступних
$unavailable_ids = $product_ids_no_avaliable; // Ваша функція для отримання недоступних

if (!empty($f_array)){
    $combined_ids = array_merge($available_ids, $unavailable_ids);
    $combined_ids = array_intersect($combined_ids,$f_array);
    $total_items = count($combined_ids);
    $total_pages = ceil($total_items / $per_page);
} else {
    $combined_ids = array_merge($available_ids, $unavailable_ids);
    $total_items = count($combined_ids);
    $total_pages = ceil($total_items / $per_page);
}


//var_dump($combined_ids);

$offset = ($current_page - 1) * $per_page;
$page_ids = array_slice($combined_ids, $offset, $per_page);
$loop = new WP_Query(array(
    'post_type' => 'product',
    'product_tag' => $area,
    'post__in' => $page_ids,
//    'posts_per_page' => $per_page,
    'orderby' => 'post__in',
    'order' => 'DESC',
    'meta_query' => $meta_arr,
//    'paged' => $current_page,
    'nopaging' => true
));
//var_dump($loop->posts);
// Виведення результатів
if ($loop->have_posts()) {
    while ($loop->have_posts()) {
        $loop->the_post();

        $permalink = get_the_permalink().'?'.$get_parrs;
        $post_id = get_the_id();
        $product = wc_get_product( $post_id );
        $regular_price = floatval($product->get_regular_price());
        $current_tags = get_the_terms( $post_id, 'product_tag' );
        $tags_name_arr = [];
        if(is_array($current_tags)){
            foreach($current_tags as $tag){
                array_push($tags_name_arr,$tag->name);
            }
        }

        $breadcrumbs = implode(" / ", $tags_name_arr);
        $child = get_post_meta($post_id,'_children', true);
        $hundtillatet = get_post_meta($post_id,'_product_hundtillåtet', true);
        $wi_fi = get_post_meta($post_id,'_product_wi_fi', true);
        $bastu = get_post_meta($post_id,'_product_bastu', true);
        $oppen_spis = get_post_meta($post_id,'_product_oppen_spis', true);
        $skidförråd = get_post_meta($post_id,'_product_skidförråd', true);
        $diskmaskin = get_post_meta($post_id,'_product_diskmaskin', true);
        $twatt = get_post_meta($post_id,'_product_tvättmaskin', true);
        $tork = get_post_meta($post_id,'_product_torkskåp', true);
        $barnsang = get_post_meta($post_id,'_product_barnsäng', true);
        $barnstol = get_post_meta($post_id,'_product_barnstol', true);
        $sovrum = get_post_meta($post_id,'_product_sovrum', true);
        $_product_boyta = get_post_meta($post_id,'_product_boyta',true);
        $product_details = $product->get_data();
        $product_short_description = $product_details['short_description'];
        $price_by_period = $act->getRoomPriceByDays($days,$date_start, $date_end, $post_id);
        $peoples = intval(get_post_meta($post_id,'_product_peoples', true));
        $adult = intval($adult);

        $roomID = get_post_meta($post_id,'_product_beds_id',true);
//        $avail = $wpdb->get_var("select `isBooked` from `beds_calendar` where `roomId` = '$roomID' and `date` = '$date_start'");
//        $availEnd = $wpdb->get_var("select `isBooked` from `beds_calendar` where `roomId` = '$roomID' and `date` = '$date_end'");
        $origin = date_create($date_start);
        $target = date_create($date_end);
        $interval = date_diff($origin, $target);
        $dateCount = $interval->format('%a');

        if($adult<=$peoples){
            $picture = get_the_post_thumbnail_url($post_id,'middle');

            if($picture == false){
                $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $base_url = $scheme . '://' . $host;
                $picture = $base_url.'/wp-content/uploads/2023/08/20230401_124150932_iOS-1.jpg';
            }
        }
//        var_dump("select `isBooked` from `beds_calendar` where `roomId` = '$roomID' and `date` = '$date_start'");
//        var_dump("select `isBooked` from `beds_calendar` where `roomId` = '$roomID' and `date` = '$date_end'");
//        var_dump($avail);
//        var_dump($availEnd);
if(in_array($post_id,$unavailable_ids)){ ?>
<div style="opacity: 0.7;" class="searh-item-wrap">
    <?php }else{ ?>
    <div  class="searh-item-wrap" >
        <?php } ?>
        <div class="search-item-image" style="background: url('<?php echo $picture; ?>')" >
            <div style="width: 100%; height: 100%; cursor: pointer;" onclick="window.open('<?php echo $permalink; ?>');"></div>
            <!--<a href="#" onclick="window.open('<?php /*echo $permalink; */?>');"></a>-->
<!--            <img src="--><?php //echo $picture; ?><!--" alt="">-->
            <label  class="add-to-favorites" data-id="<?php echo $post_id;?>" style="z-index: 999">
                <?php
                if (isset($_SESSION['wishlist'])){
                    $list = explode(',',$_SESSION['wishlist']);
                    $disp = [0=>'block',1=>'none'];
                    if (in_array($post_id,$list)){
                        $disp[0] = 'none';
                        $disp[1] = 'block';
                    }
                } else {
                    $disp = [0=>'block',1=>'none'];
                }
                ?>
                <svg data-id="<?php echo $post_id;?>-b" style="display: <?= $disp[0];?>" xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512">
                    <path fill="red" d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>                    </svg>
                <svg data-id="<?php echo $post_id;?>-r" style="display: <?= $disp[1];?>" xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512">
                    <path fill="red" d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/>
                </svg>
            </label>
        </div>
        <?php
        $s = generateRandomString();
        $args_rating = array(
            'number'  => -1,
            'post_id'=>$post_id,
        );

        $reviewCount = 0;
        $repeater_field = get_field('acomodation_reviews',$post_id);
        if ($repeater_field) {
            $reviewCount = count($repeater_field);
        }
        $average = 0;
        $ratingAll = 0;
        while (have_rows('acomodation_reviews',$post_id)):
            the_row();
            $rating = get_sub_field('rating_from_0_to_5');
            if (!empty($rating) and $rating != 0){
                $ratingAll += (int)$rating;
            }
        endwhile;
        if ($ratingAll != 0){
            $average = $ratingAll / $reviewCount;
        }
        ?>

        <style>
            .on-mobile-show{
                display: none;
            }
            @media all and (max-width: 500px)  {
                .mob-hide{
                    display: none;
                }
                .on-mobile-show{
                    display: block;
                    text-decoration: none !important;
                }
                .mob-flex{
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    width: 100%;
                    padding-right: 4px;
                    margin-bottom: 15px;
                }
                .period{
                    margin-bottom: 0;
                }

            }
        </style>
        <div class="search-item-content">

            <div style="display: flex;justify-content: space-between;">
                <p class="search-item-subtitle"><?php echo $breadcrumbs; ?><span> - <?php echo get_post_meta($post_id,'_product_breadcrumbs',true);?></span></p>

                <div class="search-item-reviews">
                    <div><a class="on-mobile-show" href="<?php echo $permalink; ?>"><i class="fas fa-star"></i><span> <?php if ($average == 0){echo $average;} else {echo sprintf("%.2f", $average);}?></span></a></div>
                </div>
            </div>
            <a href="#" class="search-item-title">
                <h4 onclick="window.open('<?php echo $permalink; ?>');"><?php the_title(); ?></h4>
            </a>
            <ul class="search-item-icons">
                <li class="icon-gray"><span><?php echo $peoples.'&nbsp;';
                        if (!empty($child)){echo '(+'.$child.')&nbsp;';}?></span><i class="fas fa-user-friends"></i></li>
                <li class="icon-gray"><span><?php echo $sovrum; ?></span> <img style="vertical-align: bottom;height:19px;" src="<?php echo BEDS_URL;?>assets/svg/hotel-bed.svg">

                </li>
                <li class="icon-gray"><span><?php echo $_product_boyta; ?> m<sup>2</sup></span></li>
                <?php

                if($hundtillatet){
                    echo '<li class="icon-red"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.1862 12.2328L15.1834 12.2403H10.9831C10.3734 12.2403 9.80583 12.4238 9.33241 12.7382H7.99222C7.44296 12.7382 6.99611 12.2915 6.99611 11.7423C6.99611 11.1932 7.44296 10.7464 7.99222 10.7464H9.48638C9.76143 10.7464 9.98443 10.5234 9.98443 10.2484C9.98443 9.97344 9.76143 9.75049 9.48638 9.75049H7.99222C6.89371 9.75049 6 10.644 6 11.7423C6 12.8406 6.89371 13.7341 7.99222 13.7341H8.39448C8.13881 14.1745 7.99222 14.6857 7.99222 15.2306V21.469C7.99222 21.744 8.21521 21.967 8.49027 21.967H10.4825C10.7575 21.967 10.9805 21.744 10.9805 21.469V17.2666L15.5294 17.6751V21.5022C15.5294 21.7772 15.7524 22.0002 16.0275 22.0002H18.0197C18.2948 22.0002 18.5178 21.7772 18.5178 21.5022V16.7712L19.1315 13.7032L15.1862 12.2328Z" fill="#F2A4A9"/>
                        <path d="M22.5016 9.25222H20.9987C20.9179 8.4921 20.2744 7.89114 19.4817 7.89114H18.646L18.3411 6.39727C18.2413 5.90864 17.5605 5.85546 17.3863 6.32311L15.5332 11.2991L19.3287 12.7138L19.4235 12.2399H20.5094C21.8825 12.2399 22.9997 11.123 22.9997 9.75017C22.9997 9.47517 22.7767 9.25222 22.5016 9.25222Z" fill="#F2A4A9"/>
                        </svg>
                        </li>';
                }
                if($wi_fi){
                    echo '<li class="icon-red"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.8749 14.0016C21.7272 14.0016 21.5857 13.9439 21.4813 13.8411C19.6165 12.0052 17.1372 10.9941 14.5 10.9941C11.8628 10.9941 9.38352 12.0052 7.51874 13.8411C7.41435 13.9439 7.27278 14.0017 7.12516 14.0017C6.97754 14.0017 6.83596 13.9439 6.73158 13.8411L5.16304 12.2969C4.94565 12.0829 4.94565 11.7359 5.16304 11.5219C6.42335 10.2811 7.89306 9.32029 9.5314 8.6662C11.1136 8.0345 12.7853 7.71423 14.5 7.71423C16.2147 7.71423 17.8864 8.03454 19.4686 8.6662C21.1069 9.32029 22.5767 10.2811 23.837 11.5219C24.0543 11.7359 24.0543 12.0829 23.837 12.2969L22.2685 13.8411C22.1641 13.9439 22.0225 14.0016 21.8749 14.0016Z" fill="#F2A4A9"/>
                        <path d="M18.6246 17.189C18.4769 17.189 18.3353 17.1312 18.231 17.0284C17.2341 16.047 15.9087 15.5064 14.4988 15.5064C13.089 15.5064 11.7636 16.047 10.7667 17.0284C10.6623 17.1312 10.5208 17.189 10.3731 17.189C10.2255 17.189 10.0839 17.1312 9.97952 17.0284L8.41105 15.4842C8.1937 15.2702 8.1937 14.9232 8.41109 14.7092C10.0372 13.1082 12.1992 12.2266 14.4989 12.2266C16.7985 12.2266 18.9606 13.1082 20.5867 14.7092C20.804 14.9232 20.804 15.2702 20.5867 15.4842L19.0182 17.0284C18.9138 17.1312 18.7722 17.189 18.6246 17.189Z" fill="#F2A4A9"/>
                        <path d="M14.5006 21.2858C13.2283 21.2858 12.1934 20.2769 12.1934 19.0368C12.1934 17.7967 13.2284 16.7878 14.5006 16.7878C15.7728 16.7878 16.8078 17.7967 16.8078 19.0368C16.8078 20.2769 15.7728 21.2858 14.5006 21.2858Z" fill="#F2A4A9"/>
                        </svg>
                        </li>';
                }
                if ($bastu){
                    echo '<li class="icon-red"><img style="vertical-align: inherit;" src="'.BEDS_URL.'assets/img/66.svg"></li>';
                }

                if ($oppen_spis){
                    echo '<li class="icon-red" ><img style="vertical-align: inherit;width:21px;" src="'.BEDS_URL.'assets/img/7.svg"></li>';
                }
                ?>
            </ul>
            <p class="search-item-excerpt">
                <?php echo mb_strimwidth($product_short_description, 0, 200, "..."); ?>
            </p>
        </div>
        <?php
        $reviewCount = 0;
        $repeater_field = get_field('acomodation_reviews',$post_id);
        if ($repeater_field) {
            $reviewCount = count($repeater_field);
        }
        $average = 0;
        $ratingAll = 0;
        while (have_rows('acomodation_reviews',$post_id)):
            the_row();
            $rating = get_sub_field('rating_from_0_to_5');
            if (!empty($rating) and $rating != 0){
                $ratingAll += (int)$rating;
//                            var_dump($rating);
            }
        endwhile;
        if ($ratingAll != 0){
            $average = $ratingAll / $reviewCount;
        }
        ?>
        <div class="search-item-meta" style="position: relative;">
            <div class="search-item-reviews mob-hide">
                <div><i class="fas fa-star"></i><span> <?php if ($average == 0){echo $average;} else {echo sprintf("%.2f", $average);}?></span></div>
                <a class="" href="<?php echo $permalink; ?>"><?php echo $reviewCount;?> <?php _e('omdömen','beds24');?></a>
            </div>
            <div class="mob-flex">
                <div><p class="search-item-price"><?php echo round($price_by_period, -2); ?> SEK</p></div>
                <div><p class="period"><span>Period: <?php echo $period1.' - '. $period2?></span></p></div>
            </div>
            <div class="search-item-buttons">
                <?php if( in_array($post_id,$unavailable_ids)){ ?>
                    <a  class="btn btn-transparent add-to-cart" style="background-color: transparent !important;" disabled="disabled">+ <i class="fas fa-shopping-cart"></i></a>

                    <a style="pointer-events: none;background: grey;" data-product_id="<?php echo $post_id; ?>" data-custom_price="<?php echo round($price_by_period, -2); ?>" class="btn open-cart beds_add_to_cart" href=""><?php _e('inte tillgänglig','beds24');?></a>
                <?php }else{ ?>
                    <a href="#" class="btn btn-transparent add-to-cart" data-s="<?php echo $s;?>" data-product_id="<?php echo $post_id; ?>" data-custom_price="<?php echo round($price_by_period, -2); ?>" data-toggle="modal" data-target="#<?php echo $s;?>">+ <i class="fas fa-shopping-cart"></i></a>

                    <a data-product_id="<?php echo $post_id; ?>" data-custom_price="<?php echo round($price_by_period, -2); ?>" class="beds_add_to_cart btn open-cart" href=""><i class="fas fa-shopping-cart"></i> <?php _e('Boka','beds24');?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="<?php echo $s;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="border: none;">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="width: 100%; text-align: center;"><i class="fas fa-check-circle" style="color:#53B235; margin: 0 auto;"></i></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">

                        <h5 style="margin-bottom: 50px;"><?php _e('Boende lades till i<br> kundvagnen','beds24');?></h5>
                        <div style="width: 100%; display: flex;justify-content: center;">
                            <div style="width: 40%;"><img src="<?php echo $picture; ?>" alt=""></div>
                            <div style="width: 40%;">
                                <p style="font-size: 19px;font-weight: 900;margin-bottom: 0;margin-left: 8px;"><?php the_title(); ?></p>
                                <p style="font-size: 19px; text-align: left;margin-left: 20px;"  class="search-item-price"><?php echo round($price_by_period, -2); ?> SEK</p>
                                <p style="font-size: 14px;margin-left: -6px;" class="period"><span><?php _e('Period:','beds24');?> <?php echo $period1.' - '. $period2?></span></p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer w-100" style="border: none; text-align: center;justify-content: center">
                    <button type="button" class="btn btn-transparent" data-dismiss="modal"><?php _e('Fortsätt Boka','beds24');?></button>
                    <button type="button" class="btn btn-primary" onclick='location = site_url+"/index.php/cart/"'><?php _e('Gå Till Varukorgen','beds24');?></button>
                </div>
            </div>
        </div>
    </div>

<?php }
}

//    if($ajax == false && $per_pages < ($prod /*+ $notAvailProd*/)){
    $i=0;
    ?>
    <div class="pagination_content">
        <div class="pagination">
            <?php
            $get_parrs = [];
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = explode('?', $url);
            $url = $url[0];
            ?><input type="hidden" name="url" value="<?php echo $url; ?>"><?php

            $get["date_start"] = $date_start;
            $get['date_end'] = $date_end;
            foreach($get as $key => $value){
                if($key != 'npage'){
                    array_push($get_parrs,$key.'='.$value);
                }
            }
            $get_parrs = implode('&',$get_parrs);
            $current_page = intval($current_page);
            if($current_page > 1){
                $back = $current_page-1;
                echo '<a href="'.$url.'?'.$get_parrs.'&npage='.$back.'">«</a>';
            }
            while($i < $total_pages){ //
                $i++;
                if($current_page == $i){
                    $class = 'class="active"';
                }else{
                    $class = '';
                }
                echo '<a '.$class.' href="'.$url.'?'.$get_parrs.'&npage='.$i.'">'.$i.'</a>';
            }
            if($current_page < $total_pages){ //$pages
                $next = $current_page+1;
                echo '<a href="'.$url.'?'.$get_parrs.'&npage='.$next.'">»</a>';
            }
            ?>
        </div>
    </div>




        <script>
            $("body").on('click','.add-to-cart', function (e) {
                e.preventDefault();
                let custom_price = $(this).attr('data-custom_price');
                let product_id = $(this).attr('data-product_id');
                let add_button = $(this);
                let date_from = $("#startDateNew").val()
                let date_to = $("#endDateNew").val()
                let persons = $("#adult").val()
                let personsA = $("#num-adult").val()
                let personsC = $("#num-child").val()

                if (!$(this).is('[disabled]')){
                    $.ajax({
                        type: 'POST',
                        url: site_url + '/wp-admin/admin-ajax.php',
                        data: {
                            product_id: product_id,
                            custom_price: custom_price,
                            date_from:date_from,
                            date_to:date_to,
                            personsA:personsA,
                            personsC:personsC,
                            action: 'addtocart'
                        },
                        dataType: "json",
                        cache: false,
                        error: function(error){
                            alert('error');
                            $('.backmodal').remove();

                        },
                        beforeSend: function(){
                            $('body').append('<div class="backmodal"><div></div></div>');
                        },
                        success: function(data){
                            $('.backmodal').remove();
                            // console.log(data[0])
                            if (data[0] === 'limit'){
                                alert("Unfortunately, it is possible to add no more than 3 objects to the basket. To book more objects, contact the administration.")
                            } else {
                                $(add_button).closest('.content_bottom').children('.result').addClass('active');
                                $(add_button).closest('.buy_button').children('.result').addClass('active');
                                // location = site_url+"/index.php/cart/";
                                // $('body').find('header').load(location.href + "* header")
                                $( ".wmc-cart-wrapper" ).load(window.location.href + " .wmc-cart-wrapper > * " );
                            }

                        }
                    });
                }


            })


        </script>
        <?php
        $_av_products_ids = [];

            $map_loop = new WP_Query( array(
                'post_type' => 'product',
                'product_tag' => $area,
                'posts_per_page' => -1,
                'post__in'=> $product_ids,
                'meta_query' => $meta_arr,
                'orderby' => 'post__in',
                'order' => 'DESC',
        //        'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : $current_page
            ));

            while ( $map_loop->have_posts() ): $map_loop->the_post();
                $post_id = get_the_id();
                array_push($_av_products_ids,$post_id);
            endwhile;
        $_av_products_ids_str = implode(",", $_av_products_ids);
        ?>
        <input type="hidden" class="av_products_ids_str" name="" value="<?php echo $_av_products_ids_str ?>">