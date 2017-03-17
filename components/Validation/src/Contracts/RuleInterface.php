<?php namespace Limoncello\Validation\Contracts;

/**
 * Copyright 2015-2016 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Generator;

/**
 * @package Limoncello\Validation
 */
interface RuleInterface
{
    /**
     * @param mixed $input
     *
     * @return Generator
     */
    public function validate($input);

    /**
     * @return bool
     */
    public function isStateless();

    /**
     * @param RuleInterface $parent
     *
     * @return void
     */
    public function setParentRule(RuleInterface $parent);

    /**
     * @return null|string
     */
    public function getParameterName();

    /**
     * @param null|string $parameterName
     *
     * @return self
     */
    public function setParameterName($parameterName);

    /**
     * @param ErrorAggregatorInterface $aggregator
     *
     * @return void
     */
    public function onFinish(ErrorAggregatorInterface $aggregator);
}