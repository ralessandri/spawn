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
use PhpParser\NodeVisitorAbstract;

class AttributeNameVisitor extends NodeVisitorAbstract
{
    private string $attributeName;
    private string $attributeDescription;

    public function __construct(string $attributeName, string $attributeDescription)
    {
        $this->attributeName = $attributeName;
        $this->attributeDescription = $attributeDescription;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            foreach ($node->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    if ($attr->name->toString() === 'AsCommand') {
                        foreach ($attr->args as $arg) {
                            if ($arg->name && $arg->name->toString() === 'name') {
                                $arg->value = new Node\Scalar\String_($this->attributeName);
                            }
                            if ($arg->name && $arg->name->toString() === 'description') {
                                $arg->value = new Node\Scalar\String_($this->attributeDescription);
                            }
                        }
                    }
                }
            }
        }

        return $node;
    }

}
