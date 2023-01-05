<?php

declare(strict_types=1);

namespace leandrogehlen\querybuilder;

use leandrogehlen\querybuilder\operator\ArrayOperatorPattern;
use leandrogehlen\querybuilder\operator\factory\PatternFactory;
use leandrogehlen\querybuilder\operator\interfaces\PatternInterface;
use leandrogehlen\querybuilder\operator\StringOperatorPattern;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Translator is used to build WHERE clauses from rules configuration
 *
 * The typical usage of Translator is as follows,
 *
 * ```php
 * public function actionIndex()
 * {
 *     $query = Customer::find();
 *     $rules = Yii::$app->request->post('rules');
 *
 *     if ($rules) {
 *         $translator = new Translator(Json::decode($rules));
 *         $query->andWhere($translator->where())
 *               ->addParams($translator->params());
 *     }
 *
 *     $dataProvider = new ActiveDataProvider([
 *         'query' => $query,
 *     ]);
 *
 *     return $this->render('index', [
 *         'dataProvider' => $dataProvider,
 *     ]);
 * }
 * ```
 * @author Leandro Gehlen <leandrogehlen@gmail.com>
 */
class Translator extends BaseObject
{
    private string $where;
    private array $params = [];

    /** @var PatternInterface[] $operators */
    private array $operators;

    /**
     * Constructors.
     * @param array $data Rules configuraion
     * @param array $config the configuration array to be applied to this object.
     * @throws \Exception
     */
    public function __construct(array $data, array $config = [])
    {
        parent::__construct($config);
        $this->where = $this->buildWhere($data);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->operators = PatternFactory::getDefaultOperators();
    }


    /**
     * Encodes filter rule into SQL condition
     * @param string $field field name
     * @param string $operator operator type
     * @param array $params query parameters
     * @return string encoded rule
     * @throws \Exception
     */
    protected function encodeRule(string $field, string $operator, array $params): string
    {
        $pattern = $this->operators[$operator];
        $keys = array_keys($params);
        $replacement = null;

        if ($pattern instanceof StringOperatorPattern) {
            $replacement = !empty($keys) ? $keys[0] : null;
        }

        if ($pattern instanceof ArrayOperatorPattern) {
            $op = $pattern->getPattern();
            if ($pattern->isList()) {
                $separator = $pattern->getSeparator();
                $replacement = implode($separator, $keys);
            } else {
                $replacement = key($params);
                $params[$replacement] = $pattern->getReplacement($params[$replacement]);
            }
            $pattern = $op;
        }

        $this->setParams(array_merge($this->params, $params));
        return $field . " " . ($replacement ? str_replace("?", $replacement, $pattern) : $pattern);
    }

    /**
     * @param array $data rules configuration
     * @return string the WHERE clause
     * @throws \Exception
     */
    protected function buildWhere(array $data): string
    {
        if (!isset($data['rules']) || !$data['rules']) {
            return '';
        }

        $where = [];
        $condition = " " . $data['condition'] . " ";

        foreach ($data['rules'] as $rule) {
            if (isset($rule['condition'])) {
                $where[] = $this->buildWhere($rule);

                continue;
            }

            $field = $rule['field'];
            $operator = $rule['operator'];
            $value = $rule['value'] ?? null;
            $params = $this->generateParams($value);

            $where[] = $this->encodeRule($field, $operator, $params);
        }
        return "(" . implode($condition, $where) . ")";
    }

    protected function generateParams(mixed $value): array
    {
        $params = [];

        if ($value !== null) {
            $i = count($this->params);

            if (!is_array($value)) {
                $value = [$value];
            }

            foreach ($value as $v) {
                $params[":p$i"] = $v;
                $i++;
            }
        }

        return $params;
    }

    /**
     * Returns query WHERE condition.
     * @return string
     */
    public function where(): string
    {
        return $this->where;
    }

    /**
     * Returns the parameters to be bound to the query.
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    protected function setParams(array $params)
    {
        $this->params = $params;
    }
}
