<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Markup;

/**
 * Pipeline class for parsers.
 *
 * @author Sergii Smirnov <free.smilesrg@gmail.com>
 */
class PipelineParser implements ParserInterface
{
    /** @var ParserInterface[] */
    private $pipeline = array();

    /**
     * Adds a parser to pipeline.
     *
     * @param ParserInterface $parser
     */
    public function addToPipeline(ParserInterface $parser)
    {
        $this->pipeline[] = $parser;
    }

    /**
     * Parses comment with all parsers in pipeline.
     *
     * @param string $raw comment to be parsed
     *
     * @return string comment that has been parsed with all parsers in pipeline
     */
    public function parse($raw)
    {
        foreach ($this->pipeline as $parser) {
            $raw = $parser->parse($raw);
        }

        return $raw;
    }
}
