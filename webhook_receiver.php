<?php

require __DIR__ . '/vendor/autoload.php';

// Tests
use App\Webhooks\WebhookHandlerFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

// create a log channel
$log = new Logger('Receiver');
$log->pushHandler(new StreamHandler('log/webhook.log', Level::Info));

$payloadRAW = file_get_contents('php://input');
//$payloadRAW = '{"object_kind":"merge_request","event_type":"merge_request","user":{"id":18648803,"name":"Vladislav Dimitrov","username":"vladislav.dimitrov1","avatar_url":"https://secure.gravatar.com/avatar/45828d279fc3a79b63127889ed291a3a?s=80&amp;d=identicon","email":"[REDACTED]"},"project":{"id":52034094,"name":"BlankGround","description":null,"web_url":"https://gitlab.com/testground3/blankground","avatar_url":null,"git_ssh_url":"git@gitlab.com:testground3/blankground.git","git_http_url":"https://gitlab.com/testground3/blankground.git","namespace":"TestGround","visibility_level":0,"path_with_namespace":"testground3/blankground","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/testground3/blankground","url":"git@gitlab.com:testground3/blankground.git","ssh_url":"git@gitlab.com:testground3/blankground.git","http_url":"https://gitlab.com/testground3/blankground.git"},"object_attributes":{"assignee_id":null,"author_id":18648803,"created_at":"2023-11-10T12:01:25.690Z","description":"","draft":false,"head_pipeline_id":1068055236,"id":263013551,"iid":3,"last_edited_at":"2023-11-13T14:14:00.827Z","last_edited_by_id":18648803,"merge_commit_sha":null,"merge_error":null,"merge_params":{"force_remove_source_branch":"1"},"merge_status":"unchecked","merge_user_id":null,"merge_when_pipeline_succeeds":false,"milestone_id":null,"source_branch":"improvement/make-app-no-longer-awesome","source_project_id":52034094,"state_id":1,"target_branch":"main","target_project_id":52034094,"time_estimate":0,"title":"Make App no-longer awesome","updated_at":"2023-11-13T14:14:00.837Z","updated_by_id":18648803,"url":"https://gitlab.com/testground3/blankground/-/merge_requests/3","source":{"id":52034094,"name":"BlankGround","description":null,"web_url":"https://gitlab.com/testground3/blankground","avatar_url":null,"git_ssh_url":"git@gitlab.com:testground3/blankground.git","git_http_url":"https://gitlab.com/testground3/blankground.git","namespace":"TestGround","visibility_level":0,"path_with_namespace":"testground3/blankground","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/testground3/blankground","url":"git@gitlab.com:testground3/blankground.git","ssh_url":"git@gitlab.com:testground3/blankground.git","http_url":"https://gitlab.com/testground3/blankground.git"},"target":{"id":52034094,"name":"BlankGround","description":null,"web_url":"https://gitlab.com/testground3/blankground","avatar_url":null,"git_ssh_url":"git@gitlab.com:testground3/blankground.git","git_http_url":"https://gitlab.com/testground3/blankground.git","namespace":"TestGround","visibility_level":0,"path_with_namespace":"testground3/blankground","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/testground3/blankground","url":"git@gitlab.com:testground3/blankground.git","ssh_url":"git@gitlab.com:testground3/blankground.git","http_url":"https://gitlab.com/testground3/blankground.git"},"last_commit":{"id":"701ebd015b9f72a496b197adc4c151c7133f1f89","message":"Make App no-longer awesome\n","title":"Make App no-longer awesome","timestamp":"2023-11-10T14:01:12+02:00","url":"https://gitlab.com/testground3/blankground/-/commit/701ebd015b9f72a496b197adc4c151c7133f1f89","author":{"name":"Vladislav Dimitrov","email":"vladislav.dimitrov@siteground.com"}},"work_in_progress":false,"total_time_spent":0,"time_change":0,"human_total_time_spent":null,"human_time_change":null,"human_time_estimate":null,"assignee_ids":[],"reviewer_ids":[],"labels":[],"state":"opened","blocking_discussions_resolved":true,"first_contribution":true,"detailed_merge_status":"unchecked"},"labels":[],"changes":{},"repository":{"name":"BlankGround","url":"git@gitlab.com:testground3/blankground.git","description":null,"homepage":"https://gitlab.com/testground3/blankground"}}';
$payload = json_decode($payloadRAW, true);

$log->info($payloadRAW);

$webhookHandler = WebhookHandlerFactory::getHandler($payload);
$log->info($webhookHandler::class);

$webhookHandler->handle($payload);

$log->info('Done!');
