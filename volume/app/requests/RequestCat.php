<?php declare(strict_types=1);

final class RequestCat
{
    public int $catId = 0;
    public string $name = "";
    public int $parentId = 0;
    public int $pos = 0;
    public bool $isDisabled = false;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::CatId->value])) {
            $this->catId = $post[EnumField::CatId->value];
        }
        if (isset($post[EnumField::Name->value])) {
            $this->name = $post[EnumField::Name->value];
        }
        if (isset($post[EnumField::ParentId->value])) {
            $this->parentId = $post[EnumField::ParentId->value];
        }
        if (isset($post[EnumField::Pos->value])) {
            $this->pos = $post[EnumField::Pos->value];
        }
        if (isset($post[EnumField::IsDisabled->value])) {
            $this->isDisabled = $post[EnumField::IsDisabled->value] === "on";
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::CatId->value => $this->catId,
            EnumField::Name->value => $this->name,
            EnumField::ParentId->value => $this->parentId,
            EnumField::Pos->value => $this->pos,
            EnumField::IsDisabled->value => $this->isDisabled ? "on": "",
        ];
    }
}