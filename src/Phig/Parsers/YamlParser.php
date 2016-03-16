<?php

namespace Phig\Parsers;

use Exception;
use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;
use Symfony\Component\Yaml\Parser;

class YamlParser implements ParserInterface
{
    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $parser;

    /**
     * Parse an YAML file using Symfony Yaml parser.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return mixed
     */
    public function parse($path)
    {
        $this->checkParser();

        try {
            return $this->parser->parse(file_get_contents($path));
        } catch (Exception $e) {
            throw new ParserException($path, ParserException::YAML, $e);
        }
    }

    /**
     * Set parser that will be used to parse YAML file contents.
     *
     * @param \Symfony\Component\Yaml\Parser $parser
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get parser that will be used to parse YAML file contents.
     *
     * @return \Symfony\Component\Yaml\Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set parser to Symfony Yaml parser if not set.
     */
    protected function checkParser()
    {
        if (is_null($this->getParser())) {
            $this->setParser(new Parser());
        }
    }
}
