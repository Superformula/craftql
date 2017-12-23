<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;
use markhuot\CraftQL\Builders\Schema;

class GetTagsFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle(GetFieldSchema $event) {
        $event->handled = true;

        $field = $event->sender;
        $query = $event->query;

        if (!$query->getRequest()->token()->can('query:tags')) {
            return;
        }

        $source = $field->settings['source'];
        if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
            $groupId = $matches[1];

            $query->addField($field)
                ->lists()
                ->type($query->getRequest()->tagGroups()->get($groupId))
                ->resolve(function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                });

            $event->mutation->addIntArgument($field)
                ->lists();
        }
    }
}
