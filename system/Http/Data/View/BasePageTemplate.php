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
            $this->baseLocation = App::getInstance()->getPath();
        } catch (\Exception $exception) {
            throw new \Exception('Path not found');
        }

        if (empty($location)) {
            $this->location = $this->baseLocation;
        } else {
            $this->location = $location;
        }
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
