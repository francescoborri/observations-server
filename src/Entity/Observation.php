<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Observation
 *
 * @ORM\Table(name="observation")
 * @ORM\Entity
 */
class Observation
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="datetime", type="string", nullable=false)
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     * @Assert\DateTime
     */
    private $datetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aTemp", type="decimal", precision=4, scale=2, nullable=true)
     */
    private $aTemp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="aHum", type="integer", nullable=true)
     */
    private $aHum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bTemp", type="decimal", precision=4, scale=2, nullable=true)
     */
    private $bTemp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="bHum", type="integer", nullable=true)
     */
    private $bHum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="extTemp", type="decimal", precision=4, scale=2, nullable=true)
     */
    private $extTemp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="extHum", type="integer", nullable=true)
     */
    private $extHum;

    public function getDatetime(): string
    {
        return $this->datetime;
    }

    public function getATemp(): ?string
    {
        return $this->aTemp;
    }

    public function setATemp(?string $aTemp): self
    {
        $this->aTemp = $aTemp;

        return $this;
    }

    public function getAHum(): ?int
    {
        return $this->aHum;
    }

    public function setAHum(?int $aHum): self
    {
        $this->aHum = $aHum;

        return $this;
    }

    public function getBTemp(): ?string
    {
        return $this->bTemp;
    }

    public function setBTemp(?string $bTemp): self
    {
        $this->bTemp = $bTemp;

        return $this;
    }

    public function getBHum(): ?int
    {
        return $this->bHum;
    }

    public function setBHum(?int $bHum): self
    {
        $this->bHum = $bHum;

        return $this;
    }

    public function getExtTemp(): ?string
    {
        return $this->extTemp;
    }

    public function setExtTemp(?string $extTemp): self
    {
        $this->extTemp = $extTemp;

        return $this;
    }

    public function getExtHum(): ?int
    {
        return $this->extHum;
    }

    public function setExtHum(?int $extHum): self
    {
        $this->extHum = $extHum;

        return $this;
    }


}
