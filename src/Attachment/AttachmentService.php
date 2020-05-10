<?php

namespace JiraRestApi\Attachment;

use JiraRestApi\Issue\Attachment;

/**
 * Class AttachmentService.
 */
class AttachmentService extends \JiraRestApi\JiraClient
{
    private $uri = '/attachment/';

    /**
     * Returns the meta-data for an attachment, including the URI of the actual attached file.
     *
     * @param string|int $id attachment Id
     * @param string|null $outDir string downloads the content and store into outDir
     * @param bool $overwrite   boolean determines whether to overwrite the file if it already exists.
     * @param int $mode creation mode.
     * @param bool $recursive   Allows the creation of nested directories specified in the pathname.
     *
     * @return \JiraRestApi\Issue\Attachment
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function get($id, string $outDir = null, bool $overwrite = false, int $mode = 0777, bool $recursive = true)
    {
        $ret = $this->exec($this->uri.$id, null);

        $this->log->info("Result=\n".$ret);

        $attachment = $this->json_mapper->map(
            json_decode($ret),
            new Attachment()
        );

        if ($outDir == null) {
            return $attachment;
        }

        // download contents
        if (!file_exists($outDir)) {
            mkdir($outDir, $mode, $recursive);
        }

        // extract filename
        $file = substr(strrchr($attachment->content, '/'), 1);

        if (file_exists($outDir.DIRECTORY_SEPARATOR.$file) && $overwrite == false) {
            return $attachment;
        }

        $this->download($attachment->content, $outDir, $file);

        return $attachment;
    }

    /**
     * Remove an attachment from an issue.
     *
     * @param string|int $id attachment id
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return bool
     */
    public function remove($id)
    {
        $ret = $this->exec($this->uri.$id, null, 'DELETE');

        $this->log->info("Result=\n".$ret);

        return $ret;
    }
}
