<?php
namespace lib;
use Aws\CloudWatch\CloudWatchClient;
class Config
{
    public function getCloudWatchClient($conf)
    {
        if (isset($conf->aws->profil)) {
            return CloudWatchClient::factory( [
                'profil'  => $conf->aws->profil,
                'region'  => $conf->aws->region,
                'version' => '2010-08-01'
            ] );
        }

        return CloudWatchClient::factory( [
            'credentials' => [
                'key'    => $conf->aws->key,
                'secret' => $conf->aws->secret
            ],
            'region'      => $conf->aws->region,
            'version'     => '2010-08-01'
        ] );
    }

    public function getConfigFile($configFile)
    {
        $longopts = [
            "required:"     // Valeur requise
        ];
        if (array_key_exists( 'f', $args = getopt( "f:", $longopts ) )) {
            return json_decode( file_get_contents( $args['f'] ) );
        }

        return json_decode( file_get_contents( $configFile ) );
    }
}