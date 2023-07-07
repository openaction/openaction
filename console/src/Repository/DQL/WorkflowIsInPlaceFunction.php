<?php

namespace App\Repository\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class WorkflowIsInPlaceFunction extends FunctionNode
{
    private PathExpression $workflowFieldName;
    private Literal $placeName;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return '('.$this->workflowFieldName->dispatch($sqlWalker).'->>\''.$this->placeName->value.'\' IS NOT NULL)';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->workflowFieldName = $parser->StateFieldPathExpression();

        $parser->match(Lexer::T_COMMA);

        $this->placeName = $parser->Literal();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
