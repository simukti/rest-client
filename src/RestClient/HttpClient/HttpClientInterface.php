<?php
namespace RestClient\HttpClient;

use RestClient\Request\RequestInterface;
use RestClient\Response\ResponseInterface;

/**
 * @package   RestClient\HttpClient
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
interface HttpClientInterface
{
    /**
     * Send actual request to API server and save result to provided ResponseInterface
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request, ResponseInterface $response) : ResponseInterface;
}