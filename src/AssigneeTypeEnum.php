<?php

namespace JiraRestApi;

enum AssigneeTypeEnum
{
    case PROJECT_LEAD;
    case COMPONENT_LEAD;
    case UNASSIGNED;

    public function type(): string
    {
        return match ($this) {
            AssigneeTypeEnum::PROJECT_LEAD   => 'PROJECT_LEAD',
            AssigneeTypeEnum::COMPONENT_LEAD => 'COMPONENT_LEAD',
            AssigneeTypeEnum::UNASSIGNED     => 'UNASSIGNED',
        };
    }
}
