<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace HellYeah\Spawn\Service;

use HellYeah\Spawn\PhpParser\Visitor\AttributeNameVisitor;
use HellYeah\Spawn\PhpParser\Visitor\ClassnameVisitor;
use HellYeah\Spawn\PhpParser\Visitor\NamespaceVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class PhpParserService
{
    private Parser $parser;
    private array $abstractSyntaxTree = [];
    private array $originalAbstractSyntaxTree = [];
    private array $originalTokens = [];
    private array $visitors = [];

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForHostVersion();
    }

    public function setCode(string $code): PhpParserService
    {
        $this->abstractSyntaxTree = $this->parser->parse($code);
        $this->originalAbstractSyntaxTree = $this->abstractSyntaxTree;
        $this->originalTokens = $this->parser->getTokens();

        return $this;
    }

    public function setNamespace(string $namespace): PhpParserService
    {
        $this->visitors[] = new NamespaceVisitor($namespace);

        return $this;
    }

    public function setCommandAttribute(string $name, string $description): PhpParserService
    {
        $this->visitors[] = new AttributeNameVisitor($name, $description);

        return $this;
    }

    public function setClassname(string $classname): PhpParserService
    {
        $this->visitors[] = new ClassnameVisitor($classname);

        return $this;
    }

    public function getPhpCode(): string
    {
        $printer = new Standard();
        $cloningTraverser = new NodeTraverser(new CloningVisitor());
        $modifierTraverser = new NodeTraverser();

        foreach ($this->visitors as $visitor) {
            $modifierTraverser->addVisitor($visitor);
        }

        $this->abstractSyntaxTree = $cloningTraverser->traverse($this->abstractSyntaxTree);
        $modifierTraverser->traverse($this->abstractSyntaxTree);

        return $printer->printFormatPreserving(
            $this->abstractSyntaxTree,
            $this->originalAbstractSyntaxTree,
            $this->originalTokens
        );
    }
}
