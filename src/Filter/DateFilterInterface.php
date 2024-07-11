<?php

declare(strict_types=1);

namespace App\Filter;

interface DateFilterInterface
{
    public const PARAMETER_BEFORE = 'before';
    public const PARAMETER_AFTER = 'after';
    public const EXCLUDE_NULL = 'exclude_null';
    public const INCLUDE_NULL_BEFORE = 'include_null_before';
    public const INCLUDE_NULL_AFTER = 'include_null_after';
    public const INCLUDE_NULL_BEFORE_AND_AFTER = 'include_null_before_and_after';
}