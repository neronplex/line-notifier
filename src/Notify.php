<?php
namespace Neronplex\LineNotifier;

use GuzzleHttp\Client;
use LogicException;

/**
 * Class Notify
 *
 * @author    暖簾 <admin@neronplex.info>
 * @copyright Copyright (c) 2017 暖簾
 * @link      https://github.com/neronplex/line-notifier
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @package   Neronplex\LineNotifier
 * @since     0.0.1
 */
class Notify
{
    /**
     * LINE Notify API Endpoint
     *
     * @var string
     */
    const API_URL = 'https://notify-api.line.me/api/notify';

    /**
     * LINE Notify API Access Token
     *
     * @var string
     */
    protected $token = null;

    /**
     * Send message
     *
     * @var string
     */
    protected $message = null;

    /**
     * Send image thumbnail uri
     *
     * maximum resolution 1024×1024px
     * jpeg only
     *
     * @var null|string
     */
    protected $imageThumbnail = null;

    /**
     * Send full image uri
     *
     * maximum resolution 1024×1024px
     * jpeg only
     *
     * @var null|string
     */
    protected $imageFullsize = null;

    /**
     * Upload image uri / path
     *
     * @var null|string
     */
    protected $imageFile = null;

    /**
     * Sticker Package ID
     *
     * @var null|int
     */
    protected $stickerPackageId = null;

    /**
     * Sticker ID
     *
     * @var null|int
     */
    protected $stickerId = null;

    /**
     * Guzzle Http Client
     *
     * @var Client
     */
    protected $client = null;

    /**
     * API response
     *
     * @var \stdClass
     */
    protected $response = null;

    /**
     * Notify constructor.
     *
     * @param string|null $token
     */
    public function __construct(string $token = null)
    {
        $this->token  = $token;
        $this->client = new Client();
    }

    /**
     * Set access token.
     *
     * @param  string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get access token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set message.
     *
     * @param  string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set image thumbnail uri.
     *
     * @param null|string $imageThumbnail
     */
    public function setImageThumbnail(string $imageThumbnail = null)
    {
        $this->imageThumbnail = $imageThumbnail;
    }

    /**
     * Get image thumbnail uri.
     *
     * @return null|string
     */
    public function getImageThumbnail()
    {
        return $this->imageThumbnail;
    }

    /**
     * Set image fullsize uri.
     *
     * @param null|string $imageFullsize
     */
    public function setImageFullsize(string $imageFullsize = null)
    {
        $this->imageFullsize = $imageFullsize;
    }

    /**
     * Get image fullsize uri.
     *
     * @return null|string
     */
    public function getImageFullsize()
    {
        return $this->imageFullsize;
    }

    /**
     * Set upload image uri or path.
     *
     * @param  string|null $imageFile
     * @return $this
     */
    public function setImageFile(string $imageFile = null)
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Get upload image uri or path.
     *
     * @return null|string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set sticker package id.
     *
     * @param  int|null $stickerPackageId
     * @return $this
     */
    public function setStickerPackageId(int $stickerPackageId = null)
    {
        $this->stickerPackageId = $stickerPackageId;

        return $this;
    }

    /**
     * Get sticker package id.
     *
     * @return int|null
     */
    public function getStickerPackageId()
    {
        return $this->stickerPackageId;
    }

    /**
     * Set sticker id.
     *
     * @param  int|null $stickerId
     * @return $this
     */
    public function setStickerId(int $stickerId = null)
    {
        $this->stickerId = $stickerId;

        return $this;
    }

    /**
     * Get sticker id.
     *
     * @return int|null
     */
    public function getStickerId()
    {
        return $this->stickerId;
    }

    /**
     * Set sending sticker information.
     *
     * @param  int|null $pkgId
     * @param  int|null $id
     * @return $this
     */
    public function setSticker(int $pkgId = null, int $id = null)
    {
        return $this->setStickerPackageId($pkgId)->setStickerId($id);
    }

    /**
     * Get api response.
     *
     * @return \stdClass
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * it is multipart request?
     *
     * @return bool
     */
    public function isMultipart(): bool
    {
        return !empty($this->getImageFile());
    }

    /**
     * Send api request.
     *
     * @return bool
     */
    public function send(): bool
    {
        $this->beforeSend();

        $result = $this->client->request('POST', static::API_URL, array_merge(
            $this->getHeader(),
            !$this->isMultipart() ? $this->getForm() : $this->getMultipart(),
            $this->getHttpErrors()
        ));

        $this->response = json_decode($result->getBody());

        return ($result->getStatusCode() === 200);
    }

    /**
     * Precheck request parameter.
     *
     * @return bool
     * @throws LogicException When the parameter is invalid.
     */
    protected function beforeSend(): bool
    {
        if (empty($this->getToken()))
        {
            throw new LogicException('Access token is mandatory.');
        }

        if (empty($this->getMessage()))
        {
            throw new LogicException('Message is mandatory.');
        }

        return true;
    }

    /**
     * Get authorization header.
     *
     * @return array
     */
    protected function getHeader(): array
    {
        return [
            'headers' => [
                'Authorization' => implode(' ', [
                    'Bearer',
                    $this->getToken()
                ])
            ]
        ];
    }

    /**
     * Get form parameter.
     *
     * @return array
     */
    protected function getForm(): array
    {
        return ['form_params' => array_filter(
            [
                'message'          => $this->getMessage(),
                'imageThumbnail'   => $this->getImageThumbnail(),
                'imageFullsize'    => $this->getImageFullsize(),
                'stickerPackageId' => $this->getStickerPackageId(),
                'stickerId'        => $this->getStickerId()
            ],
            function ($v) {
                return !empty($v);
            })
        ];
    }

    /**
     * Get multipart parameter.
     *
     * @return array
     */
    protected function getMultipart(): array
    {
        return ['multipart' => array_filter(
            [
                [
                    'name'     => 'message',
                    'contents' => $this->getMessage()
                ],
                [
                    'name'     => 'imageFile',
                    'contents' => fopen($this->getImageFile(), 'r')
                ],
                [
                    'name'     => 'stickerPackageId',
                    'contents' => $this->getStickerPackageId()
                ],
                [
                    'name'     => 'stickerId',
                    'contents' => $this->getStickerId()
                ],
            ],
            function ($v) {
                return !empty($v['contents']);
            })
        ];
    }

    /**
     * Get http_errors parameter.
     *
     * @return array
     */
    protected function getHttpErrors(): array
    {
        return [
            'http_errors' => false
        ];
    }
}
