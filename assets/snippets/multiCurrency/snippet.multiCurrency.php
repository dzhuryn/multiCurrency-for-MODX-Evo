<?php

if (empty($mainCurrency) || empty($currencies) || empty($docId)|| empty($prefix)) {
    return '';
}
global $settings;
$settings = array(
    'usd'=>array('name'=>'USD','sign'=>'$'),
    'eur'=>array('name'=>'EUR','sign'=>'€'),
    'rub'=>array('name'=>'RUB','sign'=>' р'),
    'gbp'=>array('name'=>'GBP','sign'=>'£'),
    'uah'=>array('name'=>'UAH','sign'=>'uah '),
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
        if($currency=='rub'){
            return number_format($price, 0, ',', ' ').$settings[$currency]['sign'];
        }
        else{
            return $settings[$currency]['sign'].''.number_format($price, 0, ',', ' ');
        }

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
        $js = isset($js)?$js:1;
        if(empty($GLOBALS['currency']) && $js==1){
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
            $newPrice = intval($price)* $course;
        }
        $newPrice = round($newPrice);
        if($formatted){
            echo price_formatted($newPrice,$active);
        }
        else{
            echo $newPrice;
        }
        break;

}
