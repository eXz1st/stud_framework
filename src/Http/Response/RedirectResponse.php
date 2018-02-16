<?php
namespace Mindk\Framework\Http\Response;
/**
 * Class RedirectResponse
 *
 * @package Mindk\Framework\Http\Response
 */
class RedirectResponse extends Response
{

    /**
     * RedirectResponse constructor.
     * @param $url
     * @param int $code
     */
    public function __construct($url, int $code = 301) {
        parent::__construct('', $code);
        $this->setHeader('Location', $url);
    }
}