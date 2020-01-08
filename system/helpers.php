<?php

if (!function_exists('base_path_src')) {
    function base_path_src()
    {
        return dirname(__DIR__);
    }


    if (!function_exists('alert')) {
        /**
         * Prints clear string into the console with new line char at the end
         *
         * @param string $message
         */
        function alert(string $message)
        {
            echo $message . PHP_EOL;
        }
    }

    if (!function_exists('dd')) {
        /**
         * Dump and die (Laravel alternative)
         *
         * @param $value
         */
        function dd($value)
        {
            var_dump($value);
            die();
        }
    }

    if (!function_exists('env')) {
        /**
         * Returns or sets $_ENV variable (type casting support)
         *
         * @param string $key
         * @param null $value
         *
         * @return array|bool|false|string
         */
        function env(string $key, $value = null)
        {
            if (!is_null($value)) {
                $setting = "{$key}={$value}";

                return putenv($setting);
            }

            $value = getenv($key);

            return is_json($value) ? json_decode($value, true) : $value;
        }
    }

    if (!function_exists('is_json')) {
        /**
         * Checks if is a string value JSON format
         *
         * @param string $input
         *
         * @return bool
         */
        function is_json(string $input)
        {
            json_decode($input);

            return json_last_error() == JSON_ERROR_NONE;
        }
    }

    if (!function_exists('arrayzy')) {
        /**
         * Returns Arrayzy instance, also from different arguments
         *
         * @param null $data
         * @param null $separator
         *
         * @return \Arrayzy\AbstractArray
         */
        function arrayzy($data = null, $separator = null)
        {
            if (!is_null($data)) {
                if (is_array($data)) {
                    return ArrayImitator::create($data);
                } elseif (is_object($data)) {
                    return ArrayImitator::createFromObject($data);
                } elseif (is_string($data) && !is_null($separator)) {
                    return ArrayImitator::createFromString($data, $separator);
                } elseif (is_json($data)) {
                    return ArrayImitator::createFromJson($data);
                }
            }

            return ArrayImitator::create([]);
        }
    }

    if (!function_exists('stringy')) {
        /**
         * Returns Stringy instance, also from different arguments
         * @param string $content
         *
         * @return Stringy
         */
        function stringy(string $content = '')
        {
            return Stringy::create($content);
        }
    }

    if (!function_exists('now')) {
        /**
         * Returns a new Carbon instance
         * @return \Carbon\Carbon|\Carbon\CarbonInterface
         */
        function now()
        {
            if (class_exists(\Carbon\Carbon::class)) {
                return \Carbon\Carbon::now();
            }

            return 'Carbon Not Instaled';
        }
    }
}
