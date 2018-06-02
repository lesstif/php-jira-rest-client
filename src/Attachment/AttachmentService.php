<?php

namespace JiraRestApi\Attachment;

use JiraRestApi\Issue\Attachment;
use JiraRestApi\JiraClient;

/**
 * Class AttachmentService
 *
 * @package JiraRestApi\Group
 */
class AttachmentService extends \JiraRestApi\JiraClient
{
    private $uri = '/attachment';

    /**
     * Returns the meta-data for an attachment, including the URI of the actual attached file.
     *
     * @param $id
     * @return \JiraRestApi\Issue\Attachment
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function get($id)
    {
        $ret = $this->exec($this->uri.'/'.$id, null);

        $this->log->addInfo("Result=\n".$ret);

        return $this->json_mapper->map(
                json_decode($ret), new Attachment()
        );
    }

    /**
     * Remove an attachment from an issue.
     *
     * @param $id
     * @return string
     * @throws \JiraRestApi\JiraException
     */
    public function remove($id)
    {
        $ret = $this->exec($this->uri.'/'.$id, null, 'DELETE');

        $this->log->addInfo("Result=\n".$ret);

        return $ret;
    }
}
