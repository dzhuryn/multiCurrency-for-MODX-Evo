<?php
include_once(MODX_BASE_PATH . 'assets/lib/MODxAPI/modResource.php');

if (empty($mainCurrency) || empty($currencies) || empty($docId)|| empty($prefix)) {
    return '';
}
$mainCurrency = isset($mainCurrency) ? $mainCurrency : '';
$currencies = isset($currencies) ? $currencies : '';
$currencyArray = explode(',',$currencies);
$currencySmallArray = array();
foreach ($currencyArray as $item) {
    $currencySmallArray[]=strtolower($item);
}
$currencySmallArray[]=strtolower($mainCurrency);
$docId = isset($docId) ? $docId : '';
$prefix = isset($prefix) ? $prefix : '';

$event = $modx->event->name;
switch ($event){
    case 'OnWebPageInit':

        $date = $modx->runSnippet('DocInfo', array('docid' => $docId, 'field' => 'multiCurrencyDate'));
        $thisDate = date('d-m-Y');
        if ($date == $thisDate) {
            return '';
        }


        $currencyArray = explode(',', $currencies);
        $searchString = [];
        if (is_array($currencyArray)) {
            foreach ($currencyArray as $item) {
                $searchString[] =  $mainCurrency.$item;
            }
        }
        $searchString = implode(',', $searchString);
        $url = 'https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+%22' . $searchString . '%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);

        $data = json_decode($data, true);
        $values = array();
        if (is_array($data['query']['results']['rate'])) {
            foreach ($data['query']['results']['rate'] as $result) {
                $rate = $result['Rate'];
                $name = strtolower(explode('/',$result['Name'])[0]);
                $values[$name]=$rate;
            }
        }

        if(!empty($values)){
            $res = new modResource($modx);
            $res->edit($docId);

            foreach ($values as $key=> $value){
                $res->set($prefix.$key, $value);
            }
            $res->set('multiCurrencyDate', $thisDate);
            $res->save(false,false);
            //echo 'Обновлено';
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
                die();
        }


        break;
    case 'OnSHKcalcTotalPrice':
        $totalPrice= isset($totalPrice)?$totalPrice:1;
        $newPrice = $modx->runSnippet('multiCurrency',array('formatted'=>'1','type'=>'calcAll'));

        $e->output($newPrice);


        break;
}



