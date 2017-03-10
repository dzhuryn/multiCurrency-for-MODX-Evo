<?php

if (empty($mainCurrency) || empty($currencies) || empty($docId)|| empty($prefix)) {
    return '';
}

$settings = array(
    'usd'=>array('name'=>'USD','sign'=>'$'),
    'eur'=>array('name'=>'EUR','sign'=>'€'),
    'rub'=>array('name'=>'RUB','sign'=>'&#8381;'),
    'gbp'=>array('name'=>'GBP','sign'=>'£'),
);

$mainCurrency = isset($mainCurrency) ? strtolower($mainCurrency) : '';
$currencies = isset($currencies) ? $currencies : '';
$docId = isset($docId) ? $docId : '';
$prefix = isset($prefix) ? $prefix : '';

$formatted = isset($formatted)?$formatted:0;
$active = isset($_SESSION['currency'])?$_SESSION['currency']:strtolower($mainCurrency);
$currencyArray = explode(',',$currencies);

if(!function_exists('price_formatted')){
    function price_formatted($price,$currency){
        global $settings;
        return $settings[$currency]['sign'].''.number_format(round($price), 0, ',', ' ');
    }
}
$type = isset($type)?$type:'list';
switch ($type){
    case 'currentCurrency':
        $toPlaceholder = isset($toPlaceholder)?$toPlaceholder:0;
        $active = isset($_SESSION['currency'])?$_SESSION['currency']:strtolower($mainCurrency);
        $tpl = isset($rowTpl)?$rowTpl:'[+sign+]';
        $setting=$settings[$active];

        $item = str_replace(array(
            '[+name+]','[+sign+]'
        ),array(
            $setting['name'],$setting['sign']
        ),$tpl);
        if($toPlaceholder){
            $modx->setPlaceholder('currentCurrency',$item);
        }
        else{

            echo $item;
        }
        break;
    case 'list':
        if(empty($GLOBALS['currency'])){
            $modx->regClientScript('/assets/snippets/multiCurrency/multiCurrency.js');
            $GLOBALS['currency']=1;
        }
        $active = isset($_SESSION['currency'])?$_SESSION['currency']:strtolower($mainCurrency);
        $toPlaceholder = isset($toPlaceholder)?$toPlaceholder:0;
        $rowClass = isset($rowClass)?$rowClass:'item';
        $activeClass = isset($activeClass)?$activeClass:'active';
        $wrapper = isset($wrapper)?$wrapper:'<ul>[+wrapper+]</ul>';
        $rowTpl = isset($rowTpl)?$rowTpl:'<li[+class+] [+currencyKey+]>[+name+]</li>';
        $itemStr = '';
        foreach ($currencyArray as $item) {
            $el = strtolower($item);
            $classes = ['set-currency'];
            if(!empty($rowClass))$classes[]=$rowClass;
            if($active==$el){
                $classes[]=$activeClass;
            }
            $class = ' class="'.implode(' ',$classes).'" ';

            $key = 'data-key="'.$el.'"';
            $itemStr .= str_replace(array(
                '[+class+]','[+name+]','[+sign+]','[+currencyKey+]'
            ),array(
                $class,$settings[$el]['name'],$settings[$el]['sign'],$key
            ),$rowTpl);
        }
        $output = str_replace('[+wrapper+]',$itemStr,$wrapper);
        if($toPlaceholder){
            $modx->setPlaceholder('multiCurrency',$output);
        }
        else{
            echo $output;
        }
        break;
    case 'calc':
        if(empty($GLOBALS['course'])){
            $course = $modx->runSnippet('DocInfo',array('docid'=>$docId,'field'=>'course_'.$active));
        }
        else{
            $course = $GLOBALS['course'];
        }
        $price = isset($price)?$price:0;
        $active = isset($_SESSION['currency'])?$_SESSION['currency']:strtolower($mainCurrency);
        if($active==$mainCurrency){
            $newPrice = $price;
        }
        else{
            $newPrice = intval($price)/intval($course);
        }
        if($formatted){
            echo price_formatted($newPrice,$active);
        }
        else{
            echo $newPrice;
        }
        break;
    case 'calcOne':
        $count = isset($count) ? intval($count) : 1;
        $price = isset($price) ? intval(str_replace(' ','',$price)) : 0;
        $multiPrice = $modx->runSnippet('multiCurrency', array(
            'type' => 'calc',
            'price' => $price,
            'formatted' => 0
        ));
        if($formatted){
            echo price_formatted($multiPrice,$active);
        }
        else{
            echo $multiPrice;
        }
        break;
    case 'calcAll':
        $purchases = $_SESSION['purchases'];
        $purchases = unserialize($purchases);
        $allPrice = 0;
        if (!empty($purchases) && is_array($purchases)) {
            foreach ($purchases as $purchase) {
                $price = $purchase[2];
                $count = $purchase[1];

                $itemPrice = $modx->runSnippet('multiCurrency', array(
                        'type' => 'calc',
                        'formatted' => 0,
                        'price' => $price
                    )
                );
                $itemPrice = str_replace(',', '.', $itemPrice);
                $itemsPrice = floatval($itemPrice) * $count;
                $allPrice = $allPrice + $itemsPrice;
            }
        }
        if($formatted){
            echo price_formatted($allPrice,$active);
        }
        else{
            echo $allPrice;
        }
        break;
}