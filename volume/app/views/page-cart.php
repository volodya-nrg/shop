<div class="page-cart">
    <div class="page-cart__main">
        <h1>Корзина</h1>
        <div class="page-cart__items">
            <?php
            for ($i=0; $i < 6; $i++) {
                echo template(DIR_VIEWS . "/" . ViewModuleCartItem, []);
            }
            ?>
        </div>
    </div>
    <div class="page-cart__checkout">
        <div>
            Всего товаров <span class="h3">20</span> шт. Общая цена: <span class="h3">20 000</span> ₽
        </div>
        <a href="/order">Оформить</a>
    </div>
</div>