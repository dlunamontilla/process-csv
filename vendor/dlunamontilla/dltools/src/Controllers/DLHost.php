<?php

namespace DLTools\Controllers;

/**
 * @package DLTools
 * @version 1.0.0
 * @author David E Luna <davidlunamontilla@gmail.com>
 * @copyright (c) 2020 - David E Luna M
 * @license MIT
 */

class DLHost {

    /**
     * Devuelve el nombre actual de host
     * @return string
     */
    public function getHostname(): string {
        return $_SERVER['HTTP_HOST'] ?? '';
    }
}