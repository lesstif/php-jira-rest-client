<?php

function getConfig() {
    return array(
        // JIRA Host config
        'host' => 'https://jira.example.com',
        'username' => 'username',
        'password' => 'password',

        // Options
        'CURLOPT_SSL_VERIFYHOST' => false,
        'CURLOPT_SSL_VERIFYPEER' => false,	
        'CURLOPT_VERBOSE' => true,
        'LOG_FILE' => 'QQjira-rest-client.log',
        'LOG_LEVEL' => 'DEBUG'
    );
}

?>
