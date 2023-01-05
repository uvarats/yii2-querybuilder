<?php

declare(strict_types=1);

namespace leandrogehlen\querybuilder\operator\factory;

use leandrogehlen\querybuilder\operator\ArrayOperatorPattern;
use leandrogehlen\querybuilder\operator\interfaces\PatternInterface;
use leandrogehlen\querybuilder\operator\StringOperatorPattern;

class PatternFactory
{
    public static function createStringPattern(string $pattern): PatternInterface {
        return new StringOperatorPattern($pattern);
    }

    public static function createArrayPattern(array $pattern): PatternInterface {
        return new ArrayOperatorPattern($pattern);
    }

    /**
     * @return PatternInterface[]
     */
    public static function getDefaultOperators(): array {
        return [
            'equal' =>            self::createStringPattern('= ?'),
            'not_equal' =>        self::createStringPattern('<> ?'),
            'in' =>               self::createArrayPattern(
                ['op' => 'IN (?)',     'list' => true, 'sep' => ', ' ]
            ),
            'not_in' =>           self::createArrayPattern(
                ['op' => 'NOT IN (?)', 'list' => true, 'sep' => ', ']
            ),
            'less' =>             self::createStringPattern('< ?'),
            'less_or_equal' =>    self::createStringPattern('<= ?'),
            'greater' =>          self::createStringPattern('> ?'),
            'greater_or_equal' => self::createStringPattern('>= ?'),
            'between' =>          self::createArrayPattern(
                ['op' => 'BETWEEN ?',   'list' => true, 'sep' => ' AND ']
            ),
            'not_between' =>      self::createArrayPattern(
                ['op' => 'NOT BETWEEN ?',   'list' => true, 'sep' => ' AND ']
            ),
            'begins_with' =>      self::createArrayPattern(
                ['op' => 'LIKE ?',     'fn' => function($value){ return "$value%"; } ]
            ),
            'not_begins_with' =>  self::createArrayPattern(
                ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "$value%"; } ]
            ),
            'contains' =>         self::createArrayPattern(
                ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value%"; } ]
            ),
            'not_contains' =>     self::createArrayPattern(
                ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value%"; } ]
            ),
            'ends_with' =>        self::createArrayPattern(
                ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value"; } ]
            ),
            'not_ends_with' =>    self::createArrayPattern(
                ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value"; } ]
            ),
            'is_empty' =>         self::createStringPattern('= ""'),
            'is_not_empty' =>     self::createStringPattern('<> ""'),
            'is_null' =>          self::createStringPattern('IS NULL'),
            'is_not_null' =>      self::createStringPattern('IS NOT NULL')
        ];
    }
}