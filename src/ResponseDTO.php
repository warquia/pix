<?php

namespace Warquia\Pix;

class ResponseDTO {
    public function __construct(int $statusCode, string $message, $content = null) {
        $this->code = $statusCode;
        $this->message = $message;
        $this->content = $content;
    }
}

