<?php declare(strict_types=1);

namespace App\Webhooks;

use DateTime;
use Gitlab\Api\MergeRequests;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class PushWebhookHandler extends AbstractWebhookHandler
{
    protected const MONITORED_BRANCH = 'main';

    protected function handleSpecific(array $payload): void
    {
        if (!$this->isValidTarget($payload['ref'])) {
            return;
        }

        if ($this->isPartOfMerge($payload)) {
            return;
        }

        $this->sendNotification($payload['before'], $payload['after']);
    }

    protected function isValidTarget(string $targetBranch): bool
    {
        $this->logger->info('Checking target branch...', [$targetBranch]);
        return $targetBranch === 'refs/heads/'.self::MONITORED_BRANCH;
    }

    public function isPartOfMerge(array $payload): bool
    {
        $lastCommitData = $payload['commits'][0];

        $authored = new DateTime($lastCommitData['timestamp']);
        // This is only valid for webhook commits, otherwise we need to check the merged date as well
        $updated_before = clone $authored;
        $updated_before->modify('+3 seconds');

        $mergeRequests = $this->client->mergeRequests()->all($this->projectData['id'], [
            'target_branch' => self::MONITORED_BRANCH,
            'state' => MergeRequests::STATE_MERGED,
            'updated_after' => $authored,
            'updated_before' => $updated_before,
        ]);

        $this->logger->info('Searching MRs for commit...', [$payload['checkout_sha']]);
        foreach ($mergeRequests as $mergeRequest) {
            $this->logger->info('Merge request squash_commit_sha.', [$mergeRequest['squash_commit_sha']]);
            if ($payload['checkout_sha'] == $mergeRequest['squash_commit_sha']) {
                $this->logger->info('This commit is part of a Merge Request.');
                return true;
            }
        }

        $this->logger->info('Commit not found in any Merge Request.');
        return false;
    }

    protected function sendNotification(string $beforeCommitHash, string $afterCommitHash): void
    {
        $this->logger->info('Sending notification!');
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__.'/../../log/notifications.log', Level::Alert));
        $log->alert('Someone pushed a bad commit!', [
            'before' => $beforeCommitHash,
            'after' => $afterCommitHash,
            'compare' => $this->projectData['web_url']. "/-/compare/$beforeCommitHash...$afterCommitHash?from_project_id={$this->projectData['id']}&straight=false"
        ]);
        $this->logger->info('Notification sent!');
    }
}
