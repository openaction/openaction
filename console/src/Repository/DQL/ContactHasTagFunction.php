<?php

namespace App\Repository\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class ContactHasTagFunction extends FunctionNode
{
    private PathExpression $contactAlias;
    private Literal $tagId;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return '(
            SELECT COUNT(tag_id) > 0 AS has_tag
            FROM community_contacts_tags
            WHERE contact_id = '.$this->contactAlias->dispatch($sqlWalker).'
            AND tag_id = '.$this->tagId->value.'
        )';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->contactAlias = $parser->StateFieldPathExpression();

        $parser->match(Lexer::T_COMMA);

        $this->tagId = $parser->Literal();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
