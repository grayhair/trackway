<?php

namespace AppBundle\Controller;

use AppBundle\Annotation\ReportFilter;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ReportController
 *
 * @package AppBundle\Controller
 *
 * @Route("/report")
 */
class ReportController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     *
     * @Method("GET")
     * @Route("/new", name="report_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $metadataFactory = $this->get('doctrine.orm.entity_manager')->getMetadataFactory();
        $annotationReader = new AnnotationReader();

        $data = [];

        /**
         * @var $metadata ClassMetadata
         */
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $reflectionClass = $metadata->getReflectionClass();
            $reportDimension = $annotationReader->getClassAnnotation($reflectionClass, 'AppBundle\\Annotation\\ReportDimension');
            if ($reportDimension) {
                $data[] = [
                    'dimension' => $reportDimension,
                    'filterList' => $this->getReportFiltersByReflectionClass($reflectionClass)
                ];
            }
        }

        return [
            'data' => $data
        ];
    }

    private function getReportFiltersByReflectionClass(\ReflectionClass $reflectionClass)
    {
        $metadataFactory = $this->get('doctrine.orm.entity_manager')->getMetadataFactory();
        $annotationReader = new AnnotationReader();
        $filters = [];

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /**
             * @var $reportFilter ReportFilter
             */
            $reportFilter = $annotationReader->getPropertyAnnotation($reflectionProperty, 'AppBundle\\Annotation\\ReportFilter');
            if ($reportFilter) {
                $subFilters = [];

                if ($reportFilter->type) {
                    try {
                        $filterReflectionClass = $metadataFactory->getMetadataFor($reportFilter->type);
                        $subFilters = $this->getReportFiltersByReflectionClass($filterReflectionClass->getReflectionClass());
                    } catch (\Exception $e) {}
                }

                $filters[] = [
                    'filter' => $reportFilter,
                    'subFilterList' => $subFilters
                ];
            }
        }

        return $filters;
    }
}
