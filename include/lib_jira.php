<?php

require_once "../vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

define("JIRA_USERNAME", getenv('JIRA_USERNAME'));
define("JIRA_PASSWORD", getenv('JIRA_PASSWORD'));
define("JIRA_BASE_URL", getenv("JIRA_URL"));
define("JIRA_URL", getenv('JIRA_URL') . "/rest/api/2/issue/");
define("JIRA_GENERAL_FIELD", getenv('JIRA_GENERAL_FIELD'));

/**
 * Function to create jira ticket using the jira API
 *
 */

function jira_new_issue_create($data) {
    $username = JIRA_USERNAME;
    $password = JIRA_PASSWORD;

    $url = JIRA_URL;

    $ch = curl_init();

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    $response = curl_exec($ch);

    $ch_error = curl_error($ch);
    curl_close($ch);
    if ($ch_error){
        return [
            "ok" => false,
            "error" => "Error : Jira API error",
        ];
    }

    return [
        "ok" => true,
        "body" => json_decode($response, true),
    ];
}

function jira_new_issue_get($epic){
    $username = JIRA_USERNAME;
    $password = JIRA_PASSWORD;
    $url = JIRA_URL . $epic;

    $ch = curl_init();

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    $response = curl_exec($ch);
    $ch_error = curl_error($ch);

    curl_close($ch);

    if ($ch_error){
        return [
            "ok" => false,
            "error" => "Error : Jira API error",
        ];
    }

    return [
        "ok" => true,
        "body" => json_decode($response, true),
    ];
}


function jira_new_issue_add_checklistitem($issue_id, $customfield_id, $checklist_items){
    $username = JIRA_USERNAME;
    $password = JIRA_PASSWORD;
    $url = JIRA_BASE_URL . '/rest/api/2/issue/' . $issue_id;

    $payload = [
        'fields' => [
            $customfield_id => $checklist_items
        ],
    ];

    $ch = curl_init();

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    $response = curl_exec($ch);
    $ch_error = curl_error($ch);

    curl_close($ch);

    if ($ch_error){
        return [
            "ok" => false,
            "error" => "Error : Jira API error",
        ];
    }

    return [
        "ok" => true,
        "body" => json_decode($response, true),
    ];

}
