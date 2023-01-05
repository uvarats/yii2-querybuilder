<?php

declare(strict_types=1);

namespace leandrogehlen\querybuilder\operator\interfaces;

interface PatternInterface
{
    public function getPattern(): string;
}