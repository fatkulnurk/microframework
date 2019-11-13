<?php

namespace Fatkulnurk\Microframework\Core;

trait Singleton
{
    /**
     * Berisi Object dari class yang menggunakan trait ini.
     * @var self $instance
     */
    private static $instance = null;

    /**
     * Pembuatan object singleton
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
