<?php

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
            if (isset($post[FieldItemId])) {
                $this->itemId = $post[FieldItemId];
            }
            if (isset($post[FieldTitle])) {
                $this->title = $post[FieldTitle];
            }
            if (isset($post[FieldCatId])) {
                $this->catId = $post[FieldCatId];
            }
            if (isset($post[FieldDescription])) {
                $this->description = $post[FieldDescription];
            }
            if (isset($post[FieldPrice])) {
                $this->price = $post[FieldPrice];
            }
            if (isset($post[FieldIsDisabled])) {
                $this->isDisabled = $post[FieldIsDisabled];
            }
        }
    }

    public function toArray(): array
    {
        return [
            FieldItemId => $this->itemId,
            FieldTitle => $this->title,
            FieldCatId => $this->catId,
            FieldDescription => $this->description,
            FieldPrice => $this->price,
            FieldIsDisabled => $this->isDisabled,
        ];
    }
}