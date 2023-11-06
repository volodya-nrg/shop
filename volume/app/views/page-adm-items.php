<div class="main">
    <div class="main_column">
        <div class="tabs">
            <div class="tabs_host-items">
                <a class="tabs_host-item sx-active" href="/adm/items">
                    Items
                </a>
                <a class="tabs_host-item" href="/adm/cats">
                    Cats
                </a>
                <a class="tabs_host-item" href="/adm/infos">
                    Infos
                </a>
            </div>
            <div class="tabs_contents">
                <?php
                echo template(EnumViewFile::ModuleAdmList, []);
                ?>
                <br/>
                <?php
                echo template(EnumViewFile::ModulePaginator, [
                    EnumField::Path->value => "",
                    EnumField::From->value => 0,
                ]);
                ?>
            </div>
        </div>
    </div>
</div>