<?php declare(strict_types=1);
$err = $__err ?? "";
$tabs = $__data[EnumField::Tabs->value] ?? [];
$item = $__data[EnumField::Item->value] ?? new CatRow();
$catsTreeAsList = $__data[EnumField::CatsTreeAsList->value] ?? [];
?>
<div class="main">
    <div class="main_column">
        <div>
            <a href="/adm/cats"><?php echo EnumDic::BackWithPrefix->value ?></a>
        </div>
        <br/>
        <?php if ($err): ?>
            <div>
                <?php
                echo template(EnumViewFile::ModuleNotice, "", [
                    EnumField::Msg->value => $err,
                    EnumField::Type->value => EnumNoticeStyleClass::Danger->value,
                ]);
                ?>
            </div>
            <br/>
        <?php endif; ?>
        <form class="form" method="post">
            <div class="form_row">
                <input type="hidden" name="<?php echo EnumField::CatId->value ?>" value="<?php echo $item->cat_id ?>"/>

                <div class="form_title"><?php echo EnumDic::Name->value ?></div>
                <input type="text" name="<?php echo EnumField::Name->value ?>" value="<?php echo $item->name ?>"/>
            </div>
            <div class="form_row">
                <div class="form_title"><?php echo EnumDic::Parent->value ?></div>
                <select name="<?php echo EnumField::ParentId->value ?>">
                    <option value="0"></option>
                    <?php foreach ($catsTreeAsList as $catsTreeAsListItem): ?>
                        <option value="<?php echo $catsTreeAsListItem->catRow->cat_id ?>"
                            <?php echo $item->parent_id === $catsTreeAsListItem->catRow->cat_id ? 'selected="selected"' : '' ?>
                            <?php echo $item->cat_id === $catsTreeAsListItem->catRow->cat_id ? 'disabled="disabled"' : '' ?>
                        >
                            <?php echo str_repeat(EnumDic::CatsTreeAsListPrefix->value, $catsTreeAsListItem->deep) ?>
                            <?php echo $catsTreeAsListItem->catRow->name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_row">
                <div class="form_title"><?php echo EnumDic::Position->value ?></div>
                <input type="number" name="<?php echo EnumField::Pos->value ?>" value="<?php echo $item->pos ?>"/>
            </div>
            <div class="form_row">
                <label>
                    <input type="checkbox" name="<?php echo EnumField::IsDisabled->value ?>"
                        <?php echo $item->is_disabled ? ' checked="checked"' : "" ?> />
                    <?php echo EnumDic::IfChooseThenDisabled->value ?>
                </label>
            </div>
            <div class="form_row sx-submit">
                <input type="submit" value="<?php echo EnumDic::Send->value ?>"/>
                <input type="submit"
                       form="formForDelete"
                       class="float-right"
                       name="<?php echo EnumField::ActionDelete->value ?>"
                       value="<?php echo EnumDic::Delete->value ?>"
                    <?php echo $item->cat_id ? '' : 'disabled="disabled"' ?>
                />
            </div>
        </form>
        <form id="formForDelete" method="post"
              onsubmit="return confirm('<?php echo EnumDic::AreYouSureYouWantToDelete->value ?>')">
            <input type="hidden" name="<?php echo EnumField::CatId->value ?>" value="<?php echo $item->cat_id ?>"/>
        </form>
    </div>
</div>