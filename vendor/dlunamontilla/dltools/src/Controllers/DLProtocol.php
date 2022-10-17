<?php

namespace DLTools\Controllers;

/**
 * @package DLProtocol
 * @version 1.0.0
 * @author David E Luna <davidlunamontilla@gmail.com>
 * @copyright (c) 2020 - David E Luna M
 * @license MIT
 */

class DLProtocol extends DLHost {
    /**
     * @var array $hostnames - List of hostnames
     */
    private array $hostnames;

    /**
     * @param array $hostnames
     */
    public function __construct(array $hostnames = []) {

        if (count($hostnames) > 0) {
            foreach ($hostnames as $host) {
                $this->hostnames[] = $host;
            }
        }
    }

    /**
     * Informar si se está utilizando un protocolo HTTPS
     * @return bool
     */
    private function isHTTPS(): bool {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    /**
     * Forza a utilizar el protocolo HTTPS
     * @return void
     */
    public function https(): void {
        $isHTTPS = $this->isHTTPS();
        $hostname = $this->getHostname();

        foreach($this->hostnames as $host) {
            if (!$isHTTPS && $hostname === $host) {
                header("Location: https://{$host}");
                exit;
            }
        }
    }
}
