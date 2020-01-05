<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

/**
 * Class BasePageTemplate
 */
abstract class BasePageTemplate implements PageTemplate
{
    /**
     * @var string
     */
    protected $baseLocation = '\xampp\htdocs\microframework\src\views';
//    protected $baseLocation = '../../../src/views';
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
     */
    public function __construct(string $location = '')
    {
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
