<?php
declare(strict_types = 1);

namespace kyo;

class XssFilter {
    public function handle($request, \Closure $next) {
        $request->filter([[$this, 'filter']]);
        $response = $next($request);
        return $response;
    }

    public function filter($value) {
        if (!is_string($value)) {
            return $value;
        }
        $begin_tag = '<script>';
        $end_tag = '</script>';
        $begin_index = stripos($value, $begin_tag);
        $end_index = strripos($value, $end_tag);
        $len = mb_strlen($value);
        $end_len = mb_strlen($end_tag);
        if (false !== $begin_index && false !== $end_index && $end_index > $begin_index) {
            $start_part = '';
            if ($begin_index > 0) {
                $start_part = mb_substr($value, 0, $begin_index, 'UTF-8');
            }
            $end_part = '';
            if ($end_index + $end_len < $len - 1) {
                $end_part = mb_substr($value, $end_index + $end_len, null, 'UTF-8');
            }
            $value = $start_part . $end_part;
        }
        $value = str_ireplace($begin_tag, '', $value);
        $value = str_ireplace($end_tag, '', $value);
        $value = str_ireplace('<script', '', $value);
        $value = str_ireplace('script>', '', $value);
        $value = str_ireplace('</script', '', $value);
        $value = str_ireplace('/script>', '', $value);
        $value = str_ireplace('script/>', '', $value);
        return $value;
    }
}
