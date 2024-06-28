<?php

namespace Warquia\Pix;

/**
 * Data Transfer Objects Default of Api
 */
class ResponseDTO {
    /**
     * @param int $statusCode
     * @param string $message
     * @param $content
     */
    public function __construct(int $statusCode, string $message, $content = null) {
        $this->code = $statusCode;
        $this->message = $message;
        $this->content = $content;
    }
}