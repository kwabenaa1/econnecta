<?php


if (!function_exists('qxStringStartsWith')) {
    /**
     * Starts with.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    function qxStringStartsWith($haystack, $needle)
    {
        # search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}

if(!function_exists("qxStringEchobig")) {
  function qxStringEchobig($string, $bufferSize = 1000) {
      $splitString = str_split($string, $bufferSize);

      foreach($splitString as $chunk) {
          echo $chunk;
      }
  }
}

if (!function_exists('qxStringEndsWith')) {
    /**
     * Ends with.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    function qxStringEndsWith($haystack, $needle)
    {
        # search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle,
                $temp) !== false);
    }
}

if (!function_exists('quix_trailingslashit')) {
    /**
     * @param $string
     *
     * @return string
     */
    function quix_trailingslashit($string)
    {
        return quix_untrailingslashit($string) . '/';
    }
}

if (!function_exists('quix_untrailingslashit')) {
    /**
     * @param $string
     *
     * @return string
     */
    function quix_untrailingslashit($string)
    {
        return rtrim($string, '/\\');
    }
}

if (!function_exists('classNames')) {
    /**
     * Get class names.
     *
     * @return string
     */
    function classNames()
    {
        $args = func_get_args();

        $classes = array_map(function ($arg) {
            if (is_array($arg)) {
                return implode(" ", array_filter(array_map(function ($expression, $class) {
                    return $expression ? $class : false;
                }, $arg, array_keys($arg))));
            }

            return $arg;
        }, $args);

        return implode(" ", array_filter($classes));
    }
}

if (!function_exists('visibilityClasses')) {

    /**
     * Get the class visibility from the given visibility.
     * @param $visibility
     *
     * @return string
     */
    function visibilityClasses($visibility)
    {
        return classNames([
            'qx-hidden-lg' => !$visibility['lg'],
            'qx-hidden-md' => !$visibility['md'],
            'qx-hidden-sm' => !$visibility['sm'],
            'qx-hidden-xs' => !$visibility['xs'],
        ]);
    }
}