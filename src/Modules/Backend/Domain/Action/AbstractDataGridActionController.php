<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

abstract class AbstractDataGridActionController extends AbstractActionController
{
    public function renderDataGrid(
        string $entityFullyQualifiedClassName,
        ?callable $queryBuilderCallback = null,
    ): void {
        $this->assign(
            'backend_data_grid',
            $this->dataGridFactory->forEntity($entityFullyQualifiedClassName, $queryBuilderCallback)
        );
    }
}
