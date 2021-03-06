<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
return [
    'inputXML' => '<?xml version="1.0" encoding="UTF-8"?><config>' .
    '<indexer id="indexer_internal_name" view_id="view01" class="Index\Class\Name">' .
    '<title translate="true">' .
    'Indexer public name</title><description translate="true">Indexer public description</description>' .
    '</indexer></config>',
    'expected' => [
        'indexer_internal_name' => [
            'indexer_id' => 'indexer_internal_name',
            'view_id' => 'view01',
            'action_class' => 'Index\Class\Name',
            'title' => 'Indexer public name',
            'description' => 'Indexer public description',
        ],
    ]
];
