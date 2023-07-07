<?php

namespace App\Community\ImportExport;

use App\Entity\Website\Form;
use App\Repository\Website\FormAnswerRepository;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;

class FormAnswerExporter
{
    private FormAnswerRepository $repository;

    public function __construct(FormAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function export(Form $form)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile('php://output');

        $head = ['id', 'contact_id', 'contact_email', 'created_at'];
        foreach ($form->getBlocks() as $block) {
            if ($block->isField()) {
                $head[] = $block->getContent();
            }
        }

        $writer->addRow(WriterEntityFactory::createRowFromArray($head));

        foreach ($this->repository->getExportData($form) as $contact) {
            $writer->addRow(WriterEntityFactory::createRowFromArray($contact));
        }

        $writer->close();
    }
}
