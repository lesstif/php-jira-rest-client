<?php

namespace JiraRestApi\Issue;

class RemoteIssueLinkObject
{
    /** @var string */
    public $url;

    /** @var string */
    public $title;

    /** @var string|null */
    public $summary;

    /** @var array|null */
    public $icon;

    /**
     * @var array|null
     *
     * ```json
     * "status": {
     *      "resolved": true,
     *      "icon": {
     *          "url16x16": "http://www.mycompany.com/support/resolved.png",
     *          "title": "Case Closed",
     *          "link": "http://www.mycompany.com/support?id=1&details=closed"
     *      }
     *  }
     * ```
     */
    public $status;
}
