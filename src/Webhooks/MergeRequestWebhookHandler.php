<?php declare(strict_types=1);

namespace App\Webhooks;

use Gitlab\Api\MergeRequests;

class MergeRequestWebhookHandler extends AbstractWebhookHandler
{
    public bool $testing = false;
    protected function handleSpecific(array $payload): void
    {
        if ($this->testing) {
            // Test webhooks do not have this:
            $payload['object_attributes']['action'] ??= 'merge';
        }

        call_user_func_array([$this, $payload['object_attributes']['action']], [$payload['object_attributes']]);
    }

    protected function merge(array $mergeRequestData): void
    {
        $this->logger->info('Merging...', [$mergeRequestData['source_branch']]);
        $this->autoMerge($mergeRequestData['source_branch']);
        $this->logger->info('Finished merging related branches!');
    }

    public function autoMerge(string $branchName): void
    {
        $this->logger->info('Auto merge triggered for branch.', [$branchName]);
        foreach ($this->getOtherProjectIDs() as $projectID) {
            $this->logger->info('Fetching all merge requests for project.', [$projectID]);
            $mergeRequests = $this->client->mergeRequests()->all($projectID, [
                'source_branch' => $branchName,
                'target_branch' => 'main',
                'state' => MergeRequests::STATE_OPENED,
                // 'approved_by_ids' => [1,2], // Premium Feature!
            ]);

            foreach ($mergeRequests as $mergeRequest) {
                // @TODO - rebase MRs before merging - this is an async call
                // $this->client->mergeRequests()->rebase($projectID, $mergeRequest[0]['iid']);

                $this->logger->info('Merging merge request in project.', [$projectID, $mergeRequest['iid']]);
                $this->client->mergeRequests()->merge($projectID, $mergeRequest['iid'], [
                    // 'merge_when_pipeline_succeeds' => true, // Will not merge if there is no pipeline configured!
                    // 'should_remove_source_branch' => true,
                    'squash' => true,
                ]);
            }
        }
    }
}
