<?php
include_once(MODX_BASE_PATH . 'assets/lib/MODxAPI/modResource.php');

if (empty($mainCurrency) || empty($currencies) || empty($docId)|| empty($prefix) || empty($auto) || empty($provider)) {
    return '';
}
$mainCurrency = isset($mainCurrency) ? $mainCurrency : '';
$provider = isset($provider) ? $provider : '';
$auto = isset($auto) ? $auto : '';
$currencies = isset($currencies) ? $currencies : '';
$currencyArray = explode(',',$currencies);
$currencySmallArray = array();
foreach ($currencyArray as $item) {
    $currencySmallArray[]=strtolower($item);
}
$currencySmallArray[]=strtolower($mainCurrency);
$docId = isset($docId) ? $docId : '';
$prefix = isset($prefix) ? $prefix : '';
$e = $modx->event;
$event = $e->name;




switch ($event){
    case 'OnWebPageInit':
        if($auto!=1){
            return ;
        }
        $providerFile = __DIR__.'/providers/'.$provider.'.php';
        if(!file_exists($providerFile)){
            return ;
        }
        $date = $modx->runSnippet('DocInfo', array('docid' => $docId, 'field' => 'multiCurrencyDate'));
        $thisDate = date('d-m-Y');
        if ($date == $thisDate ) {
            return '';
        }
        require_once $providerFile;

        var_dump($values);
        if(!empty($values) && is_array($values)){
            $res = new modResource($modx);
            $res->edit($docId);
            foreach ($values as $key=> $value){
                $res->set($prefix.$key, $value);
            }
            $res->set('multiCurrencyDate', $thisDate);
            $res->save(false,false);
        }
        break;
    case 'OnPageNotFound':
        $q = $_REQUEST['q'];
        switch ($q){
            case 'ajax-set-currency':
                $data = $_GET['data'];
                if(in_array($data,$currencySmallArray)){
                    $_SESSION['currency']=$data;
                }
                ///обновляем цену в товарах которие в сесии
                $items = unserialize($_SESSION['purchases']);
                if(is_array($items)){
                    foreach ($items as $key=> $item){
                        $id = $item[0];
                        $price = $modx->runSnippet('DocInfo',['field'=>'price','docid'=>$id]);

                        $items[$key][2] = $modx->runSnippet('multiCurrency',array('price'=>$price,'type'=>'calc'));
                    }
                    $_SESSION['purchases'] = serialize($items);
                }
                die();
        }
        break;

    case 'OnSHKgetProductPrice':
        $newPrice = $modx->runSnippet('multiCurrency',array('price'=>$price,'type'=>'calc'));
        $e->output($newPrice);

        break;
}



