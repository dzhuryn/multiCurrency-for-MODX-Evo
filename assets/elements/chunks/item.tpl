name:item
description:item
======
<div class="shk-item">
    <p>[+pagetitle+]</p>
    <p>[[multiCurrency? &type=`calc` &price=`[+price+]` &formatted=`1`]]</p>

    <div class="shs-tocart shk-item">
        <form action="[~[*id*]~]" method="post">
            <input type="hidden" name="shk-id" value="[+id+]" />
            <input type="hidden" name="shk-count" value="1" size="2" maxlength="3" />
            <div align="right">
                Цена: <span class="shk-price">[[multiCurrency? &type=`calc` &price=`[+price+]` &formatted=`1`]]</span>
                <button type="submit" name="shk-submit" class="shk-but">В корзину</button>
            </div>
        </form>
    </div>
</div>