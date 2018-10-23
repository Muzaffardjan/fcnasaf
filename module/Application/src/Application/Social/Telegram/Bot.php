<?php
/**
 * FC Nasaf official website
 *
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.each.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Application\Social\Telegram;


use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Json\Json;

class Bot
{
    const CMD_HOST = 'https://api.telegram.org/bot%api%/%method%';

    /**
     * @var array
     */
    protected $chats;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * Bot constructor.
     *
     * @param string $apiKey
     * @param array  $chats
     */
    public function __construct($apiKey, array $chats)
    {
        $this->chats  = $chats;
        $this->apiKey = $apiKey;
    }

    public function sendMessage($message, $html = true)
    {
        $parameters = [
            'text'       => $message,
            'parse_mode' => $html ? 'HTML': 'Markdown',
            'chat_id'    => '',
            'disable_web_page_preview' => false,
        ];

        //return $this->send('sendMessage', $parameters);
    }

    private function send($method, array $parameters)
    {
        $request = new Request();
        $headers = new Headers();
        $headers->addHeaderLine('Content-Type', 'application/json');
        $request->setHeaders($headers);
        $request->setUri($this->composeUrl($method));

        foreach ($this->chats as $chat) {
            $parameters['chat_id'] = $chat;

            $request->setContent(Json::encode($parameters));

            $this->sendRequest($request);
        }

        return true;
    }

    /**
     * @param Request $request
     * @return \Zend\Http\Response
     */
    private function sendRequest(Request $request)
    {
        if (null === $this->httpClient) {
            $this->httpClient = new Client();

            $this->httpClient->setOptions(
                [
                    'sslverifypeer' => false,
                ]
            );
        }

        return $this->httpClient->setRequest($request)->send();
    }

    /**
     * @param string $method
     * @return string
     */
    private function composeUrl($method)
    {
        return str_replace(
            ['%api%', '%method%'],
            [$this->apiKey, $method],
            self::CMD_HOST
        );
    }
}