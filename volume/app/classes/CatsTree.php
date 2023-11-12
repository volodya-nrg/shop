<?php

final class CatsTree
{
    public CatRow $catRow;
    public array $childs = []; // CatsTree[]

    public function __construct(CatRow ...$catRows)
    {
        $this->catRow = new CatRow();
        $this->childs = $this->buildTreeWalk(0, ...$catRows);
    }

    /**
     * @return CatsTree[]
     */
    private function buildTreeWalk(int $findCatId, CatRow ...$catRows): array
    {
        $branch = [];

        foreach ($catRows as $catRow) {
            if ($catRow->parent_id === $findCatId) {
                $catTreeLoc = new CatsTree();
                $catTreeLoc->catRow = $catRow;
                $catTreeLoc->childs = $this->buildTreeWalk($catRow->cat_id, ...$catRows);

                $branch[] = $catTreeLoc;
            }
        }

        return $branch;
    }
}