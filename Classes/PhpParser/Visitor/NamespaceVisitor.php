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
 * The TYPO3 project - inspiring people tos share!
 */

namespace HellYeah\Spawn\PhpParser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class NamespaceVisitor extends NodeVisitorAbstract
{
    private Node\Name $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = new Node\Name($namespace);
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $node->name = $this->namespace;
        }

        return $node;
    }

}
