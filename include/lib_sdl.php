<?php

include('lib_jira.php');
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
error_reporting(0);

//
// Retrieves JSON summary of all available modules
//
function sdl_module_list()
{
    $list = [];
    foreach (_sdl_valid_modules() as $filename) {
        $parsed = json_decode(file_get_contents($filename), true);
        $category = (isset($parsed['category'])) ? $parsed['category'] : 'General';
        if (@!is_array($list[$category])) {
            $list[$category] = [];
        }

        $infoobj = [
            'filename' => $filename,
            'title' => $parsed['title'],
            'description' => $parsed['description'],
            'tags' => $parsed['tags'],
        ];

        if (@is_array($parsed['submodules'])) {
            $infoobj['submodules'] = [];
            foreach ($parsed['submodules'] as $submod) {
                $infoobj2 = [
                    'filename' => $filename,
                    'title' => $submod['title'],
                    'description' => $submod['description'],
                ];

                if (isset($submod['tags'])) {
                    $infoobj2['tags'] = $submod['tags'];
                }

                $infoobj['submodules'][] = $infoobj2;
            }
        }

        $list[$category][] = $infoobj;
    }

    array_multisort($list);

    return [
        'ok'  => true,
        'list' => $list,
    ];
}

//
// Generate the SDL JIRA tickets
//
function sdl_generate($input)
{
    // Retrieve information
    $project_name = $input['project_name']['value'];
    $user = $input['user']['value'];
    $risk_rating = $input['risk_rating']['value'];
    $jiraEpicId = $input['jiraepic']['value'];
    $list_of_modules = $input['list_of_modules'];
    $jira_components = $input['jira_components']['value'];


    // Check if the given issue is an EPIC or non-EPIC
    $isEpic = _sdl_check_jira_type($jiraEpicId);

    // Create checklist ticket
    $ret = _sdl_create_jira_checklist_ticket($jiraEpicId, $risk_rating, $list_of_modules, $project_name, $jira_components, $isEpic);
    if (!$ret['ok']) {
        return $ret;
    } else {
        $jiraChecklist = $ret['response'];
        $ret = _sdl_populate_checklist($jiraChecklist, $risk_rating);
    }

    // Create ticket for seurity team
//          $ret = _sdl_create_jira_ticket_prodsec($input, $jiraChecklist["key"]);
//          if (! $ret['ok']) return $ret;

    return [
        'ok' => true,
        'status' => 200,
        'name' => 'SDL: '.$project_name,
        'components' => $jira_components,
        'link' => getenv('JIRA_URL').'/browse/'.$jiraChecklist['key'],
    ];
}

//
// Returns a list of valid (and includable) modules
//
function _sdl_valid_modules()
{
    return _sdl_rsearch('../www/sdl/modules/', '/.*\.json/');
}


function _sdl_valid_choosable_modules()
{
    $list = [];

    foreach (_sdl_valid_modules() as $filename) {
        $parsed = json_decode(file_get_contents($filename), true);
        $list[] = $filename;

        if (isset($parsed['submodules']) && is_array($parsed['submodules'])) {
            foreach ($parsed['submodules'] as $submod) {
                $list[] = $filename.md5($filename.$submod['title']);
            }
        }
    }

    return $list;
}

//
// Recursively searches a folder for a matching regex file pattern
//
function _sdl_rsearch($folder, $pattern)
{
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = [];

    foreach ($files as $file) {
        $fileList = array_merge($fileList, $file);
    }

    return $fileList;
}

//
// Check if the JIRA issue is an EPIC
//
function _sdl_check_jira_type($epic)
{

    // Regex to check EPIC key
    if (preg_match('/^[A-Z0-9]+-[0-9]/', $epic)) {
        $ret = jira_new_issue_get($epic);
        if ($ret['ok']) {
            if ($ret['body']['fields']['issuetype']['name'] === 'Epic') {
                return true;
            } else {
                return false;
            }
        }
    }

    return false;
}

