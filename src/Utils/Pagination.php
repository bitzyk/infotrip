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

    private $uiDisplayNoOfPages = 11;


    public function getOffset()
    {
        $offset = (int) $this->noResultsPerPage * ($this->noPag-1); // no pag start from 1 -> so offset should be 0 in this case

        if ($offset > $this->noResults) {
            $offset = $this->noResults - $this->noResultsPerPage;
            $this->noPag = (int) ceil($this->noResults/$this->noResultsPerPage);
        }

        if ($offset < 0) {
            $offset = 0;
            $this->noPag = 1;
        }

        $this->offset = (int) $offset;

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
        if($noPag <= 0) {
            $this->noPag = 1;
        } else {
            $this->noPag = (int) $noPag;
        }

        $this->noPag = $noPag;
    }

    /**
     * @return int
     */
    public function getNoResultsPerPage()
    {
        return $this->noResultsPerPage;
    }

    public function getUIHelperInfo()
    {
        $maxNoPag = ceil($this->noResults/$this->noResultsPerPage);

        $startIndex = $this->noPag - floor($this->uiDisplayNoOfPages/2);

        if ($startIndex < 1)  {
            $startIndex = 1;
        }

        $finalIndex = $this->noPag + floor($this->uiDisplayNoOfPages/2);

        if ($finalIndex > $maxNoPag) {
            $finalIndex = $maxNoPag;
        } elseif (
            $finalIndex < $this->uiDisplayNoOfPages &&
            $maxNoPag > $this->uiDisplayNoOfPages
        ) {
            $finalIndex = $this->uiDisplayNoOfPages - 1;
        }

        return [
            'startIndex' => $startIndex,
            'currentIndex' => $this->noPag,
            'finalIndex' => $finalIndex,
        ];
    }

}