<?php

namespace Time2Split\Help\Container\Class;

/**
 * Whether a container is unmodifiable.
 * 
 * Any call of a "write" function must throws an {@see \Time2Split\Help\Exception\UnmodifiableException}.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see GetUnmodifiable
 */
interface IsUnmodifiable {}
