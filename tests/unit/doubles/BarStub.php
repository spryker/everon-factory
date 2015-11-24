<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles;


class BarStub
{
    use Dependency\Gizz;


    /**
     * @var string
     */
    protected $anotherArgument;

    /**
     * @var array
     */
    protected $data = [];


    /**
     * BarStub constructor.
     */
    public function __construct(GizzStub $GizStub, $anotherArgument = 'anotherArgument', array $data=[])
    {
        $this->Gizz = $GizStub;
        $this->anotherArgument = $anotherArgument;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAnotherArgument()
    {
        return $this->anotherArgument;
    }

    /**
     * @param string $anotherArgument
     */
    public function setAnotherArgument($anotherArgument)
    {
        $this->anotherArgument = $anotherArgument;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


}
