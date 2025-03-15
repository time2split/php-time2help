<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Bag;

/**
 * @internal
 */
class BagWithArrayStorage extends BagWithStorage
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function clear(): void
    {
        parent::clear();
        $this->storage = [];
    }
}
