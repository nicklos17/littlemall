<?php
class Curl
{
    public static function send($url, $fields = array(), $method = 'get'){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        switch ($method) {
            case 'get':
                    curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                    break;
            case 'post':
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
                    break;
            case 'delete':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
                    break;
            case 'put':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
                    break;
            default:
                    # code...
                    break;
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private static function _format_fields($fields){
        $uri_fields = '';
        foreach ($fields as $key => $value) {
                $uri_fields .= '/'.$key.'/'.$value.'/';
        }
        return $uri_fields;
    }
}