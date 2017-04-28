<?php
/**
 * Copyright (c) 2016 Micorosft Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */

namespace remotelearner\aadsample;

use remotelearner\aadsample\AADSAMPLEException;

/**
 * Basic HttpClient implementation with curl.
 */
class HttpClient implements \remotelearner\aadsample\HttpClientInterface {
    /**
     * POST request.
     *
     * @param string $url The URL to request.
     * @param array|string $data The data to send.
     * @param array $options Additional curl options.
     * @return string Returned text.
     */
    public function post($url, $data = '', $options = array()) {
        return $this->request('post', $url, $data, $options);
    }

    /**
     * GET request.
     *
     * @param string $url The URL to request.
     * @param array|string $data The data to send.
     * @param array $options Additional curl options.
     * @return string Returned text.
     */
    public function get($url, $data = '', $options = array()) {
        return $this->request('get', $url, $data, $options);
    }

    /**
     * Make a curl request.
     *
     * @param string $method The HTTP method to use.
     * @param string $url The URL to request.
     * @param array|string $data The data to send.
     * @param array $options Additional curl options.
     * @return string Returned text.
     */
    public function request($method, $url, $data, $options) {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \Exception('Invalid URL in HttpClient');
        }

        $ch = curl_init();
        $curlopts = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_MAXREDIRS => 12,
        ];

        $method = strtolower($method);
        switch ($method) {
            case 'post':
                $curlopts[CURLOPT_POST] = true;
                $curlopts[CURLOPT_POSTFIELDS] = $data;
                break;

            case 'get':
                $curlopts[CURLOPT_HTTPGET] = true;
                if (!empty($data)) {
                    $curlopts[CURLOPT_URL] = (strpos($url, '?') === false)
                        ? $url.'?'.http_build_query($data)
                        : $url.'&'.http_build_query($data);
                }
                break;

            default:
                throw new AADSAMPLEException('Unsupported request method.');
        }

        curl_setopt_array($ch, $curlopts);

        $returned = curl_exec($ch);

        curl_close($ch);
        return $returned;
    }
}
