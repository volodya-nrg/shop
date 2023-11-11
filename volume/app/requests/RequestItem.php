<?php declare(strict_types=1);

final class RequestItem
{
    public int $itemId = 0;
    public string $title = "";
    public int $catId = 0;
    public ?string $description = null;
    public int $price = 0;
    public bool $isDisabled = false;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (count($post)) {
            if (isset($post[EnumField::ItemId->value])) {
                $this->itemId = $post[EnumField::ItemId->value];
            }
            if (isset($post[EnumField::Title->value])) {
                $this->title = $post[EnumField::Title->value];
            }
            if (isset($post[EnumField::CatId->value])) {
                $this->catId = $post[EnumField::CatId->value];
            }
            if (isset($post[EnumField::Description->value])) {
                $this->description = $post[EnumField::Description->value];
            }
            if (isset($post[EnumField::Price->value])) {
                $this->price = $post[EnumField::Price->value];
            }
            if (isset($post[EnumField::IsDisabled->value])) {
                $this->isDisabled = $post[EnumField::IsDisabled->value] === "on";
            }
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::ItemId->value => $this->itemId,
            EnumField::Title->value => $this->title,
            EnumField::CatId->value => $this->catId,
            EnumField::Description->value => $this->description,
            EnumField::Price->value => $this->price,
            EnumField::IsDisabled->value => $this->isDisabled ? "on": "",
        ];
    }
}