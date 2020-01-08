<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

use Fatkulnurk\Microframework\App;

/**
 * Class BasePageTemplate
 */
abstract class BasePageTemplate implements PageTemplate
{
    /**
     * @var string
     */
    protected $baseLocation = '';

    /**
     * @var string
     */
    protected $location = '';
    /**
     * @var string
     */
    protected $templateString = '';

    /**
     * BasePageTemplate constructor.
     * @param string $location
     * @throws \Exception
     */
    public function __construct(string $location = '')
    {
        try {
            $this->baseLocation = App::getInstance()->getConfig('path_template');
        } catch (\Exception $exception) {
            throw new \Exception('config with key path_template not found');
        }
        // $this->baseLocation = $_SERVER['DOCUMENT_ROOT'] . "./../src/views";
        $this->location = $location;
    }

    /**
     * @param string $location
     * @return bool
     */
    public function setLocation(string $location = ''): bool
    {
        if (is_string($location)) {
            $this->location = $location;
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getBaseLocation(): string
    {
        return $this->baseLocation;
    }
}
