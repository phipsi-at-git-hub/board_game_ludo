<?php
// src/Constants/HttpMethodConstants.php

namespace App\Constants;

final class HttpMethodConstants
{
    /**
     * Defines the server variable used to determine the request method.
     */
    public const SERVER_REQUEST_METHOD = 'REQUEST_METHOD';

    /**
     * Defines supported HTTP request methods.
     */
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
}
