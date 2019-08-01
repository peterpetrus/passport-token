<?php

namespace PeterPetrus\Auth;

use \DateTime;

/**
 * Class PassportToken
 *
 * @property string $token_id
 * @property string $user_id
 * @property boolean $expecting
 * @property int $start_at_unix
 * @property string $start_at
 * @property boolean $incorrect
 * @property int $created_at_unix
 * @property string $created_at
 * @property boolean $expired
 * @property int $expires_at_unix
 * @property string $expires_at
 * @property boolean $error
 * @property array $errors
 * @property boolean $valid
 *
 * @package PeterPetrus\Auth
 */
class PassportToken
{
    private $token = null;
    private $properties = array();

    public function __construct($token)
    {
        $this->properties = static::dirtyDecode($token);
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function __get($property) {
        if (array_key_exists($property, $this->properties)) {
            return $this->properties[$property];
        }
        return null;
    }

    /**
     * Check if token exists in DB (table 'oauth_access_tokens'), require \Illuminate\Support\Facades\DB class
     *
     * @return boolean
     */
    public function existsValid()
    {
        return static::existsValidToken($this->token_id, $this->user_id);
    }

    /**
     * Check if token exists in DB (table 'oauth_access_tokens'), require \Illuminate\Support\Facades\DB class
     *
     * @param string $token_id
     * @param string $user_id
     *
     * @return boolean
     */
    public static function existsValidToken($token_id, $user_id)
    {
        if (class_exists('\Illuminate\Support\Facades\DB')) {
            return (bool) \Illuminate\Support\Facades\DB::table('oauth_access_tokens')
                ->where('id', $token_id)
                ->where('user_id', $user_id)
                ->where('expires_at', '>=', date('Y-m-d H:i:s'))
                ->count();
        } else {
            return false;
        }
    }

    /**
     * Decode a Access Token
     *
     * @param string $access_token Access Token
     * @param array $claims
     *
     * @return array
     */
    public static function dirtyDecode($access_token, $claims = array())
    {
        $now = time();
        $expecting = false;
        $incorrect = false;
        $expired = false;
        $error = false;
        $errors = array();
        $token_segments = explode('.', $access_token);
        $body = (isset($token_segments[1])) ? $token_segments[1] : null;

        if (count($token_segments) != 3) {
            $error = true;
            $errors[] = "Token has wrong number of segments";
        }
        if (null === $data = static::jsonDecode(static::urlDecode($body))) {
            $error = true;
            $errors[] = "Decoder has problem with Token encoding";
        }
        if (isset($data->nbf) && $data->nbf > $now) {
            $expecting = true;
        }
        if (isset($data->iat) && $data->iat > $now) {
            $incorrect = true;
        }
        if (isset($data->exp) && $now >= $data->exp) {
            $expired = true;
        }

        $decodedToken =  array(
            'token_id' => (isset($data->jti)) ? $data->jti : null,
            'user_id' => (isset($data->sub)) ? $data->sub : null,
            'expecting' => $expecting,
            'start_at_unix' => (isset($data->nbf)) ? $data->nbf : null,
            'start_at' => (isset($data->nbf)) ? date(DateTime::ISO8601, $data->nbf) : null,
            'incorrect' => $incorrect,
            'created_at_unix' => (isset($data->iat)) ? $data->iat : null,
            'created_at' => (isset($data->iat)) ? date(DateTime::ISO8601, $data->iat) : null,
            'expired' => $expired,
            'expires_at_unix' => (isset($data->exp)) ? $data->exp : null,
            'expires_at' => (isset($data->exp)) ? date(DateTime::ISO8601, $data->exp) : null,
            'error' => $error,
            'errors' => $errors,
            'valid' => ($expecting || $incorrect || $expired || $error) ? false : true
        );

        if(!empty($claims)){
            $decodedToken['claims'] = static::getCustomClaims($data, $claims);
        }

        return $decodedToken;
    }

    public static function urlDecode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function jsonDecode($input)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }

        if (function_exists('json_last_error') && $errno = json_last_error()) {
            return null;
        } elseif ($obj === null && $input !== 'null') {
            return null;
        }
        return $obj;
    }

    public static function getCustomClaims($data, $claims)
    {
        $decodedToken = array();
        foreach ($claims as $claim){
            foreach ($data as $key => $value) {
                if ($key == $claim) {
                    $decodedToken[$claim] = (isset($claim)) ? $value : null;
                }
            }
        }

        return $decodedToken;
    }
}
