<?php

namespace FlatFindr\HomeAway;

/**
 * Class CrawlerAbstract
 *
 * @package HomeAway
 * @author Valdas Petrulis <petrulis.valdas@gmail.com>
 */
class CrawlerAbstract
{

    /**
     * @var string
     */
    private $browserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:30.0) Gecko/20100101 Firefox/30.0';

    /**
     * @var array
     */
    private $browserHeaders;

    /**
     * Should or not downloaded files be cached
     * @var bool
     */
    private $cacheEnabled = false;

    /**
     * @var
     */
    private $cacheDirectory;

    public function __construct()
    {
        $this->browserHeaders = array(
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Cache-Control' => 'max-age=0',
            'Connection' => 'keep-alive',
            'x-insight' => 'activate',
            'User-Agent' => $this->browserHeaders,
            //'Cookie' => '',
        );
    }

    /**
     * Sets cacheEnable.
     *
     * @param boolean $cacheEnable
     * @return $this
     */
    public function setCacheEnabled($cacheEnable)
    {
        $this->cacheEnabled = $cacheEnable;
        return $this;
    }

    /**
     * Retrieves cacheEnable.
     *
     * @return boolean
     */
    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Sets cacheDirectory.
     *
     * @param mixed $cacheDirectory
     * @return $this
     */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
        return $this;
    }

    /**
     * Retrieves cacheDirectory.
     *
     * @return mixed
     */
    public function getCacheDirectory()
    {
        if(!isset($this->cacheDirectory)) {
            $this->cacheDirectory = __DIR__ . '/cache/';
        }

        return $this->cacheDirectory;
    }

    /**
     * Performs HTTP request
     *
     * @param string $url URL to request
     * @param string $method (Optional) HTTP method (GET, POST, PUT, etc.)
     *
     * @return string Response string
     * @throws \UnexpectedValueException
     */
    public function request($url, $method='GET')
    {
        $this->log(sprintf(
            "HTTP request:\n\t%s",
            $url
        ));

        // Retrieve from cache, if exists
        $cacheEnabled = $this->getCacheEnabled();
        $cacheDirectory = $this->getCacheDirectory();
        $cacheFile = $cacheDirectory . md5($url.'-'.$method);
        if($cacheEnabled && file_exists($cacheFile)) {
            $response = file_get_contents($cacheFile);
            $this->log(sprintf(
                "HTTP cached response: \n\t%s..",
                substr($response, 0, 100)
            ));
            return $response;
        }

        // Do request
        $context = stream_context_create(array(
            'http' => array(
                'method' => $method,
                'user_agent' => $this->browserAgent,
                'header' => implode("\r\n", $this->browserHeaders),
            )
        ));
        $response = file_get_contents($url, false, $context);
        // HTTP request failed
        if($httpError=error_get_last()) {
            throw new \UnexpectedValueException(sprintf(
                'HTTP request failed: %s',
                $httpError['message']
            ));
        }

        list($httpVersions, $httpStatus, $httpMessage) = explode(' ',$http_response_header[0], 3);
        $this->log(sprintf(
            "HTTP status %s with response: \n\t%s..",
            $httpStatus,
            substr($response, 0, 100)
        ));
        if($httpStatus!=200) {
            throw new \UnexpectedValueException(sprintf(
                'HTTP response with wrong status: %s %s',
                $httpStatus,
                $httpMessage
            ));
        }

        // Update cache
        if($cacheEnabled) {
            if(!is_dir($cacheDirectory)) {
                mkdir($cacheDirectory);
                chmod($cacheDirectory, 0777);
            }
            file_put_contents($cacheFile, $response);
        }

        return $response;
    }

    /**
     * Logs message
     *
     * @param string $message
     */
    protected function log($message)
    {
        echo sprintf(
            '%s %s' . PHP_EOL,
            date('Y-m-d H:i:s'),
            $message
        );
    }
}