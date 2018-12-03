<?php

namespace Infotrip\Utils\Google\Recaptcha;

class V2
{
    const SECRET = '6Lcij34UAAAAAPD4rQsm-DTMUMJf-RNL5pWirpli';

    /**
     * @param $captchaResponse
     * @return bool
     */
    public function captchaIsValid(
        $captchaResponse
    )
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = array(
            'secret' => self::SECRET,
            'response' => $captchaResponse
        );

        $options = array(
            'http' => array (
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        $captcha_success = json_decode($response);

        if ($captcha_success->success==false) {
            return false;
        } else if ($captcha_success->success==true) {
            return true;
        }

        return false;
    }
}