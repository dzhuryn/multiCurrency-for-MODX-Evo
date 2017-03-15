name:order.prepare
description:order.prepare
======
<?php
$_SESSION['save_purchases'] = $_SESSION['purchases'];
populateOrderData($fields);
$fields['order.subject'] = $modx->getConfig('__order.subject');
//$fields['order.subject.lang.more'] = $modx->getConfig('__order.subject.more');
//order.subject.more
$modx->setPlaceholder('orderID',$fields['orderID']);
$modx->setPlaceholder('orderId',$fields['orderId']);

foreach ($fields as $key => $field){
    $FormLister->setField($key,$field);
}
?>
