<?php

namespace Warquia\Pix;

class ResponseDTO {
    public function __construct(int $statusCode, string $message, $contents = null) {
        $this->code = $statusCode;
        $this->message = $message;
        $this->contents = $contents;
    }
}

