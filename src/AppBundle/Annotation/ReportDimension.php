<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * ReportDimension
 *
 * @Annotation
 * @Target("CLASS")
 */
class ReportDimension
{
    /**
     * @var string
     */
    public $name;

}