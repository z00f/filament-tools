<?php

namespace z00f\FilamentTools\Exceptions;

use Exception;
use z00f\FilamentTools\Tool;

/** @internal */
class ToolsException extends Exception
{
    public static function expectedTool(mixed $actual): static
    {
        return new static('Expected an instance of ' . Tool::class . ', got ' . gettype($actual) . ' instead.');
    }

    public static function missingLabel(): static
    {
        return new static('All tools must have a label. Please set one by calling the `Tool::label()` method.');
    }
}
