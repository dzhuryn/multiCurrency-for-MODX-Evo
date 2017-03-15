name:order.prepareProcess
description:order.prepareProcess
======
<?php
if(is_object($FormLister) &&$FormLister->validateForm()){
    $fields = array();
    foreach ($_POST as $key => $val){
        $fields[$key]=$val;
    }
    sendOrderToManager($fields);


}
?>
