<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * ReportFilter
 *
 * @Annotation
 * @Target({"PROPERTY","ANNOTATION"})
 */
class ReportFilter
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;
}