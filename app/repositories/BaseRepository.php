<?php
namespace App\Repositories;

use HTMLPurifier;
use HTMLPurifier_Config;
class BaseRepository
{
    protected $purifier;

    public function __construct()
    {
        // Configuración de HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Limpiar el contenido HTML
     *
     * @param string $content
     * @return string
     */
    protected function cleanHtml($content)
    {
        if (empty($content)) {
            return null;
        }
        return $this->purifier->purify($content);
    }

}
