<?php

declare(strict_types=1);

namespace leandrogehlen\querybuilder\operator;

use Closure;
use leandrogehlen\querybuilder\operator\interfaces\PatternInterface;

class ArrayOperatorPattern implements PatternInterface
{
    private string $pattern;

    private bool $isList;
    private ?string $separator = null;
    private ?Closure $replacementCallback = null;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->pattern = $config['op'];
        $this->isList = $config['list'] ?? false;

        if ($this->isList) {
            $this->separator = $config['sep'];
        }

        $this->replacementCallback = $config['fn'] ?? null;
    }


    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->isList;
    }

    /**
     * @param string $value
     * @return string
     */
    public function getReplacement(string $value): string
    {
        if ($this->replacementCallback === null) {
            throw new \BadMethodCallException("Callback is null!");
        }

        return call_user_func($this->replacementCallback, $value);
    }

    /**
     * @return string|null
     */
    public function getSeparator(): ?string
    {
        return $this->separator;
    }
}