//
// Function to create SDL checklist jira ticket
//
function _sdl_create_jira_checklist_ticket($epicId, $risk_rating, $list_of_modules, $project_name, $jira_components, $isEpic)
{

    $project_risk = _sdl_determineRiskValue($risk_rating);


    $our_modules = array_intersect(_sdl_valid_choosable_modules(), $list_of_modules);
    $lists = [];

    foreach ($our_modules as $filename) {
        if (!in_array($filename, _sdl_valid_modules())) {
            continue; //skip for the submodule identifiers
        }
        $parsed = json_decode(file_get_contents($filename), true);
        $category = (isset($parsed['category'])) ? $parsed['category'] : 'General';
        if (!isset($lists[$category])) {
            $lists[$category] = [];
        }

        $infoobj = [
            'title' => $parsed['title'],
            'description' => $parsed['description'],
            'minimum_risk_required' => $parsed['minimum_risk_required'],
            'lists' => _sdl_get_lists_from_sdl($parsed['questions']),
        ];
        $submodules = [];

        if (isset($parsed['submodules'])) {
            foreach ($parsed['submodules'] as $submod) {
                $infoobj2 = [
                'filename' => $filename.md5($filename.$submod['title']),
                'title' => $submod['title'],
                'description' => $submod['description'],
                'minimum_risk_required' => $submod['minimum_risk_required'],
                'lists' => _sdl_get_lists_from_sdl($submod['questions']),
                ];

                if (in_array($infoobj2['filename'], $our_modules)) {
                    $submodules[] = $infoobj2;
                }
            }
        }

        $lists[$category][] = $infoobj;
        foreach ($submodules as $submod) {
            $lists[$category][] = $submod;
        }
    }

    //
    // Normal Editor
    //
    $category_html = '{panel:title=Ticket Instructions|bgColor=#FFFFCE}
		Please complete the checklist below in the "*Checklists*" tab.
		Mark the items to complete the checklist. Once all of the checklist is completed then move this ticket\'s status to "*Done*" and enter resolution "*Done*" 
		If you end up over-scoping and have too many checklist, and you end up not needing it, you can mark the item as "*Not Applicable*" by putting the "*N/A*" status to the checklist items.
		If you have question completing a specific checklist, feel free to reach out to security team for any help or pointers.

		Your initial risk assessment questionnaire came back with a rating of *'.$risk_rating.'*.
		Due to this risk rating, you must complete the items that are tagged with a star (*).
		{panel}

		h1. Component Selected'."\n";
    // Create HTML
    foreach ($lists as $category => $cards) {
        $category_html = $category_html.'*'.$category.'*';
        $category_html = $category_html."\n";

        $card_html = '';
        foreach ($cards as $card) {
            // Add label to cards depeding on risk level
            if (isset($card['minimum_risk_required'])) {
                $card_risk = _sdl_determineRiskValue($card['minimum_risk_required']);

                if ($project_risk >= $card_risk) {
                    $card_html = $card_html.'* *'.$card['title'].'* (*)'."\n";
                } else {
                    $card_html = $card_html.'* *'.$card['title'].'*'."\n";
                }
            }

            $card_html = $card_html.$card['description']."\n\n";
        }

        $category_html = $category_html.$card_html;
    }

    if ($isEpic) {
        $data = [
            'fields' => [
                'project' => [
                    'key' => getenv('JIRA_PROJECT'),
                ],
                'summary' => 'SDL Checklist - '.$project_name,
                'description' => $category_html,
                'assignee' => [
                    'name' => 'Unassigned',
                ],
                'issuetype' => [
                    'name' => 'Task',
                ],
                'reporter' => [
                    'id' => '5d5cf5497b9a8f0cf55fb5f3',
                ],
                'components' => [
                    [
                        'name' => $jira_components,
                    ],
                ],
            ],
        ];
    } else {
        $data = [
            'fields' => [
                'project' => [
                    'key' => getenv('JIRA_PROJECT'),
                ],
                'summary' => 'SDL Checklist - '.$project_name,
                'description' => $category_html,
                'issuetype' => [
                    'name' => 'Task',
                ],
                'reporter' => [
                    'id' => '5d5cf5497b9a8f0cf55fb5f3',
                ],
                'components' => [
                    [
                        'name' => $jira_components,
                    ],
                ],
            ],
        ];
    }

    $ret = jira_new_issue_create($data);
    if (!$ret['ok']) {
        return $ret;
    }
    $response = $ret['body'];
    $response['list'] = $lists;

    return  [
        'ok' => true,
        'response' => $response,
    ];
}

