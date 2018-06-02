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
    private $uri = '/attachment/';

    /**
     * Returns the meta-data for an attachment, including the URI of the actual attached file.
     *
     * @param $id string|int attachment Id
     * @outDir string downloads the content and store into outDir
     * @overwrite boolean determines whether to overwrite the file if it already exists.
     *
     * @return \JiraRestApi\Issue\Attachment
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function get($id, $outDir = null, $overwrite = false)
    {
        $ret = $this->exec($this->uri.$id, null);

        $this->log->addInfo("Result=\n".$ret);

        $attachment = $this->json_mapper->map(
                json_decode($ret), new Attachment()
        );

        if ($outDir == null) {
            return $attachment;
        }

        // download contents
        if (! file_exists($outDir)) {
            mkdir($outDir);
        }

        // extract filename
        $file = substr(strrchr($attachment->content, '/'), 1);

        if (file_exists($outDir.DIRECTORY_SEPARATOR.$file) && $overwrite == false) {
            return $attachment;
        }

        $this->download($attachment->content, $outDir, $file);
    }

    /**
     * Remove an attachment from an issue.
     *
     * @param $id string|int attachment id
     * @return boolean
     *
     * @throws \JiraRestApi\JiraException
     */
    public function remove($id)
    {
        $ret = $this->exec($this->uri.$id, null, 'DELETE');

        $this->log->addInfo("Result=\n".$ret);

        return $ret;
    }
}
