<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 17/09/2018
 * Time: 19:01
 */

namespace Infotrip\Utils;


class Pagination
{
    /**
     * @var int
     */
    private $noResults = 0;

    private $noResultsPerPage = 20;

    private $offset = 0;

    /**
     * @var int
     */
    private $noPag = 0;


    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $noResults
     */
    public function setNoResults($noResults)
    {
        $this->noResults = $noResults;
    }

    /**
     * @param int $noPag
     */
    public function setNoPag($noPag)
    {
        $this->noPag = $noPag;
    }




}