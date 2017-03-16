<?php namespace Limoncello\Core\Contracts\Application;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package Limoncello\Core
 */
interface SapiInterface
{
    /**
     * @return array
     */
    public function getServer();

    /**
     * @return string|resource|StreamInterface
     */
    public function getRequestBody();

    /**
     * @return array|object
     */
    public function getParsedBody();

    /**
     * @return array
     */
    public function getQueryParams();

    /**
     * @return array
     */
    public function getCookies();

    /**
     * @return array
     */
    public function getFiles();

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @return UriInterface
     */
    public function getUri();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function handleResponse(ResponseInterface $response);
}
