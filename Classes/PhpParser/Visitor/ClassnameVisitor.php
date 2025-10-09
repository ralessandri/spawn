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

namespace HellYeah\Spawn\PhpParser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

class ClassnameVisitor extends NodeVisitorAbstract
{
    private Node\Identifier $classname;

    public function __construct(string $classname)
    {
        $this->classname = new Node\Identifier($classname);
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Class_) {
            $node->name = $this->classname;
        }

        return $node;
    }

}