//
// Risk level to integer for minimum risk requirement comparison
//
function _sdl_determineRiskValue($risk_rating)
{
    $normalize = trim(mb_strtolower($risk_rating));
    if ($normalize === 'low risk') {
        return 1;
    }
    if ($normalize === 'medium risk') {
        return 2;
    }
    if ($normalize === 'high risk') {
        return 3;
    }

    return 4;
}


//
// Retrive questions list from metadata
//
function _sdl_get_lists_from_sdl($parsed_questions)
{
    $lists = [];
    foreach ($parsed_questions as $question_cat => $question_arr) {
        if (!array_key_exists($question_cat, $lists)) {
            $lists[$question_cat] = [];
        }

        foreach ($question_arr as $question) {
            if (is_string($question)) {
                $lists[$question_cat][] = $question;
            } else {
                // Question is an object (with explanation)
                $text = $question['text'];
                if ($question['explanation']) {
                    $text .= " ({$question['explanation']})";
                }
                $lists[$question_cat][] = $text;
            }
        }
    }

    return $lists;
}


//
// Populate the checklist item with the selected questions
//
function _sdl_populate_checklist($jiraChecklist, $risk_rating)
{
    $issue_id = $jiraChecklist['key'];
    $lists = $jiraChecklist['list'];
    $project_risk = _sdl_determineRiskValue($risk_rating);

    $customfield_id = getenv('JIRA_CHECKLIST_FIELD');
    $checklist_items = '';

    foreach ($lists as $category => $cards) {


        foreach ($cards as $card) {
            //Add label to cards depeding on risk level
            if (isset($card['minimum_risk_required'])) {
                $card_risk = _sdl_determineRiskValue($card['minimum_risk_required']);
            }

            if (isset($card['lists'])) {
                foreach ($card['lists'] as $questiongroup => $questionlist) {
                    $checklist_items .= '## '.$card['title']."\n";
                    foreach ($questionlist as $questioncheckbox) {
                        if ($project_risk >= $card_risk) {
                            $checklist_items .= '- '.$questioncheckbox."\n";
                        } else {
                            $checklist_items .= '+ '.$questioncheckbox."\n";
                        }
                    }
                    $checklist_items .= "\n";
                }
            }
        }

    }

    // Call REST API
    return jira_new_issue_add_checklistitem($issue_id, $customfield_id, trim($checklist_items));
}

//
// Function to create jira ticket using the jira API for Prodsec team to review the SDL process
//
function _sdl_create_jira_ticket_prodsec($input, $jiraChecklistId)
{
    $project_name = $input['project_name']['value'];
    $risk_rating = $input['risk_rating']['value'];

    // Create text info blob for jira
    $info_blob = "Information Gathering \n";
    $info_blob = $info_blob."========================== \n";

    foreach ($input as $key => $value) {
        if (is_array($value) && isset($value['value'])) {
            $info_blob = $info_blob.$value['text'].' : '.$value['value']."\n";
        }
    }

    $selected_tags = 'Selected Components: ';
    for ($i = 0; $i < count($input['tags']); ++$i) {
        if ($i == 0) {
            $selected_tags = $selected_tags.$input['tags'][$i];
        } else {
            $selected_tags = $selected_tags.', '.$input['tags'][$i];
        }
    }

    $info_blob = $info_blob.$selected_tags." \n" ;

    $info_blob = $info_blob."\nRisk Assessment Responses \n";
    $info_blob = $info_blob."========================== \n";
    foreach ($input['riskassessment'] as $value) {
        $info_blob = $info_blob.$value['text']." \n".$value['response']."\n\n";
    }

    $data = [
        'fields' => [
            'project' => [
                'key' => 'PRODSEC',
            ],
            'labels' => ['sdl'],
            'components' => [
                [
                    'name' => 'Identification',
                ],
            ],
            'summary' => $risk_rating.': Review: '.$project_name,
            'description' => "Review for {$project_name}\nJIRA SDL Checklist at : {$jiraChecklistId}\n\n{$info_blob}",
            'issuetype' => [
                'name' => 'Task',
            ],
        ],
    ];

    $ret = jira_new_issue_create($data);
    if (!$ret['ok']) {
        return $ret;
    }

    $response = $ret['body'];

    return  [
        'ok' => true,
        'response' => $response,
    ];
}
