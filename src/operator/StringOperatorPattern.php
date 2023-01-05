<?php

declare(strict_types=1);

namespace leandrogehlen\querybuilder\operator;

use leandrogehlen\querybuilder\operator\interfaces\PatternInterface;

class StringOperatorPattern implements PatternInterface
{
    private string $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }


    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }


}