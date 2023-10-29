<?php

final class RequestCat
{
    public int $catId = 0;
    public string $name = "";
    public int $parentId = 0;
    public int $pos = 0;
    public bool $isDisabled = false;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldCatId])) {
            $this->catId = $post[FieldCatId];
        }
        if (isset($post[FieldName])) {
            $this->name = $post[FieldName];
        }
        if (isset($post[FieldParentId])) {
            $this->parentId = $post[FieldParentId];
        }
        if (isset($post[FieldPos])) {
            $this->pos = $post[FieldPos];
        }
        if (isset($post[FieldIsDisabled])) {
            $this->isDisabled = $post[FieldIsDisabled];
        }
    }

    public function toArray(): array
    {
        return [
            FieldCatId => $this->catId,
            FieldName => $this->name,
            FieldParentId => $this->parentId,
            FieldPos => $this->pos,
            FieldIsDisabled => $this->isDisabled,
        ];
    }
}