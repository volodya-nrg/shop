<div class="main">
    <div class="main_column">
        <div>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem
            voluptates.
        </div>
        <div>
            <h1>Корзина</h1>
            <div class="page-cart_items">
                <?php
                for ($i=0; $i < 6; $i++) {
                    echo template(EnumViewFile::ModuleCartItem);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="main_column sx-side">
        <div>
            Всего товаров <span class="h3">20</span> шт. Общая цена: <span class="h3">20 000</span> ₽
        </div>
        <a href="/order">Оформить</a>
    </div>
</div>