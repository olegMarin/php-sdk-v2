<?php
namespace Fondy\Helper;


class ApiHelper
{
    /**
     * @var string sign separator
     */
    const signatureSeparator = '|';

    /**
     * @param array $params
     * @param $secret_key
     * @param $version
     */
    public static function generateSignature($params = [], $secret_key, $version = '1.0', $encoded = true)
    {

        if ($version == '2.0') {
            if ($encoded) {
                $signature = sha1($secret_key . self::signatureSeparator . base64_encode(json_encode($params)));
            } else {
                $signature = $secret_key . self::signatureSeparator . base64_encode(json_encode($params));
            }

        } else {
            $data = array_filter($params,
                function ($var) {
                    return $var !== '' && $var !== null;
                });
            ksort($data);
            $sign_str = $secret_key;
            foreach ($data as $k => $v) {
                $sign_str .= self::signatureSeparator . $v;
            }
            if ($encoded) {
                $signature = sha1($sign_str);
            } else {
                $signature = $sign_str;
            }
        }
        return strtolower($signature);
    }

    /**
     * @param string $merchant_id
     * @return string
     */
    public static function generateOrderID($merchant_id)
    {
        return $merchant_id . '_' . md5(uniqid(rand(), 1));
    }

    /**
     * @param $order_id
     * @return string
     */
    public static function generateOrderDesc($order_id)
    {
        return sprintf('Order pay #: %s', $order_id);
    }

    /**
     * @param $data
     * @param $url
     * @return string
     */
    public static function generatePaymentForm($data, $url)
    {
        $form = '<form method="POST" action="' . $url . '">' . "\n";
        foreach ($data as $name => $value) {
            if (!empty($value)) {
                $form .= '<input type="hidden" name="' . htmlentities($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '" />' . "\n";
            }
        }
        $form .= '<input type="submit" class="f_button"></form>';
        return $form;
    }

    /**
     * @param $data
     * @param null $node
     * @return xml
     */
    public static function toXML($data, $wrap = '?xml version="1.0" encoding="UTF-8"?')
    {
        $xml = '';
        if ($wrap != null) {
            $xml .= "<$wrap>\n";
        }
        foreach ($data as $key => $value) {

            if (empty($value))
                continue;
            if (is_numeric($key))
                continue;
            $xml .= "<$key>";
            if (is_array($value)) {
                $child = self::toXML($value, null);
                $xml .= $child;
                if (!is_array($value))
                    $xml .= htmlspecialchars(trim($value)) . "</$key>";
            } else {
                if (!is_array($value))
                    $xml .= htmlspecialchars(trim($value)) . "</$key>";
            }
        }
        if ($wrap != null) {
            $xml .= "\n</xml>\n";
        }

        return $xml;
    }

    /**
     * @param $data
     * @return string
     */
    public static function toJSON($data)
    {
        return json_encode($data);
    }

    /**
     * @param $data
     * @return string
     */
    public static function toFormData($data)
    {
        return http_build_query($data, NULL, '&');
    }
    /**
     * @param string
     * @return array
     */
    public static function jsonToArray($data)
    {
        return json_decode($data, TRUE);
    }
}