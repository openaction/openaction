<?php

namespace App\Community\ImportExport;

use App\Entity\Website\Form;
use App\Repository\Website\FormAnswerRepository;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class FormAnswerExporter
{
    private FormAnswerRepository $repository;

    public function __construct(FormAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function export(Form $form)
    {
        $writer = new Writer();
        $writer->openToFile('php://output');

        $head = ['id', 'contact_id', 'contact_email', 'created_at'];
        foreach ($form->getBlocks() as $block) {
            if ($block->isField()) {
                $head[] = $block->getContent();
            }
        }

        $writer->addRow(Row::fromValues($head));

        foreach ($this->repository->getExportData($form) as $contact) {
            $writer->addRow(Row::fromValues($contact));
        }

        $writer->close();
    }
}
