<?php
namespace ZenDesk;

abstract class Snippet
{
    /** @var \MODX\Revolution\modX $modx */
    public $modx;

    /** @var \Zendesk */
    protected $zendesk;

    public $scriptProperties;

    /**
     * Endpoint constructor.
     * @param \Zendesk $zendesk
     */
    public function __construct(\Zendesk &$zendesk, array $scriptProperties = [])
    {
        $this->zendesk =& $zendesk;
        $this->modx =& $this->zendesk->modx;
        $this->scriptProperties = $scriptProperties;
    }

    abstract public function run();

    protected function getChunk($tpl, $phs = [])
    {
        if (strpos($tpl, '@INLINE ') !== false) {
            $content = str_replace('@INLINE ', '', $tpl);

            /** @var \modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid('', true)));
            $chunk->setCacheable(false);

            return $chunk->process($phs, $content);
        }

        return $this->modx->getChunk($tpl, $phs);
    }
}
