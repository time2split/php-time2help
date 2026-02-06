<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

/**
 * Type of an edge.
 *
 * @see Path
 * @see PathEdge
 * 
 * @package time2help\container\path
 * @author Olivier Rodriguez (zuri)
 */
enum PathEdgeType
{
    /**
     * Represents the "current" element (like `.` in an url).
     */
    case Current;

    /**
     * Represents the "previous" element (like `..` in an url).
     */
    case Previous;
}
