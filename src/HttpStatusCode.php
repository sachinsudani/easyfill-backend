<?php

namespace src;

class HttpStatusCode {
    const OK = "HTTP/1.1 200 OK";
    const NOT_FOUND = "HTTP/1.1 404 Not Found";
    const BAD_REQUEST = "HTTP/1.1 400 Bad Request";
    const CONFLICT = "HTTP/1.1 409 Conflict";
    const DELETE = "HTTP/1.1 204 No Content";
}