<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use DOMDocument;
use DOMElement;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Export the template of a theme with their positions and the default blocks.
 */
final class ThemeTemplateExport extends AbstractActionController
{
    public function __construct(
        ActionServices $services,
        private readonly SerializerInterface $serializer,
    ) {
        parent::__construct($services);
    }

    protected function execute(Request $request): void
    {
    }

    public function getResponse(Request $request): Response
    {
        $theme = $this->getEntityFromRequest($request, Theme::class);
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $templatesXml = $xml->createElement('templates');
        $xml->appendChild($templatesXml);
        /** @var BlockRepository $blockRepository */
        $blockRepository = $this->getRepository(Block::class);
        foreach ($theme->getTemplates() as $template) {
            $templateXml = $xml->createElement('template');
            $templateXml->setAttribute('name', $template->getName());
            $templateXml->setAttribute('path', $template->getPath());
            if ($template->isDefault()) {
                $templateXml->setAttribute('default', 'true');
            }
            $templatesXml->appendChild($templateXml);
            $templateXml->appendChild(
                $xml->createElement(
                    'layout',
                    "\n      " . str_replace("\n", "\n      ", $template->getSetting('layout')) . "\n    "
                )
            );
            $positions = $template->getSetting('positions', []);
            $positionsXml = $xml->createElement('positions');
            $templateXml->appendChild($positionsXml);
            foreach ($positions as $position) {
                $positionXml = $xml->createElement('position');
                $positionXml->setAttribute('name', $position['name']);
                foreach ($position['blocks'] ?? [] as $blockId) {
                    $block = $blockRepository->find($blockId);
                    if ($block === null) {
                        continue;
                    }
                    $blockDOMDocument = new DOMDocument('1.0', 'utf-8');
                    $blockDOMDocument->loadXML(
                        $this->serializer->serialize(
                            $block->getSettings()->all(),
                            'xml',
                            [
                                'xml_root_node_name' => 'block'
                            ]
                        )
                    );
                    /** @var DOMElement $blockXml */
                    $blockXml = $xml->importNode($blockDOMDocument->documentElement, true);
                    $blockXml->setAttribute('module', $block->getBlock()->getModule()->getName());
                    $blockXml->setAttribute('type', $block->getType()->value);
                    $blockXml->setAttribute('name', $block->getBlock()->getName());
                    $blockXml->setAttribute('label', $block->getLabel()->getName());
                    $positionXml->append($blockXml);
                }
                $positionsXml->appendChild($positionXml);
            }
        }

        return new Response(
            $xml->saveXML(),
            Response::HTTP_OK,
            [
                'Content-type' => 'text/xml',
                'Content-disposition' => 'attachment; filename="templates_' . gmdate('Y-m-d', null) . '.xml"',
            ]
        );
    }
}
