<?php
/**
 * @var modX $modx
 * @var array $scriptProperties
 */
$zendesk = $modx->getService('zendesk', 'Zendesk', $modx->getOption('zendesk.core_path', null, $modx->getOption('core_path') . 'components/zendesk/') . 'model/zendesk/');
if (!($zendesk instanceof \Zendesk)) {
    return '';
}

return (new \Zendesk\Snippet\PostHook($zendesk, $scriptProperties))->run();
