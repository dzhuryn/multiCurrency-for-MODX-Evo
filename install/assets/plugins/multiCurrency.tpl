//<?php
/**
 * multiCurreny
 *
 * Обновление валют каждый день 
 *
 * @category    plugin
 * @version     1.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @package     modx
 * @author      dzhuryn
 * @internal    @properties &mainCurrency=Главная валюта;string;RUB &currencies=Валюты;string;USD,EUR,GBP &docId=Ресурс где хранаться tv параметры;string;1 &prefix=Префикс тв параметров;string;course_
 * @internal    @events OnPageInit
 * @internal    @modx_category multiCurreny
 * @internal    @installset base, sample
 */

require_once MODX_BASE_PATH.'assets/snippets/multiCurrency/plugin.multiCurrency.php';
