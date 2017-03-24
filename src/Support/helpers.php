<?php

if (!function_exists('generate_sign')) {
    /**
     * Generate a signature.
     *
     * @param array  $attributes
     * @param string $key
     * @param string $encryptMethod
     *
     * @return string
     */
    function generate_sign(array $attributes, $key, $encryptMethod = 'md5')
    {
        ksort($attributes);

        $attributes['key'] = $key;

        return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
    }
}
